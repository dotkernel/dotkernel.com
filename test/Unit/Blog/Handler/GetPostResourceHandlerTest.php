<?php

declare(strict_types=1);

namespace LightTest\Unit\Blog\Handler;

use Laminas\Diactoros\ServerRequest;
use Light\Blog\Entity\Category;
use Light\Blog\Entity\Post;
use Light\Blog\Enum\PostStatusEnum;
use Light\Blog\Handler\GetPostResourceHandler;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use LightTest\Unit\UnitTest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Message\ResponseInterface;

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
    private function handle(?Post $article): ResponseInterface
    {
        $categories = [$this->createStub(Category::class)];

        $postRepository = $this->createMock(PostRepository::class);
        $postRepository->expects($this->once())->method('getArticleResource')->willReturn($article);

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository->expects($this->once())->method('getCategories')->willReturn($categories);

        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->once())->method('render')->willReturn('<html lang="en"></html>');

        $handler = new GetPostResourceHandler($template, $postRepository, $categoryRepository);

        return $handler->handle(
            (new ServerRequest())->withAttribute('slug', 'a-slug')->withAttribute('categorySlug', 'a-category')
        );
    }
}
