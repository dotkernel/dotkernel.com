<?php

declare(strict_types=1);

namespace Light\App\Service;

use RuntimeException;

interface GitHubClientInterface
{
    public const ACCEPT_JSON = 'application/vnd.github+json';
    public const ACCEPT_RAW  = 'application/vnd.github.raw';

    /**
     * Performs a single authenticated GET request.
     *
     * @param string $path Path relative to the API root, or an absolute URL.
     * @return string|null The raw response body, or null when the resource does not exist.
     * @throws RuntimeException On transport failure or an unexpected response status.
     */
    public function get(string $path, string $accept = self::ACCEPT_JSON): ?string;

    /**
     * Performs a GET request and follows every `rel="next"` link, merging the decoded pages.
     *
     * @return list<array<string, mixed>>
     * @throws RuntimeException On transport failure or an unexpected response status.
     */
    public function getAllPages(string $path): array;
}
