<?php

declare(strict_types=1);

namespace Light\Blog;

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Light\App\Factory\Articles\ArticleHandlerFactory;
use Light\App\Factory\Categories\CategoryHandlerFactory;
use Light\App\Factory\Categories\CategoryRepositoryFactory;
use Light\App\Factory\Articles\ArticleRepositoryFactory;

use Light\App\Factory\Posts\PostHandlerFactory;
use Light\Blog\Handler\GetPostHandler;
use Light\Blog\Handler\GetArticlesHandler;
use Light\Blog\Handler\GetCategoriesHandler;
use Light\Blog\Repository\ArticleRepository;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Application;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'doctrine'     => $this->getDoctrineConfig(),
            'templates'    => $this->getTemplates(),
        ];
    }
    private function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class => [
                    RoutesDelegator::class,
                ],
            ],
            'factories'  => [
                GetCategoriesHandler::class => CategoryHandlerFactory::class,
                CategoryRepository::class => CategoryRepositoryFactory::class,
                GetArticlesHandler::class => ArticleHandlerFactory::class,
                ArticleRepository::class => ArticleRepositoryFactory::class,
                GetPostHandler::class => PostHandlerFactory::class,
            ],
        ];
    }

    private function getDoctrineConfig(): array
    {
        return [
            'driver' => [
                'orm_default'  => [
                    'drivers' => [
                        'Light\Blog\Entity' => 'BlogEntities',
                    ],
                ],
                'BlogEntities' => [
                    'class' => AttributeDriver::class,
                    'cache' => 'array',
                    'paths' => [__DIR__ . '/Entity'],
                ],
            ],
        ];
    }

    private function getTemplates(): array
    {
        return [
            'paths' => [
                'page' => [__DIR__ . '/../templates/page'],
            ],
        ];
    }
}