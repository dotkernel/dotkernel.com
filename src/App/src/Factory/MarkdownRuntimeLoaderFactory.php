<?php

declare(strict_types=1);

namespace Light\App\Factory;

use League\CommonMark\GithubFlavoredMarkdownConverter;
use Psr\Container\ContainerInterface;
use Twig\Extra\Markdown\LeagueMarkdown;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

class MarkdownRuntimeLoaderFactory
{
    public function __invoke(ContainerInterface $container): RuntimeLoaderInterface
    {
        return new FactoryRuntimeLoader([
            MarkdownRuntime::class => static fn (): MarkdownRuntime => new MarkdownRuntime(
                new LeagueMarkdown(new GithubFlavoredMarkdownConverter())
            ),
        ]);
    }
}
