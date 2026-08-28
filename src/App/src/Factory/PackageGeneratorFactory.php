<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Service\GitHubClientInterface;
use Light\App\Service\PackageGenerator;
use Psr\Container\ContainerInterface;

use function assert;
use function is_array;
use function is_scalar;
use function is_string;

class PackageGeneratorFactory
{
    public function __invoke(ContainerInterface $container): PackageGenerator
    {
        $client = $container->get(GitHubClientInterface::class);
        assert($client instanceof GitHubClientInterface);

        $config = $container->get('config');
        if (! is_array($config)) {
            $config = [];
        }
        $github   = isset($config['github']) && is_array($config['github'])
            ? $config['github']
            : [];
        $packages = isset($config['packages']) && is_array($config['packages'])
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

        $markdownFile = isset($packages['markdownFile']) && is_string($packages['markdownFile'])
            ? $packages['markdownFile']
            : null;

        return new PackageGenerator(
            $client,
            (string) ($packages['dataFile'] ?? 'data/dotkernel-packages.json'),
            (string) ($github['org'] ?? 'dotkernel'),
            $ignoreRepos,
            (bool) ($packages['includeArchived'] ?? true),
            $markdownFile,
            (string) ($config['application']['url'] ?? ''),
        );
    }
}
