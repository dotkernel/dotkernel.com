<?php

declare(strict_types=1);

namespace LightTest\Unit\Page\Factory;

use Light\Page\Factory\PageServiceFactory;
use Light\Page\Service\PageService;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerInterface;

class PageServiceFactoryTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testInvokeReturnsThePageService(): void
    {
        $service = (new PageServiceFactory())(
            $this->createStub(ContainerInterface::class),
            PageService::class
        );

        // The declared return type is the interface; what matters is which implementation it builds.
        $this->assertInstanceOf(PageService::class, $service);
    }

    /**
     * @throws Exception
     */
    public function testInvokeBuildsAFreshServiceEachTime(): void
    {
        $factory   = new PageServiceFactory();
        $container = $this->createStub(ContainerInterface::class);

        $this->assertNotSame(
            $factory($container, PageService::class),
            $factory($container, PageService::class)
        );
    }
}
