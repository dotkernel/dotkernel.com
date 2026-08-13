<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Service\FeedGenerator;
use Light\Blog\Repository\PostRepository;
use Psr\Container\ContainerInterface;

use function assert;

class FeedGeneratorFactory
{
    public function __invoke(ContainerInterface $container): FeedGenerator
    {
        $postRepository = $container->get(PostRepository::class);
        assert($postRepository instanceof PostRepository);

        $config = $container->get('config');

        return new FeedGenerator(
            $postRepository,
            $config['feed']['path'],
            $config['application']['url'] ?? '',
            $config['application']['meta']['title'] ?? '',
            $config['application']['meta']['description'] ?? '',
            $config['application']['meta']['image'] ?? '',
        );
    }
}
