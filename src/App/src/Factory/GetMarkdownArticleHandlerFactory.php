<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Handler\GetMarkdownArticleHandler;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class GetMarkdownArticleHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetMarkdownArticleHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        $categoryRepository = $container->get(CategoryRepository::class);
        assert($categoryRepository instanceof CategoryRepository);

        $config = $container->get('config');

        return new GetMarkdownArticleHandler($template, $categoryRepository, $config['llms']['sourceDir']);
    }
}
