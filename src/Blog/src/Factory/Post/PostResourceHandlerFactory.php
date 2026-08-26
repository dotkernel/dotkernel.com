<?php

declare(strict_types=1);

namespace Light\Blog\Factory\Post;

use Light\Blog\Handler\GetPostResourceHandler;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Light\Blog\Service\BlogServiceInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class PostResourceHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetPostResourceHandler
    {
        $repository         = $container->get(PostRepository::class);
        $categoryRepository = $container->get(CategoryRepository::class);
        $template           = $container->get(TemplateRendererInterface::class);
        $blogService        = $container->get(BlogServiceInterface::class);

        assert($repository instanceof PostRepository);
        assert($template instanceof TemplateRendererInterface);
        assert($blogService instanceof BlogServiceInterface);

        return new GetPostResourceHandler(
            $template,
            $repository,
            $categoryRepository,
            $blogService
        );
    }
}
