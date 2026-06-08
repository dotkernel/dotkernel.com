<?php

declare(strict_types=1);

namespace Light\App\Factory\Categories;

use Light\Blog\Handler\GetCategoriesHandler;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use function assert;

class CategoryHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetCategoriesHandler
    {
        $repository = $container->get(CategoryRepository::class);
        $template   = $container->get(TemplateRendererInterface::class);

        assert($repository instanceof CategoryRepository);
        assert($template instanceof TemplateRendererInterface);

        return new GetCategoriesHandler($template, $repository);
    }
}