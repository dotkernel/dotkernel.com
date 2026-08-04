<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Light\App\Service\GitHubClient;
use Psr\Container\ContainerInterface;

use function is_array;

class GitHubClientFactory
{
    public function __invoke(ContainerInterface $container): GitHubClient
    {
        $config = $container->get('config');
        if (! is_array($config)) {
            $config = [];
        }
        // the token is in `config/autoload/local.php`
        // if unauthenticated, the client has a lower call limit
        $github   = isset($config['github']) && is_array($config['github'])
            ? $config['github']
            : [];
        $packages = isset($config['packages']) && is_array($config['packages'])
            ? $config['packages']
            : [];

        return new GitHubClient(
            (string) ($github['authBearer'] ?? ''),
            (string) ($github['userAgent'] ?? 'dotkernel.com'),
            (int) ($packages['timeout'] ?? 10),
            (int) ($packages['connectTimeout'] ?? 5),
        );
    }
}
