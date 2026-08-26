<?php

declare(strict_types=1);

namespace LightTest\Unit\Blog\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;
use Light\Blog\Entity\Category;
use Light\Blog\Entity\Post;
use Light\Blog\Enum\PostStatusEnum;
use Light\Blog\Handler\GetPostResourceHandler;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Light\Blog\Service\BlogServiceInterface;
use LightTest\Unit\UnitTest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Message\ResponseInterface;

use function file_put_contents;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

class GetPostResourceHandlerTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testHandleReturnsNotFoundWhenArticleDoesNotExist(): void
    {
        $response = $this->handle(null);

        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testHandleReturnsGoneWhenArticleIsArchived(): void
    {
        $article = $this->createStub(Post::class);
        $article->method('getStatus')->willReturn(PostStatusEnum::Archived);

        $response = $this->handle($article);

        $this->assertSame(410, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testHandleReturnsNotFoundWhenArticleIsNotPublished(): void
    {
        $article = $this->createStub(Post::class);
        $article->method('getStatus')->willReturn(PostStatusEnum::Draft);

        $response = $this->handle($article);

        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testHandleReturnsTheMarkdownFileWhenAcceptRequestsIt(): void
    {
        $markdownFile = sys_get_temp_dir() . '/' . uniqid('dk-article-', true) . '.md';
        file_put_contents($markdownFile, '# A slug');

        $blogService = $this->createMock(BlogServiceInterface::class);
        $blogService
            ->expects($this->once())
            ->method('resolveMarkdownFilePath')
            ->with('a-category', 'a-slug')
            ->willReturn($markdownFile);

        $postRepository     = $this->createMock(PostRepository::class);
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $postRepository->expects($this->never())->method('getArticleResource');
        $categoryRepository->expects($this->never())->method('getCategories');

        $handler = new GetPostResourceHandler(
            $this->createStub(TemplateRendererInterface::class),
            $postRepository,
            $categoryRepository,
            $blogService,
        );

        $response = $handler->handle(
            (new ServerRequest())
                ->withAttribute('slug', 'a-slug')
                ->withAttribute('categorySlug', 'a-category')
                ->withHeader('Accept', 'text/markdown')
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('text/markdown', $response->getHeaderLine('Content-Type'));
        $this->assertSame('# A slug', (string) $response->getBody());

        unlink($markdownFile);
    }

    /**
     * @throws Exception
     */
    public function testHandleFallsBackToHtmlWhenNoMarkdownFileExistsForTheRequestedArticle(): void
    {
        $article = $this->createStub(Post::class);
        $article->method('getStatus')->willReturn(PostStatusEnum::Draft);

        $response = $this->handle($article, withAccept: 'text/markdown');

        $this->assertStringContainsString('text/html', $response->getHeaderLine('Content-Type'));
    }

    /**
     * @throws Exception
     */
    private function handle(?Post $article, string $withAccept = ''): ResponseInterface
    {
        $categories = [$this->createStub(Category::class)];

        $postRepository = $this->createMock(PostRepository::class);
        $postRepository->expects($this->once())->method('getArticleResource')->willReturn($article);

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository->expects($this->once())->method('getCategories')->willReturn($categories);

        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->never())->method('render');

        $blogService = $this->createStub(BlogServiceInterface::class);
        $blogService->method('resolveMarkdownFilePath')->willReturn(null);
        $blogService->method('notFound')
            ->willReturn(new HtmlResponse('<html lang="en"></html>', StatusCodeInterface::STATUS_NOT_FOUND));
        $blogService->method('gone')
            ->willReturn(new HtmlResponse('<html lang="en"></html>', StatusCodeInterface::STATUS_GONE));

        $handler = new GetPostResourceHandler($template, $postRepository, $categoryRepository, $blogService);

        $request = (new ServerRequest())->withAttribute('slug', 'a-slug')->withAttribute('categorySlug', 'a-category');
        if ($withAccept !== '') {
            $request = $request->withHeader('Accept', $withAccept);
        }

        return $handler->handle($request);
    }
}
