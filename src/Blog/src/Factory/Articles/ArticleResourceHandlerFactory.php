<?php

declare(strict_types=1);

namespace Light\Blog\Factory\Articles;

use Light\Blog\Handler\GetArticleResourceHandler;
use Light\Blog\Repository\ArticleRepository;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class ArticleResourceHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetArticleResourceHandler
    {
        $repository = $container->get(ArticleRepository::class);
        $categoryRepository = $container->get(CategoryRepository::class);
        $template   = $container->get(TemplateRendererInterface::class);

        assert($repository instanceof ArticleRepository);
        assert($template instanceof TemplateRendererInterface);

        return new GetArticleResourceHandler($template, $repository, $categoryRepository);
    }
}
