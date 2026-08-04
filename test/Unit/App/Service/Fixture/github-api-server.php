<?php

/**
 * Stand-in for the GitHub API, served by PHP's built-in web server for the duration of
 * `GitHubClientTest`. It exists so the cURL transport in `GitHubClient` is exercised for real
 * without the test suite needing network access.
 *
 * Each route covers one branch of the client: status handling, `Link` header pagination,
 * redirects, malformed payloads, and request header construction.
 *
 * Runs without the Composer autoloader, so status codes are written as literals rather than
 * referencing `StatusCodeInterface`.
 */

declare(strict_types=1);

use function header;
use function http_response_code;
use function is_string;
use function json_encode;
use function parse_url;
use function sprintf;

use const PHP_URL_PATH;

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path       = is_string($requestUri) ? (string) parse_url($requestUri, PHP_URL_PATH) : '/';

$host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
$host = is_string($host) ? $host : '127.0.0.1';

$requestHeader = static function (string $key): string {
    $value = $_SERVER[$key] ?? '';

    return is_string($value) ? $value : '';
};

switch ($path) {
    case '/ok':
        header('Content-Type: application/json');
        echo '{"lifecycle":"active"}';

        return;

    case '/missing':
        http_response_code(404);
        echo 'Not Found';

        return;

    case '/server-error':
        http_response_code(500);
        echo 'Internal Server Error';

        return;

    case '/redirect':
        http_response_code(302);
        header(sprintf('Location: http://%s/ok', $host));

        return;

    // The `rel="last"` entry proves every rel is parsed, not just the one that gets used, and the
    // bare string in the payload proves non-array members are discarded.
    case '/page-1':
        header(sprintf('Link: <http://%s/page-2>; rel="next", <http://%s/page-9>; rel="last"', $host, $host));
        echo (string) json_encode([['name' => 'one'], 'discard me', ['name' => 'two']]);

        return;

    case '/page-2':
        header('Link: <not a parseable link entry>');
        echo (string) json_encode([['name' => 'three']]);

        return;

    case '/not-an-array':
        echo '"a bare string"';

        return;

    case '/echo-request':
        echo (string) json_encode([
            'accept'        => $requestHeader('HTTP_ACCEPT'),
            'apiVersion'    => $requestHeader('HTTP_X_GITHUB_API_VERSION'),
            'authorization' => $requestHeader('HTTP_AUTHORIZATION'),
            'userAgent'     => $requestHeader('HTTP_USER_AGENT'),
        ]);

        return;
}

http_response_code(404);
echo 'Unknown route';
