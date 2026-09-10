<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Middleware;

use Light\App\Middleware\ServiceDocLinkMiddleware;
use LightTest\Unit\UnitTest;
use Mezzio\Router\RouteResult;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ServiceDocLinkMiddlewareTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testAddsServiceDocLinkForRouteWithDedicatedDocs(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getHeaderLine')->with('Link')->willReturn('');
        $response
            ->expects($this->once())
            ->method('withHeader')
            ->with('Link', '<https://docs.dotkernel.org/api-documentation/>; rel="service-doc"')
            ->willReturn($response);

        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($response);

        $routeResult = $this->createStub(RouteResult::class);
        $routeResult->method('isSuccess')->willReturn(true);
        $routeResult->method('getMatchedRouteName')->willReturn('page::api');

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn($routeResult);

        $middleware = new ServiceDocLinkMiddleware();
        $result     = $middleware->process($request, $handler);

        $this->assertSame($response, $result);
    }

    /**
     * @throws Exception
     */
    public function testCombinesWithAnExistingLinkHeaderIntoOneLine(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getHeaderLine')->with('Link')->willReturn(
            '</llms.txt>; rel="llms-txt", </llms-full.txt>; rel="llms-full-txt"'
        );
        $response
            ->expects($this->once())
            ->method('withHeader')
            ->with(
                'Link',
                '</llms.txt>; rel="llms-txt", </llms-full.txt>; rel="llms-full-txt", '
                    . '<https://docs.dotkernel.org/>; rel="service-doc"'
            )
            ->willReturn($response);

        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($response);

        $routeResult = $this->createStub(RouteResult::class);
        $routeResult->method('isSuccess')->willReturn(true);
        $routeResult->method('getMatchedRouteName')->willReturn('app::index');

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn($routeResult);

        $middleware = new ServiceDocLinkMiddleware();
        $result     = $middleware->process($request, $handler);

        $this->assertSame($response, $result);
    }

    /**
     * @throws Exception
     */
    public function testLeavesResponseUntouchedForRouteWithNoDedicatedDocs(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->never())->method('withHeader');

        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($response);

        $routeResult = $this->createStub(RouteResult::class);
        $routeResult->method('isSuccess')->willReturn(true);
        $routeResult->method('getMatchedRouteName')->willReturn('page::dotboost');

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn($routeResult);

        $middleware = new ServiceDocLinkMiddleware();
        $result     = $middleware->process($request, $handler);

        $this->assertSame($response, $result);
    }

    /**
     * @throws Exception
     */
    public function testLeavesResponseUntouchedWhenRoutingFailed(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->never())->method('withHeader');

        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($response);

        $routeResult = $this->createStub(RouteResult::class);
        $routeResult->method('isSuccess')->willReturn(false);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn($routeResult);

        $middleware = new ServiceDocLinkMiddleware();
        $result     = $middleware->process($request, $handler);

        $this->assertSame($response, $result);
    }
}
