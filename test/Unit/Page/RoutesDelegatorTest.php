<?php

declare(strict_types=1);

namespace LightTest\Unit\Page;

use Light\Page\Handler\GetPageViewHandler;
use Light\Page\RoutesDelegator;
use LightTest\Unit\UnitTest;
use Mezzio\Application;
use Mezzio\Router\Route;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function sprintf;

class RoutesDelegatorTest extends UnitTest
{
    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function testWillInvoke(): void
    {
        $moduleName   = 'test_module_name';
        $routeName    = 'test_route_name';
        $routeUri     = sprintf('/%s/', $routeName);
        $templateName = sprintf('%s::%s', $moduleName, $routeName);

        $container = $this->createStub(ContainerInterface::class);
        $app       = $this->createMock(Application::class);

        $app->method('get')->willReturn($this->createStub(Route::class));
        $app
            ->expects($this->exactly(1))
            ->method('get')
            ->willReturnCallback(function (...$args) use ($routeUri, $templateName) {
                $this->assertSame($routeUri, $args[0]);
                $this->assertSame([GetPageViewHandler::class], [$args[1]]);
                $this->assertSame($templateName, $args[2]);
            });

        $container->method('get')->willReturn([
            'routes' => [
                $moduleName => [
                    $routeName => $routeName,
                ],
            ],
        ]);

        $application  = (new RoutesDelegator())(
            $container,
            '',
            $callback = function () use ($app) {
                return $app;
            }
        );

        $this->assertContainsOnlyInstancesOf(Application::class, [$application]);
    }
}
