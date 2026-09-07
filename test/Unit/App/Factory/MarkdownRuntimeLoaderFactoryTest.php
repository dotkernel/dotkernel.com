<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Factory;

use Light\App\Factory\MarkdownRuntimeLoaderFactory;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerInterface;
use Twig\Extra\Markdown\MarkdownRuntime;

class MarkdownRuntimeLoaderFactoryTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testInvokeReturnsALoaderThatResolvesTheMarkdownRuntime(): void
    {
        $loader = (new MarkdownRuntimeLoaderFactory())($this->createStub(ContainerInterface::class));

        $this->assertInstanceOf(MarkdownRuntime::class, $loader->load(MarkdownRuntime::class));
        $this->assertNull($loader->load('SomeUnknownRuntime'));
    }
}
