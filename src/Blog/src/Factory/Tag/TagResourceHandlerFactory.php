<?php

declare(strict_types=1);

namespace Light\Blog\Factory\Tag;

use Light\Blog\Handler\GetTagResourceHandler;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\TagRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class TagResourceHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetTagResourceHandler
    {
        $repository         = $container->get(TagRepository::class);
        $template           = $container->get(TemplateRendererInterface::class);
        $categoryRepository = $container->get(CategoryRepository::class);

        assert($repository instanceof TagRepository);
        assert($template instanceof TemplateRendererInterface);
        assert($categoryRepository instanceof CategoryRepository);

        return new GetTagResourceHandler($template, $repository, $categoryRepository);
    }
}
