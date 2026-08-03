<?php

declare(strict_types=1);

namespace Light\App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PoweredByMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly string $siteName)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if ($this->siteName === '') {
            return $response;
        }

        return $response->withHeader('X-Powered-By', $this->siteName);
    }
}
