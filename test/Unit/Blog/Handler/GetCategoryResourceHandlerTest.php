<?php

declare(strict_types=1);

namespace LightTest\Unit\Blog\Handler;

use Laminas\Diactoros\ServerRequest;
use Light\Blog\Entity\Category;
use Light\Blog\Handler\GetCategoryResourceHandler;
use Light\Blog\Repository\CategoryRepository;
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
    public function testHandleReturnsGoneWhenCategoryIsNotVisible(): void
    {
        $category = $this->createStub(Category::class);
        $category->method('isVisible')->willReturn(false);

        $response = $this->handle($category);

        $this->assertSame(410, $response->getStatusCode());
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
        $template->expects($this->once())->method('render')->willReturn('<html lang="en"></html>');

        $handler = new GetCategoryResourceHandler($template, $categoryRepository);

        return $handler->handle((new ServerRequest())->withAttribute('slug', 'a-category'));
    }
}
