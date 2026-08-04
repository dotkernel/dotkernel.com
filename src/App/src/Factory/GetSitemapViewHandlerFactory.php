<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Handler\GetSitemapViewHandler;
use Light\App\Service\SitemapGenerator;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

class GetSitemapViewHandlerFactory
{
    /**
     * @param class-string $requestedName
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetSitemapViewHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        $categoryRepository = $container->get(CategoryRepository::class);
        assert($categoryRepository instanceof CategoryRepository);

        $sitemapGenerator = $container->get(SitemapGenerator::class);
        assert($sitemapGenerator instanceof SitemapGenerator);

        return new GetSitemapViewHandler($template, $categoryRepository, $sitemapGenerator);
    }
}
