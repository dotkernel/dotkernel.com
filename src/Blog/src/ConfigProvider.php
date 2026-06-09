<?php

declare(strict_types=1);

namespace Light\Blog;

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Light\Blog\DBAL\Types\ArticleStatusEnumType;
use Light\Blog\Factory\Articles\ArticleCollectionHandlerFactory;
use Light\Blog\Factory\Articles\ArticleCollectionRepositoryFactory;
use Light\Blog\Factory\Articles\ArticleResourceHandlerFactory;
use Light\Blog\Factory\Categories\CategoryCollectionHandlerFactory;
use Light\Blog\Factory\Categories\CategoryCollectionRepositoryFactory;
use Light\Blog\Factory\Categories\CategoryResourceHandlerFactory;
use Light\Blog\Handler\GetArticleCollectionHandler;
use Light\Blog\Handler\GetArticleResourceHandler;
use Light\Blog\Handler\GetCategoryCollectionHandler;
use Light\Blog\Handler\GetCategoryResourceHandler;
use Light\Blog\Repository\ArticleRepository;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Application;

class ConfigProvider
{
    /**
    @return array{
     *     dependencies: array<mixed>,
     *     templates: array<mixed>,
     * }
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'doctrine'     => $this->getDoctrineConfig(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * @return array{
     *     delegators: array<class-string, array<class-string>>,
     *     factories: array<class-string, class-string>,
     * }
     */
    private function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class => [
                    RoutesDelegator::class,
                ],
            ],
            'factories'  => [
                GetCategoryCollectionHandler::class => CategoryCollectionHandlerFactory::class,
                CategoryRepository::class           => CategoryCollectionRepositoryFactory::class,
                GetArticleCollectionHandler::class  => ArticleCollectionHandlerFactory::class,
                ArticleRepository::class            => ArticleCollectionRepositoryFactory::class,
                GetArticleResourceHandler::class    => ArticleResourceHandlerFactory::class,
                GetCategoryResourceHandler::class   => CategoryResourceHandlerFactory::class,
            ],
        ];
    }

    /**
    @return array{
     *     driver: array<mixed>,
     *     types: array<mixed>,
     * }
     */
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
            'types'  => [
                ArticleStatusEnumType::NAME => ArticleStatusEnumType::class,
            ],
        ];
    }

    /**
     * @return array{
     *     paths: array{
     *          page: array{literal-string&non-falsy-string},
     *     }
     * }
     */
    private function getTemplates(): array
    {
        return [
            'paths' => [
                'page' => [__DIR__ . '/../templates/page'],
            ],
        ];
    }
}
