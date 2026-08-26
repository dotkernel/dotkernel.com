<?php

declare(strict_types=1);

namespace LightTest\Unit\Page\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Light\Blog\Entity\Category;
use Light\Blog\Entity\Post;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Light\Page\Handler\GetPageViewHandler;
use Light\Page\Service\PageServiceInterface;
use LightTest\Unit\UnitTest;
use Mezzio\Router\RouteResult;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Message\ServerRequestInterface;

use function file_put_contents;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

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
    public function testHandleReturnsTheMarkdownFileWhenAcceptRequestsIt(): void
    {
        $markdownFile = sys_get_temp_dir() . '/' . uniqid('dk-page-', true) . '.md';
        file_put_contents($markdownFile, '# Dotkernel API');

        $pageService = $this->createMock(PageServiceInterface::class);
        $pageService
            ->expects($this->once())
            ->method('resolveMarkdownFilePath')
            ->with('page::api')
            ->willReturn($markdownFile);

        $postRepository = $this->createMock(PostRepository::class);
        $postRepository->expects($this->never())->method('getRecentPosts');

        $response = $this->createHandler(postRepository: $postRepository, pageService: $pageService)
            ->handle($this->createRequest('page::api', accept: 'text/markdown'));

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertStringContainsString('text/markdown', $response->getHeaderLine('Content-Type'));
        $this->assertSame('# Dotkernel API', (string) $response->getBody());

        unlink($markdownFile);
    }

    /**
     * @throws Exception
     */
    public function testHandleFallsBackToHtmlWhenNoMarkdownFileExistsForTheRequestedPage(): void
    {
        $pageService = $this->createStub(PageServiceInterface::class);
        $pageService->method('resolveMarkdownFilePath')->willReturn(null);

        $response = $this->createHandler(pageService: $pageService)
            ->handle($this->createRequest('page::contact', accept: 'text/markdown'));

        $this->assertStringContainsString('text/html', $response->getHeaderLine('Content-Type'));
    }

    /**
     * @throws Exception
     */
    private function createHandler(
        ?TemplateRendererInterface $template = null,
        ?CategoryRepository $categoryRepository = null,
        ?PostRepository $postRepository = null,
        ?PageServiceInterface $pageService = null,
    ): GetPageViewHandler {
        if (! $template instanceof TemplateRendererInterface) {
            $template = $this->createStub(TemplateRendererInterface::class);
            $template->method('render')->willReturn('');
        }

        if (! $pageService instanceof PageServiceInterface) {
            $pageService = $this->createStub(PageServiceInterface::class);
            $pageService->method('resolveMarkdownFilePath')->willReturn(null);
        }

        return new GetPageViewHandler(
            $template,
            $categoryRepository ?? $this->createStub(CategoryRepository::class),
            $postRepository ?? $this->createStub(PostRepository::class),
            $pageService,
        );
    }

    /**
     * @throws Exception
     */
    private function createRequest(string $routeName, string $accept = ''): ServerRequestInterface
    {
        $routeResult = $this->createStub(RouteResult::class);
        $routeResult->method('getMatchedRouteName')->willReturn($routeName);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn($routeResult);
        $request->method('getHeaderLine')->willReturn($accept);

        return $request;
    }
}
