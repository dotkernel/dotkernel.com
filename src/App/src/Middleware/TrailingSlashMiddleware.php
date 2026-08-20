<?php

declare(strict_types=1);

namespace Light\App\Middleware;

use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function in_array;
use function str_contains;
use function str_ends_with;
use function strrpos;
use function substr;

class TrailingSlashMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (! in_array($request->getMethod(), ['GET', 'HEAD'], true)) {
            return $handler->handle($request);
        }

        $uri  = $request->getUri();
        $path = $uri->getPath();

        if ($path === '' || $path === '/' || str_ends_with($path, '/')) {
            return $handler->handle($request);
        }

        $lastSegment = substr($path, strrpos($path, '/') + 1);
        if (str_contains($lastSegment, '.')) {
            return $handler->handle($request);
        }

        return new RedirectResponse((string) $uri->withPath($path . '/'), 301);
    }
}
