<?php

declare(strict_types=1);

namespace LightTest\Unit\App;

use Laminas\Diactoros\Response\RedirectResponse;
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
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

use function sprintf;

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

        $this->assertArrayHasKey('/{wpPath:wp-.*}', $this->registeredRoutes);
        $this->assertArrayHasKey('/{first}', $this->registeredRoutes);
        $this->assertArrayHasKey('/{first}/{second}', $this->registeredRoutes);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function testWpPrefixedPathRouteRedirectsToHomepage(): void
    {
        $handler = $this->captureRouteHandler('/{wpPath:wp-.*}');

        $response = $handler();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('/', $response->getHeaderLine('Location'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function testFirstSegmentCatchAllAppendsTrailingSlash(): void
    {
        $handler = $this->captureRouteHandler('/{first}');
        $request = $this->createRequestForUri('/some-page');

        $response = $handler($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('/some-page/', $response->getHeaderLine('Location'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function testTwoSegmentCatchAllAppendsTrailingSlash(): void
    {
        $handler = $this->captureRouteHandler('/{first}/{second}');
        $request = $this->createRequestForUri('/some-category/some-slug');

        $response = $handler($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('/some-category/some-slug/', $response->getHeaderLine('Location'));
    }

    /**
     * @throws Exception
     */
    private function createRequestForUri(string $uri): ServerRequestInterface
    {
        $uriStub = $this->createStub(UriInterface::class);
        $uriStub->method('__toString')->willReturn($uri);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uriStub);

        return $request;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    private function captureRouteHandler(string $routeUri): callable
    {
        $container = $this->createStub(ContainerInterface::class);
        $app       = $this->createStub(Application::class);
        $captured  = null;

        $app->method('get')->willReturnCallback(function (...$args) use ($routeUri, &$captured) {
            if ($args[0] === $routeUri) {
                $captured = $args[1];
            }

            return $this->createStub(Route::class);
        });

        (new RoutesDelegator())($container, '', fn () => $app);

        $this->assertIsCallable($captured, sprintf('No callable handler was registered for route "%s".', $routeUri));

        return $captured;
    }
}
