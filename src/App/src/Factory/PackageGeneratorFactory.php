<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Service\GitHubClientInterface;
use Light\App\Service\PackageGenerator;
use Psr\Container\ContainerInterface;

use function assert;
use function is_array;
use function is_scalar;

class PackageGeneratorFactory
{
    public function __invoke(ContainerInterface $container): PackageGenerator
    {
        $client = $container->get(GitHubClientInterface::class);
        assert($client instanceof GitHubClientInterface);

        $config = $container->get('config');

        $github   = is_array($config) && isset($config['github']) && is_array($config['github'])
            ? $config['github']
            : [];
        $packages = is_array($config) && isset($config['packages']) && is_array($config['packages'])
            ? $config['packages']
            : [];

        $ignoreRepos = [];
        if (isset($packages['ignoreRepos']) && is_array($packages['ignoreRepos'])) {
            foreach ($packages['ignoreRepos'] as $repository) {
                if (is_scalar($repository)) {
                    $ignoreRepos[] = (string) $repository;
                }
            }
        }

        return new PackageGenerator(
            $client,
            (string) ($packages['dataFile'] ?? 'data/packages.json'),
            (string) ($github['org'] ?? 'dotkernel'),
            $ignoreRepos,
            (bool) ($packages['includeArchived'] ?? true),
        );
    }
}
