<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Service\FeedGenerator;
use Light\Blog\Repository\PostRepository;
use Psr\Container\ContainerInterface;

use function assert;
use function rtrim;

class FeedGeneratorFactory
{
    public function __invoke(ContainerInterface $container): FeedGenerator
    {
        $postRepository = $container->get(PostRepository::class);
        assert($postRepository instanceof PostRepository);

        $config = $container->get('config');

        return new FeedGenerator(
            $postRepository,
            $config['feed']['file'],
            rtrim($config['application']['url'] ?? '', '/') . '/',
            $config['app']['meta']['title'] ?? '',
            $config['app']['meta']['description'] ?? '',
            $config['app']['meta']['image'] ?? '',
        );
    }
}
