<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Middleware\PoweredByMiddleware;
use Psr\Container\ContainerInterface;

class PoweredByMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): PoweredByMiddleware
    {
        $config = $container->get('config');

        return new PoweredByMiddleware($config['application']['meta']['siteName'] ?? '');
    }
}
