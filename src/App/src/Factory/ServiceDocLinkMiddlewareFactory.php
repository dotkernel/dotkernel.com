<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Middleware\ServiceDocLinkMiddleware;
use Psr\Container\ContainerInterface;

class ServiceDocLinkMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): ServiceDocLinkMiddleware
    {
        return new ServiceDocLinkMiddleware();
    }
}
