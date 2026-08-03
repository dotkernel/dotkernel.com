<?php

declare(strict_types=1);

namespace Light\Page;

use Light\Page\Factory\GetPageViewHandlerFactory;
use Light\Page\Factory\PageServiceFactory;
use Light\Page\Handler\GetPageViewHandler;
use Light\Page\Service\PageService;
use Light\Page\Service\PageServiceInterface;
use Mezzio\Application;

/**
 * @phpstan-type ConfigType array{
 *      dependencies: DependenciesType,
 *      templates: TemplatesType,
 * }
 * @phpstan-type DependenciesType array{
 *       delegators: non-empty-array<class-string, array<class-string>>,
 *       factories: array<class-string|non-empty-string, class-string|non-empty-string>,
 *       aliases: array<class-string|non-empty-string, class-string|non-empty-string>,
 * }
 * @phpstan-type TemplatesType array{
 *       paths: non-empty-array<non-empty-string, non-empty-string[]>,
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
                GetPageViewHandler::class => GetPageViewHandlerFactory::class,
                PageService::class        => PageServiceFactory::class,
            ],
            'aliases'    => [
                PageServiceInterface::class => PageService::class,
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
                'page' => [__DIR__ . '/../templates/page'],
            ],
        ];
    }
}
