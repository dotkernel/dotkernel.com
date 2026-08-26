<?php

declare(strict_types=1);

namespace LightTest\Unit\Page\Factory;

use Light\Page\Factory\PageServiceFactory;
use Light\Page\Service\PageService;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerInterface;
use ReflectionProperty;

class PageServiceFactoryTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testInvokeReturnsThePageService(): void
    {
        $service = (new PageServiceFactory())(
            $this->createContainer('/some/path/md-pages'),
            PageService::class
        );

        // The declared return type is the interface; what matters is which implementation it builds.
        $this->assertInstanceOf(PageService::class, $service);
        $this->assertSame(
            '/some/path/md-pages',
            (new ReflectionProperty(PageService::class, 'mdPagesPath'))->getValue($service)
        );
    }

    /**
     * @throws Exception
     */
    public function testInvokeBuildsAFreshServiceEachTime(): void
    {
        $factory   = new PageServiceFactory();
        $container = $this->createContainer('/some/path/md-pages');

        $this->assertNotSame(
            $factory($container, PageService::class),
            $factory($container, PageService::class)
        );
    }

    /**
     * @throws Exception
     */
    private function createContainer(string $pagesDir): ContainerInterface
    {
        $container = $this->createStub(ContainerInterface::class);
        $container->method('get')->willReturn([
            'llms' => ['pagesDir' => $pagesDir],
        ]);

        return $container;
    }
}
