<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Factory;

use Light\App\Factory\MarkdownExtensionFactory;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerInterface;
use Twig\TwigFilter;

use function array_map;

class MarkdownExtensionFactoryTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testInvokeReturnsAMarkdownExtensionRegisteringTheMarkdownFilter(): void
    {
        $extension = (new MarkdownExtensionFactory())($this->createStub(ContainerInterface::class));

        $filterNames = array_map(
            static fn (TwigFilter $filter): string => $filter->getName(),
            $extension->getFilters()
        );

        $this->assertContains('markdown_to_html', $filterNames);
    }
}
