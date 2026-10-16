<?php

declare(strict_types=1);

namespace LightTest\Unit\Blog\Handler;

use Laminas\Diactoros\ServerRequest;
use Light\Blog\Entity\Category;
use Light\Blog\Handler\GetCategoryCollectionHandler;
use Light\Blog\Repository\CategoryRepository;
use LightTest\Unit\UnitTest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;

class GetCategoryCollectionHandlerTest extends UnitTest
{
    /**
     * Visibility must not decide what /categories/ lists: only categories with published posts do.
     *
     * @throws Exception
     */
    public function testHandleListsCategoriesWithPublishedPostsRegardlessOfVisibility(): void
    {
        $hiddenWithPosts = new Category();
        $hiddenWithPosts->setVisibility(false);
        $categories = [$hiddenWithPosts];

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository->expects($this->once())
            ->method('getCategoriesWithPublishedPosts')
            ->willReturn($categories);
        $categoryRepository->expects($this->never())->method('getCategories');

        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->once())
            ->method('render')
            ->with('page::categories', ['categories' => $categories])
            ->willReturn('<html lang="en"></html>');

        $response = (new GetCategoryCollectionHandler($template, $categoryRepository))
            ->handle(new ServerRequest());

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testHandleRendersEmptyListWhenNoCategoryHasPublishedPosts(): void
    {
        $categoryRepository = $this->createStub(CategoryRepository::class);
        $categoryRepository->method('getCategoriesWithPublishedPosts')->willReturn([]);

        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->once())
            ->method('render')
            ->with('page::categories', ['categories' => []])
            ->willReturn('');

        $response = (new GetCategoryCollectionHandler($template, $categoryRepository))
            ->handle(new ServerRequest());

        $this->assertSame(200, $response->getStatusCode());
    }
}
