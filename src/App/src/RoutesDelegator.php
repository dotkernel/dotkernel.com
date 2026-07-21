<?php

declare(strict_types=1);

namespace Light\App;

use Laminas\Diactoros\Response\RedirectResponse;
use Light\App\Handler\GetIndexViewHandler;
use Mezzio\Application;
use Psr\Container\ContainerInterface;

use function assert;

class RoutesDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $callback): Application
    {
        $app = $callback();
        assert($app instanceof Application);
        $app->get('/', [GetIndexViewHandler::class], 'app::index');

        $app->get('/{first}', function ($request) {
            $uri = $request->getUri();
            return new RedirectResponse((string) $uri . '/', 301);
        });

        $app->get('/{first}/{second}', function ($request) {
            $uri = $request->getUri();
            return new RedirectResponse((string) $uri . '/', 301);
        });

        return $app;
    }
}
