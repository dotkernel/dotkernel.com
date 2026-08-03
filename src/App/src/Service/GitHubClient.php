<?php

declare(strict_types=1);

namespace Light\App\Service;

use CurlHandle;
use Fig\Http\Message\StatusCodeInterface;
use RuntimeException;

use function curl_error;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt_array;
use function explode;
use function is_array;
use function json_decode;
use function preg_match;
use function sprintf;
use function str_starts_with;
use function strlen;
use function strtolower;
use function trim;

use const CURLINFO_RESPONSE_CODE;
use const CURLOPT_CONNECTTIMEOUT;
use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_HEADERFUNCTION;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_MAXREDIRS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_TIMEOUT;
use const CURLOPT_URL;
use const CURLOPT_USERAGENT;

/**
 * Minimal cURL-backed GitHub API client.
 *
 * Deliberately not a general purpose HTTP client: it covers the authenticated GET requests the
 * package generator needs and nothing more. Requests degrade to unauthenticated when no token is
 * configured, which keeps the generator usable on a machine without credentials.
 *
 * @phpstan-type ResponseData array{status: int, body: string, links: array<string, string>}
 */
class GitHubClient implements GitHubClientInterface
{
    private const string API_ROOT    = 'https://api.github.com';
    private const string API_VERSION = '2022-11-28';

    public function __construct(
        private readonly string $token,
        private readonly string $userAgent,
        private readonly int $timeout,
        private readonly int $connectTimeout,
    ) {
    }

    public function get(string $path, string $accept = self::ACCEPT_JSON): ?string
    {
        $response = $this->request($this->absoluteUrl($path), $accept);

        if ($response['status'] === StatusCodeInterface::STATUS_NOT_FOUND) {
            return null;
        }

        $this->assertOk($response['status'], $path);

        return $response['body'];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getAllPages(string $path): array
    {
        $url   = $this->absoluteUrl($path);
        $items = [];

        while ($url !== null) {
            $response = $this->request($url, self::ACCEPT_JSON);
            $this->assertOk($response['status'], $url);

            $decoded = json_decode($response['body'], true);
            if (! is_array($decoded)) {
                throw new RuntimeException(sprintf('Expected a JSON array from %s.', $url));
            }

            foreach ($decoded as $item) {
                if (is_array($item)) {
                    $items[] = $item;
                }
            }

            $url = $response['links']['next'] ?? null;
        }

        return $items;
    }

    /**
     * @return ResponseData
     */
    private function request(string $url, string $accept): array
    {
        $handle = curl_init();
        if (! $handle instanceof CurlHandle) {
            throw new RuntimeException('Unable to initialise a cURL handle.');
        }

        $links = [];

        curl_setopt_array($handle, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
            CURLOPT_USERAGENT      => $this->userAgent,
            CURLOPT_HTTPHEADER     => $this->headers($accept),
            CURLOPT_HEADERFUNCTION => function (CurlHandle $curlHandle, string $header) use (&$links): int {
                $parts = explode(':', $header, 2);
                if (isset($parts[1]) && strtolower(trim($parts[0])) === 'link') {
                    $links = $this->parseLinkHeader(trim($parts[1]));
                }

                return strlen($header);
            },
        ]);

        $body   = curl_exec($handle);
        $error  = curl_error($handle);
        $status = curl_getinfo($handle, CURLINFO_RESPONSE_CODE);

        if ($body === false) {
            throw new RuntimeException(sprintf('Request to %s failed: %s', $url, $error));
        }

        return [
            'status' => (int) $status,
            'body'   => (string) $body,
            'links'  => $links,
        ];
    }

    /**
     * Parses `<https://...>; rel="next", <https://...>; rel="last"` into a rel => url map.
     *
     * @return array<string, string>
     */
    private function parseLinkHeader(string $value): array
    {
        $links = [];

        foreach (explode(',', $value) as $part) {
            if (preg_match('/<([^>]+)>\s*;\s*rel="([^"]+)"/', trim($part), $matches) === 1) {
                $links[$matches[2]] = $matches[1];
            }
        }

        return $links;
    }

    /**
     * @return list<string>
     */
    private function headers(string $accept): array
    {
        $headers = [
            'Accept: ' . $accept,
            'X-GitHub-Api-Version: ' . self::API_VERSION,
        ];

        if ($this->token !== '') {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }

        return $headers;
    }

    private function absoluteUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return self::API_ROOT . $path;
    }

    private function assertOk(int $status, string $url): void
    {
        if ($status === StatusCodeInterface::STATUS_OK) {
            return;
        }

        throw new RuntimeException(sprintf('GitHub returned HTTP %d for %s.', $status, $url));
    }
}
