<?php

declare(strict_types=1);

namespace Light\Blog\Factory\Author;

use Light\Blog\Handler\GetAuthorResourceHandler;
use Light\Blog\Repository\AuthorRepository;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class AuthorResourceHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetAuthorResourceHandler
    {
        $repository         = $container->get(AuthorRepository::class);
        $template           = $container->get(TemplateRendererInterface::class);
        $postRepository     = $container->get(PostRepository::class);
        $categoryRepository = $container->get(CategoryRepository::class);

        assert($repository instanceof AuthorRepository);
        assert($template instanceof TemplateRendererInterface);
        assert($postRepository instanceof PostRepository);

        return new GetAuthorResourceHandler($template, $repository, $postRepository, $categoryRepository);
    }
}
