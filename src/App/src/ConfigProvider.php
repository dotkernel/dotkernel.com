<?php

declare(strict_types=1);

namespace Light\App;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Dot\Cache\Adapter\ArrayAdapter;
use Dot\Cache\Adapter\FilesystemAdapter;
use Light\App\Factory\GetIndexViewHandlerFactory;
use Light\App\Handler\GetIndexViewHandler;
use Mezzio\Application;
use Ramsey\Uuid\Doctrine\UuidType;
use Roave\PsrContainerDoctrine\EntityManagerFactory;
use Symfony\Component\Cache\Adapter\AdapterInterface;

use function getcwd;

/**
 * @phpstan-type ConfigType array{
 *      dependencies: DependenciesType,
 *      doctrine: DoctrineConfigType,
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
 *     fixtures: non-empty-string,
 *      migrations: array{
 *          migrations_paths: array<non-empty-string, non-empty-string>,
 *          all_or_nothing: bool,
 *          check_database_platform: bool,
 *      },
 * }
 * @phpstan-type DependenciesType array{
 *       factories: array<class-string|non-empty-string, class-string|non-empty-string>,
 *       aliases: array<class-string|non-empty-string, class-string|non-empty-string>,
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
            'dependencies' => $this->getDependencies(),
            'doctrine'     => $this->getDoctrineConfig(),
            'templates'    => $this->getTemplates(),
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
                GetIndexViewHandler::class            => GetIndexViewHandlerFactory::class,
            ],
            'aliases'    => [
                EntityManager::class          => 'doctrine.entity_manager.orm_default',
                EntityManagerInterface::class => 'doctrine.entity_manager.orm_default',
            ],
        ];
    }

    /**
     * @return array{
     *     paths: array{
     *          app: array{literal-string&non-falsy-string},
     *          error: array{literal-string&non-falsy-string},
     *          layout: array{literal-string&non-falsy-string},
     *          partial: array{literal-string&non-falsy-string},
     *     }
     * }
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app'     => [__DIR__ . '/../templates/app'],
                'error'   => [__DIR__ . '/../templates/error'],
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
                // The default metadata driver aggregates all other drivers into a single one.
                // Override `orm_default` only if you know what you're doing.
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
                'all_or_nothing'          => true,
                'check_database_platform' => true,
            ],
            'types'         => [
                UuidType::NAME => UuidType::class,
            ],
        ];
    }
}
