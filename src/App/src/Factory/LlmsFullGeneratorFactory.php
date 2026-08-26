<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Service\LlmsFullGenerator;
use Light\Blog\Repository\PostRepository;
use Psr\Container\ContainerInterface;

use function assert;

class LlmsFullGeneratorFactory
{
    public function __invoke(ContainerInterface $container): LlmsFullGenerator
    {
        $postRepository = $container->get(PostRepository::class);
        assert($postRepository instanceof PostRepository);

        $config = $container->get('config');

        return new LlmsFullGenerator(
            $postRepository,
            $config['llms']['sourceDir'],
            $config['llms']['outputFile'],
            $config['application']['url'] ?? '',
            // Optional: a config predating the static-page markdown simply skips those sections.
            $config['llms']['pagesDir'] ?? null,
        );
    }
}
