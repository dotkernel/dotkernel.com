<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Service\SitemapGenerator;
use Light\Blog\Repository\PostRepository;
use Psr\Container\ContainerInterface;

use function assert;

class SitemapGeneratorFactory
{
    public function __invoke(ContainerInterface $container): SitemapGenerator
    {
        $postRepository = $container->get(PostRepository::class);
        assert($postRepository instanceof PostRepository);

        $config = $container->get('config');

        return new SitemapGenerator(
            $postRepository,
            $config['sitemap']['path'],
            $config['application']['baseUrl'] ?? '',
        );
    }
}
