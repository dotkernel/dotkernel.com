<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Middleware;

use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Uri;
use Light\App\Middleware\TrailingSlashMiddleware;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TrailingSlashMiddlewareTest extends UnitTest
{
    private TrailingSlashMiddleware $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new TrailingSlashMiddleware();
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function redirectingUrisProvider(): array
    {
        return [
            'no query or fragment' => ['/about', '/about/'],
            'query string'         => ['/about?x=1', '/about/?x=1'],
            'fragment'             => ['/about#section', '/about/#section'],
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('redirectingUrisProvider')]
    public function testRedirectsGetRequestMissingTrailingSlash(string $requestUri, string $expectedLocation): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn(new Uri($requestUri));

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $response = $this->middleware->process($request, $handler);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame($expectedLocation, $response->getHeaderLine('Location'));
    }

    /**
     * @throws Exception
     */
    public function testRedirectsHeadRequestMissingTrailingSlash(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('HEAD');
        $request->method('getUri')->willReturn(new Uri('/about'));

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $response = $this->middleware->process($request, $handler);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('/about/', $response->getHeaderLine('Location'));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function passthroughProvider(): array
    {
        return [
            'root'                   => ['GET', '/'],
            'already has trailing /' => ['GET', '/blog/'],
            'markdown-style route'   => ['GET', '/category/article.md'],
            'dotted last segment'    => ['GET', '/foo.bar'],
            'double trailing slash'  => ['GET', '/foo//'],
            'non-GET/HEAD method'    => ['POST', '/about'],
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('passthroughProvider')]
    public function testPassesThroughWhenNoRedirectNeeded(string $method, string $path): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn($method);
        $request->method('getUri')->willReturn(new Uri($path));

        $expectedResponse = $this->createStub(ResponseInterface::class);
        $handler          = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())->method('handle')->with($request)->willReturn($expectedResponse);

        $response = $this->middleware->process($request, $handler);

        $this->assertSame($expectedResponse, $response);
    }
}
