<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Service\SitemapGenerator;
use Light\Blog\Repository\AuthorRepository;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Psr\Container\ContainerInterface;

use function array_keys;
use function array_merge;
use function assert;
use function is_array;

class SitemapGeneratorFactory
{
    public function __invoke(ContainerInterface $container): SitemapGenerator
    {
        $postRepository = $container->get(PostRepository::class);
        assert($postRepository instanceof PostRepository);

        $categoryRepository = $container->get(CategoryRepository::class);
        assert($categoryRepository instanceof CategoryRepository);

        $authorRepository = $container->get(AuthorRepository::class);
        assert($authorRepository instanceof AuthorRepository);

        $config = $container->get('config');

        $pageRoutes = [];
        foreach ($config['routes'] ?? [] as $moduleRoutes) {
            if (is_array($moduleRoutes)) {
                $pageRoutes = array_merge($pageRoutes, array_keys($moduleRoutes));
            }
        }

        return new SitemapGenerator(
            $postRepository,
            $categoryRepository,
            $authorRepository,
            $pageRoutes,
            $config['sitemap']['path'],
            $config['application']['url'] ?? '',
        );
    }
}
