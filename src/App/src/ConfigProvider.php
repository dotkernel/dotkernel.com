<?php

declare(strict_types=1);

namespace Light\App;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Dot\Cache\Adapter\ArrayAdapter;
use Dot\Cache\Adapter\FilesystemAdapter;
use Light\App\DBAL\Types\UuidType;
use Light\App\Factory\EntityListenerResolverFactory;
use Light\App\Factory\FeedGeneratorFactory;
use Light\App\Factory\GetFeedViewHandlerFactory;
use Light\App\Factory\GetIndexViewHandlerFactory;
use Light\App\Factory\GetMarkdownArticleHandlerFactory;
use Light\App\Factory\GetPackagesViewHandlerFactory;
use Light\App\Factory\GetSitemapViewHandlerFactory;
use Light\App\Factory\GitHubClientFactory;
use Light\App\Factory\PackageGeneratorFactory;
use Light\App\Factory\SitemapGeneratorFactory;
use Light\App\Handler\GetFeedViewHandler;
use Light\App\Handler\GetIndexViewHandler;
use Light\App\Handler\GetMarkdownArticleHandler;
use Light\App\Handler\GetPackagesViewHandler;
use Light\App\Handler\GetSitemapViewHandler;
use Light\App\Resolver\EntityListenerResolver;
use Light\App\Service\FeedGenerator;
use Light\App\Service\GitHubClient;
use Light\App\Service\GitHubClientInterface;
use Light\App\Service\PackageGenerator;
use Light\App\Service\SitemapGenerator;
use Mezzio\Application;
use Roave\PsrContainerDoctrine\EntityManagerFactory;
use Symfony\Component\Cache\Adapter\AdapterInterface;

use function getcwd;

/**
 * @phpstan-type ConfigType array{
 *      dependencies: DependenciesType,
 *      doctrine: DoctrineConfigType,
 *      templates: TemplatesType,
 *      resultCacheLifetime: int,
 * }
 * @phpstan-type DoctrineConfigType array{
 *      cache: array{
 *          array: array{
 *              class: class-string<AdapterInterface>,
 *          },
 *          filesystem: array{
 *              class: class-string<AdapterInterface>,
 *              directory: non-empty-string,
 *              namespace: non-empty-string,
 *          },
 *      },
 *      configuration: array{
 *          orm_default: array{
 *              result_cache: non-empty-string,
 *              metadata_cache: non-empty-string,
 *              query_cache: non-empty-string,
 *              hydration_cache: non-empty-string,
 *              typed_field_mapper: non-empty-string|null,
 *              second_level_cache: array{
 *                  enabled: bool,
 *                  default_lifetime: int,
 *                  default_lock_lifetime: int,
 *                  file_lock_region_directory: string,
 *                  regions: string[],
 *               },
 *          },
 *      },
 *      driver: array{
 *          orm_default: array{
 *              class: class-string<MappingDriver>,
 *          },
 *      },
 *      fixtures: non-empty-string,
 *      migrations: array{
 *          table_storage: array{
 *              table_name: non-empty-string,
 *              version_column_name: non-empty-string,
 *              version_column_length: int,
 *              executed_at_column_name: non-empty-string,
 *              execution_time_column_name: non-empty-string,
 *          },
 *          migrations_paths: array<non-empty-string, non-empty-string>,
 *          all_or_nothing: bool,
 *          check_database_platform: bool,
 *      },
 *      types: array<non-empty-string, class-string>,
 * }
 * @phpstan-type DependenciesType array{
 *       factories: array<class-string|non-empty-string, class-string|non-empty-string>,
 *       aliases: array<class-string|non-empty-string, class-string|non-empty-string>,
 * }
 * @phpstan-type TemplatesType array{
 *        paths: non-empty-array<non-empty-string, non-empty-string[]>,
 * }
 **/
class ConfigProvider
{
    /**
     * @return ConfigType
     */
    public function __invoke(): array
    {
        return [
            'dependencies'        => $this->getDependencies(),
            'doctrine'            => $this->getDoctrineConfig(),
            'templates'           => $this->getTemplates(),
            'resultCacheLifetime' => 3600,
        ];
    }

    /**
     * @return DependenciesType
     */
    public function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class => [
                    RoutesDelegator::class,
                ],
            ],
            'factories'  => [
                'doctrine.entity_manager.orm_default' => EntityManagerFactory::class,
                EntityListenerResolver::class         => EntityListenerResolverFactory::class,
                GetIndexViewHandler::class            => GetIndexViewHandlerFactory::class,
                GetFeedViewHandler::class             => GetFeedViewHandlerFactory::class,
                GetMarkdownArticleHandler::class      => GetMarkdownArticleHandlerFactory::class,
                GetSitemapViewHandler::class          => GetSitemapViewHandlerFactory::class,
                GetPackagesViewHandler::class          => GetPackagesViewHandlerFactory::class,
                FeedGenerator::class                  => FeedGeneratorFactory::class,
                SitemapGenerator::class               => SitemapGeneratorFactory::class,
                GitHubClient::class                   => GitHubClientFactory::class,
                PackageGenerator::class               => PackageGeneratorFactory::class,
            ],
            'aliases'    => [
                EntityManager::class          => 'doctrine.entity_manager.orm_default',
                EntityManagerInterface::class => 'doctrine.entity_manager.orm_default',
                GitHubClientInterface::class  => GitHubClient::class,
            ],
        ];
    }

    /**
     * @return TemplatesType
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app'     => [__DIR__ . '/../templates/app'],
                'error'   => [__DIR__ . '/../templates/error'],
                'jsonld'  => [__DIR__ . '/../templates/JSON-LD'],
                'layout'  => [__DIR__ . '/../templates/layout'],
                'partial' => [__DIR__ . '/../templates/partial'],
            ],
        ];
    }

    /**
     * @return DoctrineConfigType
     */
    private function getDoctrineConfig(): array
    {
        return [
            'cache'         => [
                'array'      => [
                    'class' => ArrayAdapter::class,
                ],
                'filesystem' => [
                    'class'     => FilesystemAdapter::class,
                    'directory' => getcwd() . '/data/cache',
                    'namespace' => 'doctrine',
                ],
            ],
            'configuration' => [
                'orm_default' => [
                    'result_cache'       => 'filesystem',
                    'metadata_cache'     => 'filesystem',
                    'query_cache'        => 'filesystem',
                    'hydration_cache'    => 'array',
                    'typed_field_mapper' => null,
                    'second_level_cache' => [
                        'enabled'                    => true,
                        'default_lifetime'           => 3600,
                        'default_lock_lifetime'      => 60,
                        'file_lock_region_directory' => '',
                        'regions'                    => [],
                    ],
                ],
            ],
            'driver'        => [
                'orm_default' => [
                    'class' => MappingDriverChain::class,
                ],
            ],
            'fixtures'      => getcwd() . '/src/App/src/Fixture',
            'migrations'    => [
                'table_storage'           => [
                    'table_name'                 => 'doctrine_migration_versions',
                    'version_column_name'        => 'version',
                    'version_column_length'      => 191,
                    'executed_at_column_name'    => 'executed_at',
                    'execution_time_column_name' => 'execution_time',
                ],
                'migrations_paths'        => [
                    'Migration' => 'src/App/src/Migration',
                ],
                'all_or_nothing'          => false,
                'check_database_platform' => true,
            ],
            'types'         => [
                UuidType::NAME => UuidType::class,
            ],
        ];
    }
}
