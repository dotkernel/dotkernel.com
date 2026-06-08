<?php

declare(strict_types=1);

namespace Light\Blog;

use Light\Blog\Handler\GetArticlesHandler;
use Light\Blog\Handler\GetCategoriesHandler;
use Light\Blog\Handler\GetPostHandler;
use Mezzio\Application;
use Psr\Container\ContainerInterface;

use function assert;

class RoutesDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $callback): Application
    {
        $app = $callback();
        assert($app instanceof Application);

        $app->get('/posts/categories', [GetCategoriesHandler::class], 'page::categories');
        $app->get('/posts', [GetArticlesHandler::class], 'page::posts');
        $app->get('/posts/{slug}', [GetPostHandler::class], 'page::postBySlug');

        return $app;
    }
}
