<?php

declare(strict_types=1);

namespace Light\App\Middleware;

use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_key_exists;
use function sprintf;

/**
 * Adds a `Link: <...>; rel="service-doc"` header (RFC 8631) on pages that have a dedicated
 * documentation page on docs.dotkernel.org, pointing at that page's own docs.
 */
class ServiceDocLinkMiddleware implements MiddlewareInterface
{
    private const array ROUTE_DOCS = [
        'app::index'         => 'https://docs.dotkernel.org/',
        'page::api'          => 'https://docs.dotkernel.org/api-documentation/',
        'page::admin'        => 'https://docs.dotkernel.org/admin-documentation/',
        'page::queue'        => 'https://docs.dotkernel.org/queue-documentation/',
        'page::light'        => 'https://docs.dotkernel.org/light-documentation/',
        'page::frontend'     => 'https://docs.dotkernel.org/frontend/',
        'page::wsl2'         => 'https://docs.dotkernel.org/development/v2/terminal/',
        'page::architecture' => 'https://docs.dotkernel.org/api-documentation/',
    ];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $routeResult = $request->getAttribute(RouteResult::class);
        if (! $routeResult instanceof RouteResult || ! $routeResult->isSuccess()) {
            return $response;
        }

        $routeName = $routeResult->getMatchedRouteName();
        if ($routeName === false || ! array_key_exists($routeName, self::ROUTE_DOCS)) {
            return $response;
        }

        $serviceDocLink = sprintf('<%s>; rel="service-doc"', self::ROUTE_DOCS[$routeName]);
        $existingLink   = $response->getHeaderLine('Link');

        // A single `Link` header line with comma-separated values (RFC 8288 section 3), matching
        // the style the `'*'` dot_response_headers entry already uses for llms-txt/llms-full-txt.
        return $response->withHeader(
            'Link',
            $existingLink === '' ? $serviceDocLink : $existingLink . ', ' . $serviceDocLink
        );
    }
}
