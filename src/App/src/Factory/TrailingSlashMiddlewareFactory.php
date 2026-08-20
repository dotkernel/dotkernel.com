<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Middleware\TrailingSlashMiddleware;
use Psr\Container\ContainerInterface;

class TrailingSlashMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): TrailingSlashMiddleware
    {
        return new TrailingSlashMiddleware();
    }
}
