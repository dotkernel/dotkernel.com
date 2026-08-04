<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Factory;

use Light\App\Factory\GitHubClientFactory;
use Light\App\Service\GitHubClient;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerInterface;
use ReflectionProperty;

class GitHubClientFactoryTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testInvokeAppliesTheConfiguredValues(): void
    {
        $client = (new GitHubClientFactory())($this->createContainer([
            'github'   => [
                'authBearer' => 'gh-token',
                'userAgent'  => 'dotkernel.com-test',
            ],
            'packages' => [
                'timeout'        => 30,
                'connectTimeout' => 15,
            ],
        ]));

        $this->assertSame('gh-token', $this->readProperty($client, 'token'));
        $this->assertSame('dotkernel.com-test', $this->readProperty($client, 'userAgent'));
        $this->assertSame(30, $this->readProperty($client, 'timeout'));
        $this->assertSame(15, $this->readProperty($client, 'connectTimeout'));
    }

    /**
     * Values arrive from config, so they are not guaranteed to already be the right type.
     *
     * @throws Exception
     */
    public function testInvokeCastsTheConfiguredValues(): void
    {
        $client = (new GitHubClientFactory())($this->createContainer([
            'github'   => ['authBearer' => 12345, 'userAgent' => 678],
            'packages' => ['timeout' => '30', 'connectTimeout' => '15'],
        ]));

        $this->assertSame('12345', $this->readProperty($client, 'token'));
        $this->assertSame('678', $this->readProperty($client, 'userAgent'));
        $this->assertSame(30, $this->readProperty($client, 'timeout'));
        $this->assertSame(15, $this->readProperty($client, 'connectTimeout'));
    }

    /**
     * A machine without credentials still gets a usable, unauthenticated client.
     *
     * @param mixed $config
     * @throws Exception
     */
    #[DataProvider('incompleteConfigProvider')]
    public function testInvokeFallsBackToDefaults($config): void
    {
        $client = (new GitHubClientFactory())($this->createContainer($config));

        $this->assertSame('', $this->readProperty($client, 'token'));
        $this->assertSame('dotkernel.com', $this->readProperty($client, 'userAgent'));
        $this->assertSame(10, $this->readProperty($client, 'timeout'));
        $this->assertSame(5, $this->readProperty($client, 'connectTimeout'));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function incompleteConfigProvider(): array
    {
        return [
            'config is not an array'  => ['not an array'],
            'config is empty'         => [[]],
            'sections are not arrays' => [['github' => 'nope', 'packages' => 'nope']],
            'sections are empty'      => [['github' => [], 'packages' => []]],
        ];
    }

    /**
     * @throws Exception
     */
    private function createContainer(mixed $config): ContainerInterface
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with('config')->willReturn($config);

        return $container;
    }

    private function readProperty(GitHubClient $client, string $name): mixed
    {
        return (new ReflectionProperty(GitHubClient::class, $name))->getValue($client);
    }
}
