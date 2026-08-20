<?php

declare(strict_types=1);

namespace Light\App;

use Laminas\Diactoros\Response\RedirectResponse;
use Light\App\Handler\GetFeedViewHandler;
use Light\App\Handler\GetIndexViewHandler;
use Light\App\Handler\GetMarkdownArticleHandler;
use Light\App\Handler\GetPackagesViewHandler;
use Light\App\Handler\GetSitemapViewHandler;
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
        $app->get('/feed/', [GetFeedViewHandler::class], 'app::feed');
        $app->get('/sitemap/', [GetSitemapViewHandler::class], 'app::sitemap');
        $app->get('/{categorySlug}/{slug}.md', [GetMarkdownArticleHandler::class], 'app::markdown-article');

        // Route name kept as `page::…` because `@layout/default.html.twig` links it by name.
        // The matching entry must stay out of `routes.page` in local.php to avoid a duplicate.
        $app->get(
            '/dotkernel-packages-oss-lifecycle/',
            [GetPackagesViewHandler::class],
            GetPackagesViewHandler::TEMPLATE
        );

        $app->get('/{wpPath:wp-.*}', function () {
            return new RedirectResponse('/', 301);
        });

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
