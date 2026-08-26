<?php

declare(strict_types=1);

namespace Light\Page\Factory;

use Light\Page\Service\PageService;
use Light\Page\Service\PageServiceInterface;
use Psr\Container\ContainerInterface;

class PageServiceFactory
{
    /**
     * @param class-string $requestedName
     */
    public function __invoke(ContainerInterface $container, string $requestedName): PageServiceInterface
    {
        $config = $container->get('config');

        return new PageService($config['llms']['pagesDir']);
    }
}
