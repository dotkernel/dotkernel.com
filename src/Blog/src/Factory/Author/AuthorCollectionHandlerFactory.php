<?php

declare(strict_types=1);

namespace Light\Blog\Factory\Author;

use Light\Blog\Handler\GetAuthorCollectionHandler;
use Light\Blog\Repository\AuthorRepository;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class AuthorCollectionHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetAuthorCollectionHandler
    {
        $repository         = $container->get(AuthorRepository::class);
        $categoryRepository = $container->get(CategoryRepository::class);
        $template           = $container->get(TemplateRendererInterface::class);

        assert($repository instanceof AuthorRepository);
        assert($categoryRepository instanceof CategoryRepository);
        assert($template instanceof TemplateRendererInterface);

        return new GetAuthorCollectionHandler($template, $repository, $categoryRepository);
    }
}
