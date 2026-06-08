<?php

declare(strict_types=1);

namespace Light\App\Factory\Posts;

use Light\Blog\Handler\GetPostHandler;
use Light\Blog\Repository\ArticleRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class PostHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetPostHandler
    {
        $repository = $container->get(ArticleRepository::class);
        $template   = $container->get(TemplateRendererInterface::class);

        assert($repository instanceof ArticleRepository);
        assert($template instanceof TemplateRendererInterface);

        return new GetPostHandler($template, $repository);
    }
}
