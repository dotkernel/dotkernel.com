<?php

declare(strict_types=1);

namespace Light\Blog;

use Light\Blog\Handler\GetArticleCollectionHandler;
use Light\Blog\Handler\GetArticleResourceHandler;
use Light\Blog\Handler\GetCategoryCollectionHandler;
use Light\Blog\Handler\GetCategoryResourceHandler;
use Mezzio\Application;
use Psr\Container\ContainerInterface;

use function assert;

class RoutesDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $callback): Application
    {
        $app = $callback();
        assert($app instanceof Application);

        $app->get('/blog/categories', [GetCategoryCollectionHandler::class], 'page::categories');
        $app->get('/blog', [GetArticleCollectionHandler::class], 'page::blog');
        $app->get('/blog/{slug}', [GetArticleResourceHandler::class], 'page::blog-resource');
        $app->get('/blog/categories/{slug}', [GetCategoryResourceHandler::class], 'page::category-resource');

        return $app;
    }
}
