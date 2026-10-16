<?php

declare(strict_types=1);

namespace LightTest\Unit\Blog\Handler;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;
use Light\Blog\Entity\Category;
use Light\Blog\Handler\GetCategoryResourceHandler;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Service\BlogServiceInterface;
use LightTest\Unit\UnitTest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Message\ResponseInterface;

class GetCategoryResourceHandlerTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testHandleReturnsNotFoundWhenCategoryDoesNotExist(): void
    {
        $response = $this->handle(null);

        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testHandleReturnsGoneWhenCategoryIsNotVisibleAndHasNoPublishedPosts(): void
    {
        $category = $this->createStub(Category::class);
        $category->method('isVisible')->willReturn(false);
        $category->method('getPublishedPostsCount')->willReturn(0);

        $response = $this->handle($category);

        $this->assertSame(410, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testHandleReturnsGoneWhenVisibleCategoryHasNoPublishedPosts(): void
    {
        $category = $this->createStub(Category::class);
        $category->method('isVisible')->willReturn(true);
        $category->method('getPublishedPostsCount')->willReturn(0);

        $response = $this->handle($category);

        $this->assertSame(410, $response->getStatusCode());
    }

    /**
     * A hidden category is listed on /categories/ when it has published posts, so its page must be served.
     *
     * @throws Exception
     */
    public function testHandleRendersHiddenCategoryThatHasPublishedPosts(): void
    {
        $category = $this->createStub(Category::class);
        $category->method('isVisible')->willReturn(false);
        $category->method('getPublishedPostsCount')->willReturn(2);

        $this->assertSame(200, $this->handleRendering($category)->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testHandleRendersVisibleCategoryThatHasPublishedPosts(): void
    {
        $category = $this->createStub(Category::class);
        $category->method('isVisible')->willReturn(true);
        $category->method('getPublishedPostsCount')->willReturn(1);

        $this->assertSame(200, $this->handleRendering($category)->getStatusCode());
    }

    /**
     * @throws Exception
     */
    private function handleRendering(Category $category): ResponseInterface
    {
        $query = $this->createStub(Query::class);
        $query->method('getResult')->willReturn([]);

        $paginator = $this->createStub(DoctrinePaginator::class);
        $paginator->method('count')->willReturn(0);
        $paginator->method('getQuery')->willReturn($query);

        $categoryRepository = $this->createStub(CategoryRepository::class);
        $categoryRepository->method('getCategories')->willReturn([]);
        $categoryRepository->method('getCategoryResource')->willReturn($category);
        $categoryRepository->method('getCategoryPost')->willReturn($paginator);

        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->once())->method('render')
            ->with('page::category-resource', $this->anything())
            ->willReturn('<html lang="en"></html>');

        $blogService = $this->createMock(BlogServiceInterface::class);
        $blogService->expects($this->never())->method('gone');
        $blogService->expects($this->never())->method('notFound');

        return (new GetCategoryResourceHandler($template, $categoryRepository, $blogService))
            ->handle((new ServerRequest())->withAttribute('slug', 'a-category'));
    }

    /**
     * @throws Exception
     */
    private function handle(?Category $category): ResponseInterface
    {
        $categories = [$this->createStub(Category::class)];

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository->expects($this->once())->method('getCategories')->willReturn($categories);
        $categoryRepository->expects($this->once())->method('getCategoryResource')->willReturn($category);

        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->never())->method('render');

        $blogService = $this->createMock(BlogServiceInterface::class);
        if ($category === null) {
            $blogService->expects($this->once())->method('notFound')->with($categories)
                ->willReturn(new HtmlResponse('<html lang="en"></html>', StatusCodeInterface::STATUS_NOT_FOUND));
            $blogService->expects($this->never())->method('gone');
        } else {
            $blogService->expects($this->once())->method('gone')->with($categories)
                ->willReturn(new HtmlResponse('<html lang="en"></html>', StatusCodeInterface::STATUS_GONE));
            $blogService->expects($this->never())->method('notFound');
        }

        $handler = new GetCategoryResourceHandler($template, $categoryRepository, $blogService);

        return $handler->handle((new ServerRequest())->withAttribute('slug', 'a-category'));
    }
}
