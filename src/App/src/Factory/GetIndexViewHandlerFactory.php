<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Handler\GetIndexViewHandler;
use Light\Blog\Repository\PostRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class GetIndexViewHandlerFactory
{
    /**
     * @param class-string $requestedName
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetIndexViewHandler
    {
        $postRepository = $container->get(PostRepository::class);
        $template       = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        $config = $container->get('config');

        return new GetIndexViewHandler($template, $postRepository, $config['llms']['pagesDir']);
    }
}
