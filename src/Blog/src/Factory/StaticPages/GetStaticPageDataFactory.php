<?php

declare(strict_types=1);

namespace Light\Blog\Factory\StaticPages;

use Light\Blog\Handler\GetStaticPageDataHandler;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class GetStaticPageDataFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetStaticPageDataHandler
    {
        $repository         = $container->get(PostRepository::class);
        $categoryRepository = $container->get(CategoryRepository::class);
        $template           = $container->get(TemplateRendererInterface::class);

        assert($repository instanceof PostRepository);
        assert($template instanceof TemplateRendererInterface);

        return new GetStaticPageDataHandler($template, $repository, $categoryRepository);
    }
}
