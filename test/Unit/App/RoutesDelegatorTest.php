<?php

declare(strict_types=1);

namespace LightTest\Unit\App;

use Light\App\Handler\GetFeedViewHandler;
use Light\App\Handler\GetIndexViewHandler;
use Light\App\Handler\GetMarkdownArticleHandler;
use Light\App\Handler\GetPackagesViewHandler;
use Light\App\Handler\GetSitemapViewHandler;
use Light\App\RoutesDelegator;
use LightTest\Unit\UnitTest;
use Mezzio\Application;
use Mezzio\Router\Route;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class RoutesDelegatorTest extends UnitTest
{
    /** @var array<string, array{handler: mixed, name: string|null}> */
    private array $registeredRoutes = [];

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function testWillRegisterAllRoutes(): void
    {
        $container = $this->createStub(ContainerInterface::class);
        $app       = $this->createStub(Application::class);

        $app->method('get')->willReturnCallback(function (string $uri, mixed $handler, ?string $name = null) {
            $this->registeredRoutes[$uri] = ['handler' => $handler, 'name' => $name];

            return $this->createStub(Route::class);
        });

        $application = (new RoutesDelegator())($container, '', fn () => $app);

        $this->assertSame($app, $application);

        $this->assertSame([GetIndexViewHandler::class], $this->registeredRoutes['/']['handler']);
        $this->assertSame('app::index', $this->registeredRoutes['/']['name']);

        $this->assertSame([GetFeedViewHandler::class], $this->registeredRoutes['/feed/']['handler']);
        $this->assertSame('app::feed', $this->registeredRoutes['/feed/']['name']);

        $this->assertSame([GetSitemapViewHandler::class], $this->registeredRoutes['/sitemap/']['handler']);
        $this->assertSame('app::sitemap', $this->registeredRoutes['/sitemap/']['name']);

        $this->assertSame(
            [GetMarkdownArticleHandler::class],
            $this->registeredRoutes['/{categorySlug}/{slug}.md']['handler']
        );
        $this->assertSame('app::markdown-article', $this->registeredRoutes['/{categorySlug}/{slug}.md']['name']);

        $this->assertSame(
            [GetPackagesViewHandler::class],
            $this->registeredRoutes['/dotkernel-packages-oss-lifecycle/']['handler']
        );
        $this->assertSame(
            GetPackagesViewHandler::TEMPLATE,
            $this->registeredRoutes['/dotkernel-packages-oss-lifecycle/']['name']
        );
    }
}
