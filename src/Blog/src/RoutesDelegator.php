<?php

declare(strict_types=1);

namespace Light\Blog;

use Light\Blog\Handler\GetAuthorResourceHandler;
use Light\Blog\Handler\GetCategoryCollectionHandler;
use Light\Blog\Handler\GetCategoryResourceHandler;
use Light\Blog\Handler\GetPostCollectionHandler;
use Light\Blog\Handler\GetPostResourceHandler;
use Mezzio\Application;
use Psr\Container\ContainerInterface;

use function assert;

class RoutesDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $callback): Application
    {
        $app = $callback();
        assert($app instanceof Application);
        $app->get('/blog', [GetPostCollectionHandler::class], 'page::blog');
        $app->get('/category/{slug}', [GetCategoryResourceHandler::class], 'page::category-resource');
        $app->get('/categories', [GetCategoryCollectionHandler::class], 'page::categories');
        $app->get('/author/{slug}', [GetAuthorResourceHandler::class], 'page::author-resource');
        $app->get('/{categorySlug}/{slug}', [GetPostResourceHandler::class], 'page::blog-resource');
        return $app;
    }
}
