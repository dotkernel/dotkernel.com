<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Handler\GetFeedViewHandler;
use Light\App\Service\FeedGenerator;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

class GetFeedViewHandlerFactory
{
    /**
     * @param class-string $requestedName
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetFeedViewHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        $categoryRepository = $container->get(CategoryRepository::class);
        assert($categoryRepository instanceof CategoryRepository);

        $feedGenerator = $container->get(FeedGenerator::class);
        assert($feedGenerator instanceof FeedGenerator);

        return new GetFeedViewHandler($template, $categoryRepository, $feedGenerator);
    }
}
