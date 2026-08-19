<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Service\LlmsGenerator;
use Light\Blog\Repository\PostRepository;
use Psr\Container\ContainerInterface;

use function assert;

class LlmsGeneratorFactory
{
    public function __invoke(ContainerInterface $container): LlmsGenerator
    {
        $postRepository = $container->get(PostRepository::class);
        assert($postRepository instanceof PostRepository);

        $config = $container->get('config');

        return new LlmsGenerator(
            $postRepository,
            $config['llms']['sourceDir'],
            $config['llms']['indexFile'],
            $config['application']['url'] ?? '',
            $config['llms']['pagesDir'] ?? null,
        );
    }
}
