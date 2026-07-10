<?php

declare(strict_types=1);

namespace Light\Page\Factory;

use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Light\Page\Handler\GetPageViewHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class GetPageViewHandlerFactory
{
    /**
     * @param class-string $requestedName
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetPageViewHandler
    {
        $postRepository     = $container->get(PostRepository::class);
        $categoryRepository = $container->get(CategoryRepository::class);
        $template           = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        return new GetPageViewHandler($template, $categoryRepository, $postRepository);
    }
}
