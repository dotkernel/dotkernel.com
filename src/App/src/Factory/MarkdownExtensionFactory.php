<?php

declare(strict_types=1);

namespace Light\App\Factory;

use Psr\Container\ContainerInterface;
use Twig\Extra\Markdown\MarkdownExtension;

class MarkdownExtensionFactory
{
    public function __invoke(ContainerInterface $container): MarkdownExtension
    {
        return new MarkdownExtension();
    }
}
