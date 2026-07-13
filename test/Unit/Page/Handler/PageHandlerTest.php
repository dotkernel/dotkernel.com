<?php

declare(strict_types=1);

namespace LightTest\Unit\Page\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Light\Page\Handler\GetPageViewHandler;
use Mezzio\Router\RouteResult;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PageHandlerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testWillInstantiate(): void
    {
        $handler = $this->createStub(GetPageViewHandler::class);

        $this->assertContainsOnlyInstancesOf(RequestHandlerInterface::class, [$handler]);
    }

    /**
     * @throws Exception
     */
    public function testHandle(): void
    {
        $routeName          = 'test_route_name';
        $request            = $this->createStub(ServerRequestInterface::class);
        $template           = $this->createStub(TemplateRendererInterface::class);
        $routeResult        = $this->createStub(RouteResult::class);
        $postRepository     = $this->createStub(PostRepository::class);
        $categoryRepository = $this->createStub(CategoryRepository::class);

        $postRepository
            ->method('getRecentPosts')
            ->willReturn([]);

        $categoryRepository
            ->method('getCategories')
            ->willReturn([]);

        $routeResult
            ->method('getMatchedRouteName')
            ->willReturn($routeName);

        $request
            ->method('getAttribute')
            ->willReturn($routeResult);

        $template
            ->method('render')
            ->willReturn('<p>' . $routeName . '</p>');

        $handler = new GetPageViewHandler($template, $categoryRepository, $postRepository);

        $response = $handler->handle($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertSame('<p>' . $routeName . '</p>', $response->getBody()->getContents());
    }
}
