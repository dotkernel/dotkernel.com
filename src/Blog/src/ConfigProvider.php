<?php

declare(strict_types=1);

namespace Light\Blog;

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Light\Blog\DBAL\Types\PostStatusEnumType;
use Light\Blog\Factory\Author\AuthorCollectionHandlerFactory;
use Light\Blog\Factory\Author\AuthorResourceHandlerFactory;
use Light\Blog\Factory\Author\AuthorResourceRepositoryFactory;
use Light\Blog\Factory\Category\CategoryCollectionHandlerFactory;
use Light\Blog\Factory\Category\CategoryCollectionRepositoryFactory;
use Light\Blog\Factory\Category\CategoryResourceHandlerFactory;
use Light\Blog\Factory\Post\PostCollectionHandlerFactory;
use Light\Blog\Factory\Post\PostCollectionRepositoryFactory;
use Light\Blog\Factory\Post\PostResourceHandlerFactory;
use Light\Blog\Factory\Tag\TagResourceHandlerFactory;
use Light\Blog\Factory\Tag\TagResourceRepositoryFactory;
use Light\Blog\Handler\GetAuthorCollectionHandler;
use Light\Blog\Handler\GetAuthorResourceHandler;
use Light\Blog\Handler\GetCategoryCollectionHandler;
use Light\Blog\Handler\GetCategoryResourceHandler;
use Light\Blog\Handler\GetPostCollectionHandler;
use Light\Blog\Handler\GetPostResourceHandler;
use Light\Blog\Handler\GetTagResourceHandler;
use Light\Blog\Repository\AuthorRepository;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Light\Blog\Repository\TagRepository;
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
                GetPostCollectionHandler::class     => PostCollectionHandlerFactory::class,
                PostRepository::class               => PostCollectionRepositoryFactory::class,
                GetPostResourceHandler::class       => PostResourceHandlerFactory::class,
                GetCategoryResourceHandler::class   => CategoryResourceHandlerFactory::class,
                AuthorRepository::class             => AuthorResourceRepositoryFactory::class,
                GetAuthorResourceHandler::class     => AuthorResourceHandlerFactory::class,
                GetAuthorCollectionHandler::class   => AuthorCollectionHandlerFactory::class,
                TagRepository::class                => TagResourceRepositoryFactory::class,
                GetTagResourceHandler::class        => TagResourceHandlerFactory::class,
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
                PostStatusEnumType::NAME => PostStatusEnumType::class,
            ],
        ];
    }

    /**
     * @return array{
     *     paths: array{
     *          page: array{literal-string&non-falsy-string},
     *          jsonld: array{literal-string&non-falsy-string},
     *     }
     * }
     */
    private function getTemplates(): array
    {
        return [
            'paths' => [
                'page'   => [__DIR__ . '/../templates/page'],
                'jsonld' => [__DIR__ . '/../templates/page/JSON-LD'],
            ],
        ];
    }
}
