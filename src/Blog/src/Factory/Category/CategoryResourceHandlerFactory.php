<?php

declare(strict_types=1);

namespace Light\Blog\Factory\Category;

use Light\Blog\Handler\GetCategoryResourceHandler;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Service\BlogServiceInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class CategoryResourceHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetCategoryResourceHandler
    {
        $repository  = $container->get(CategoryRepository::class);
        $template    = $container->get(TemplateRendererInterface::class);
        $blogService = $container->get(BlogServiceInterface::class);

        assert($repository instanceof CategoryRepository);
        assert($template instanceof TemplateRendererInterface);
        assert($blogService instanceof BlogServiceInterface);

        return new GetCategoryResourceHandler($template, $repository, $blogService);
    }
}
