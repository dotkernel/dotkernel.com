<?php

declare(strict_types=1);

namespace LightTest\Unit\Page\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Light\Blog\Entity\Category;
use Light\Blog\Entity\Post;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Light\Page\Handler\GetPageViewHandler;
use LightTest\Unit\UnitTest;
use Mezzio\Router\RouteResult;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Message\ServerRequestInterface;

class GetPageViewHandlerTest extends UnitTest
{
    /**
     * The matched route name doubles as the template name, which is what lets a page be added
     * with a config entry and a template and no handler of its own.
     *
     * @throws Exception
     */
    public function testHandleRendersTheTemplateNamedByTheMatchedRoute(): void
    {
        $template = $this->createMock(TemplateRendererInterface::class);
        $template
            ->expects($this->once())
            ->method('render')
            ->with('page::api', $this->anything())
            ->willReturn('<h1>Dotkernel API</h1>');

        $response = $this->createHandler($template)->handle($this->createRequest('page::api'));

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertSame('<h1>Dotkernel API</h1>', (string) $response->getBody());
    }

    /**
     * @throws Exception
     */
    public function testHandleReturnsAnHtmlResponse(): void
    {
        $response = $this->createHandler()->handle($this->createRequest('page::api'));

        $this->assertStringContainsString('text/html', $response->getHeaderLine('Content-Type'));
    }

    /**
     * @throws Exception
     */
    public function testHandlePassesTheThreeMostRecentPostsToTheTemplate(): void
    {
        $posts = [new Post(), new Post(), new Post()];

        $postRepository = $this->createMock(PostRepository::class);
        $postRepository
            ->expects($this->once())
            ->method('getRecentPosts')
            ->with(3)
            ->willReturn($posts);

        $template = $this->createMock(TemplateRendererInterface::class);
        $template
            ->expects($this->once())
            ->method('render')
            ->with('page::light', $this->callback(
                fn (array $params): bool => $params['posts'] === $posts
            ))
            ->willReturn('');

        $this->createHandler($template, postRepository: $postRepository)
            ->handle($this->createRequest('page::light'));
    }

    /**
     * @throws Exception
     */
    public function testHandlePassesTheCategoriesToTheTemplate(): void
    {
        $categories = [new Category()];

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository
            ->expects($this->once())
            ->method('getCategories')
            ->willReturn($categories);

        $template = $this->createMock(TemplateRendererInterface::class);
        $template
            ->expects($this->once())
            ->method('render')
            ->with('page::queue', $this->callback(
                fn (array $params): bool => $params['categories'] === $categories
            ))
            ->willReturn('');

        $this->createHandler($template, categoryRepository: $categoryRepository)
            ->handle($this->createRequest('page::queue'));
    }

    /**
     * @throws Exception
     */
    private function createHandler(
        ?TemplateRendererInterface $template = null,
        ?CategoryRepository $categoryRepository = null,
        ?PostRepository $postRepository = null,
    ): GetPageViewHandler {
        if (! $template instanceof TemplateRendererInterface) {
            $template = $this->createStub(TemplateRendererInterface::class);
            $template->method('render')->willReturn('');
        }

        return new GetPageViewHandler(
            $template,
            $categoryRepository ?? $this->createStub(CategoryRepository::class),
            $postRepository ?? $this->createStub(PostRepository::class),
        );
    }

    /**
     * @throws Exception
     */
    private function createRequest(string $routeName): ServerRequestInterface
    {
        $routeResult = $this->createStub(RouteResult::class);
        $routeResult->method('getMatchedRouteName')->willReturn($routeName);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn($routeResult);

        return $request;
    }
}
