<?php

declare(strict_types=1);

use Twig\Extra\Markdown\MarkdownExtension;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

return [
    'templates' => [
        'extension' => 'html.twig',
    ],
    'twig'      => [
        'assets_url'      => '/',
        'assets_version'  => null,
        'autoescape'      => 'html',
        'auto_reload'     => true,
        'cache_dir'       => 'data/cache/twig',
        'extensions'      => [
            MarkdownExtension::class,
        ],
        'globals'         => [],
        'optimizations'   => -1,
        'runtime_loaders' => [
            RuntimeLoaderInterface::class,
        ],
        'timezone'        => 'UTC',
    ],
];
