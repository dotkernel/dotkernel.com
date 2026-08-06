<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Service\LlmsFullGenerator;
use Psr\Container\ContainerInterface;

class LlmsFullGeneratorFactory
{
    public function __invoke(ContainerInterface $container): LlmsFullGenerator
    {
        $config = $container->get('config');

        return new LlmsFullGenerator(
            $config['llms']['sourceDir'],
            $config['llms']['outputFile'],
            $config['application']['baseUrl'] ?? '',
        );
    }
}
