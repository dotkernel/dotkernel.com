<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Service;

use JsonException;
use Light\App\Service\GitHubClient;
use Light\App\Service\GitHubClientInterface;
use LightTest\Unit\UnitTest;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;

use function fclose;
use function fsockopen;
use function is_resource;
use function json_decode;
use function proc_close;
use function proc_open;
use function proc_terminate;
use function sprintf;
use function stream_socket_get_name;
use function stream_socket_server;
use function strrchr;
use function substr;
use function usleep;

use const DIRECTORY_SEPARATOR;
use const JSON_THROW_ON_ERROR;
use const PHP_BINARY;

/**
 * Drives the real cURL transport against a local stand-in for the GitHub API.
 *
 * `GitHubClient::absoluteUrl()` passes absolute URLs through untouched, which is the seam that
 * lets these tests point the client at 127.0.0.1 without any production code changing.
 */
class GitHubClientTest extends UnitTest
{
    private const string TOKEN      = 'gh-test-token';
    private const string USER_AGENT = 'dotkernel.com-test';

    /** @var resource|null */
    private static $server;

    private static string $baseUrl = '';

    public static function setUpBeforeClass(): void
    {
        $port    = self::findFreePort();
        $router  = __DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'github-api-server.php';
        $process = proc_open(
            [PHP_BINARY, '-S', sprintf('127.0.0.1:%d', $port), $router],
            [
                0 => ['file', '/dev/null', 'r'],
                1 => ['file', '/dev/null', 'w'],
                2 => ['file', '/dev/null', 'w'],
            ],
            $pipes
        );

        if (! is_resource($process)) {
            self::fail('Unable to start the local GitHub API stand-in.');
        }

        self::$server  = $process;
        self::$baseUrl = sprintf('http://127.0.0.1:%d', $port);

        self::waitForServer($port);
    }

    public static function tearDownAfterClass(): void
    {
        if (is_resource(self::$server)) {
            proc_terminate(self::$server);
            proc_close(self::$server);
        }

        self::$server  = null;
        self::$baseUrl = '';
    }

    public function testGetReturnsTheResponseBody(): void
    {
        $this->assertSame('{"lifecycle":"active"}', $this->createClient()->get($this->url('/ok')));
    }

    public function testGetReturnsNullWhenTheResourceDoesNotExist(): void
    {
        $this->assertNull($this->createClient()->get($this->url('/missing')));
    }

    public function testGetThrowsOnAnUnexpectedStatus(): void
    {
        $url = $this->url('/server-error');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('GitHub returned HTTP 500 for %s.', $url));

        $this->createClient()->get($url);
    }

    public function testGetFollowsRedirects(): void
    {
        $this->assertSame('{"lifecycle":"active"}', $this->createClient()->get($this->url('/redirect')));
    }

    public function testGetThrowsWhenTheTransportFails(): void
    {
        $url = sprintf('http://127.0.0.1:%d/ok', self::findFreePort());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Request to %s failed:', $url));

        $this->createClient()->get($url);
    }

    /**
     * @throws JsonException
     */
    public function testGetSendsTheExpectedRequestHeaders(): void
    {
        $body = $this->createClient()->get($this->url('/echo-request'));
        $this->assertIsString($body);

        $this->assertSame([
            'accept'        => GitHubClientInterface::ACCEPT_JSON,
            'apiVersion'    => '2022-11-28',
            'authorization' => 'Bearer ' . self::TOKEN,
            'userAgent'     => self::USER_AGENT,
        ], json_decode($body, true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @throws JsonException
     */
    public function testGetSendsTheRequestedAcceptHeader(): void
    {
        $body = $this->createClient()->get($this->url('/echo-request'), GitHubClientInterface::ACCEPT_RAW);
        $this->assertIsString($body);

        $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($decoded);
        $this->assertSame(GitHubClientInterface::ACCEPT_RAW, $decoded['accept']);
    }

    /**
     * An unauthenticated client still works, it just has a lower rate limit.
     *
     * @throws JsonException
     */
    public function testGetOmitsTheAuthorizationHeaderWithoutAToken(): void
    {
        $body = $this->createClient('')->get($this->url('/echo-request'));
        $this->assertIsString($body);

        $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($decoded);
        $this->assertSame('', $decoded['authorization']);
    }

    /**
     * cURL rejects an empty user agent and GitHub rejects requests without one.
     *
     * @throws JsonException
     */
    public function testAnEmptyUserAgentFallsBackToTheDefault(): void
    {
        $body = $this->createClient(self::TOKEN, '')->get($this->url('/echo-request'));
        $this->assertIsString($body);

        $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($decoded);
        $this->assertSame('dotkernel.com', $decoded['userAgent']);
    }

    public function testGetAllPagesFollowsNextLinksAndDiscardsNonArrayMembers(): void
    {
        $this->assertSame(
            [['name' => 'one'], ['name' => 'two'], ['name' => 'three']],
            $this->createClient()->getAllPages($this->url('/page-1'))
        );
    }

    public function testGetAllPagesReturnsASinglePageWhenThereIsNoNextLink(): void
    {
        $this->assertSame([['name' => 'three']], $this->createClient()->getAllPages($this->url('/page-2')));
    }

    public function testGetAllPagesThrowsWhenThePayloadIsNotAJsonArray(): void
    {
        $url = $this->url('/not-an-array');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Expected a JSON array from %s.', $url));

        $this->createClient()->getAllPages($url);
    }

    public function testGetAllPagesThrowsOnAnUnexpectedStatus(): void
    {
        $url = $this->url('/server-error');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('GitHub returned HTTP 500 for %s.', $url));

        $this->createClient()->getAllPages($url);
    }

    /**
     * A relative path is resolved against the API root rather than being used verbatim.
     */
    public function testRelativePathsAreResolvedAgainstTheApiRoot(): void
    {
        $client = $this->createClient();

        $this->assertSame(
            'https://api.github.com/orgs/dotkernel/repos',
            $this->invokeAbsoluteUrl($client, '/orgs/dotkernel/repos')
        );
        $this->assertSame(
            'http://example.com/passed-through',
            $this->invokeAbsoluteUrl($client, 'http://example.com/passed-through')
        );
        $this->assertSame(
            'https://example.com/passed-through',
            $this->invokeAbsoluteUrl($client, 'https://example.com/passed-through')
        );
    }

    private function invokeAbsoluteUrl(GitHubClient $client, string $path): string
    {
        $method = new ReflectionMethod($client, 'absoluteUrl');
        $result = $method->invoke($client, $path);
        $this->assertIsString($result);

        return $result;
    }

    public function testTheConfiguredUserAgentIsKept(): void
    {
        $property = new ReflectionProperty(GitHubClient::class, 'userAgent');

        $this->assertSame(self::USER_AGENT, $property->getValue($this->createClient()));
    }

    private function createClient(string $token = self::TOKEN, string $userAgent = self::USER_AGENT): GitHubClient
    {
        return new GitHubClient($token, $userAgent, 10, 5);
    }

    /**
     * @return non-empty-string
     */
    private function url(string $path): string
    {
        /** @var non-empty-string $url */
        $url = self::$baseUrl . $path;
        $this->assertNotSame('', $url);

        return $url;
    }

    /**
     * Binding to port 0 lets the OS pick a port that is known to be free.
     */
    private static function findFreePort(): int
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0', $errorNumber, $errorMessage);
        if (! is_resource($socket)) {
            self::fail(sprintf('Unable to reserve a local port: %s', $errorMessage));
        }

        $name = stream_socket_get_name($socket, false);
        fclose($socket);

        if ($name === false) {
            self::fail('Unable to determine the reserved local port.');
        }

        $port = strrchr($name, ':');

        return $port === false ? 0 : (int) substr($port, 1);
    }

    private static function waitForServer(int $port): void
    {
        for ($attempt = 0; $attempt < 100; $attempt++) {
            $connection = @fsockopen('127.0.0.1', $port, $errorNumber, $errorMessage, 0.2);
            if (is_resource($connection)) {
                fclose($connection);

                return;
            }

            usleep(50_000);
        }

        self::fail(sprintf('The local GitHub API stand-in never came up on port %d.', $port));
    }
}
