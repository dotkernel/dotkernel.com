<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Handler\GetPackagesViewHandler;
use Light\App\Service\PackageGenerator;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

class GetPackagesViewHandlerFactory
{
    public function __invoke(ContainerInterface $container): GetPackagesViewHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        $categoryRepository = $container->get(CategoryRepository::class);
        assert($categoryRepository instanceof CategoryRepository);

        $postRepository = $container->get(PostRepository::class);
        assert($postRepository instanceof PostRepository);

        $packageGenerator = $container->get(PackageGenerator::class);
        assert($packageGenerator instanceof PackageGenerator);

        return new GetPackagesViewHandler($template, $categoryRepository, $postRepository, $packageGenerator);
    }
}
