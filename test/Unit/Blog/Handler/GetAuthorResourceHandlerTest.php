<?php

declare(strict_types=1);

namespace LightTest\Unit\Blog\Handler;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;
use Light\Blog\Entity\Author;
use Light\Blog\Entity\Category;
use Light\Blog\Handler\GetAuthorResourceHandler;
use Light\Blog\Repository\AuthorRepository;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Light\Blog\Service\BlogServiceInterface;
use LightTest\Unit\UnitTest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;

class GetAuthorResourceHandlerTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testHandleRendersTheAuthorResourceTemplateWhenAuthorExists(): void
    {
        $author     = $this->createStub(Author::class);
        $categories = [$this->createStub(Category::class)];

        $query = $this->createStub(Query::class);
        $query->method('getResult')->willReturn([]);

        $paginator = $this->createStub(DoctrinePaginator::class);
        $paginator->method('count')->willReturn(0);
        $paginator->method('getQuery')->willReturn($query);

        $authorRepository = $this->createMock(AuthorRepository::class);
        $authorRepository->expects($this->once())->method('getAuthorResource')->willReturn($author);
        $authorRepository->expects($this->never())->method('getAuthorsWithPublishedPosts');

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository->expects($this->once())->method('getCategories')->willReturn($categories);

        $postRepository = $this->createMock(PostRepository::class);
        $postRepository->expects($this->exactly(2))->method('getArticleByAuthor')->willReturn($paginator);

        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->once())->method('render')
            ->willReturnCallback(function (string $name, mixed $parameters = []) use ($author, $categories): string {
                $this->assertSame('page::author-resource', $name);
                $this->assertIsArray($parameters);
                $this->assertSame($author, $parameters['author']);
                $this->assertSame($categories, $parameters['categories']);

                return '<html lang="en"></html>';
            });

        $blogService = $this->createMock(BlogServiceInterface::class);
        $blogService->expects($this->never())->method('authorNotFound');

        $handler  = new GetAuthorResourceHandler(
            $template,
            $authorRepository,
            $postRepository,
            $categoryRepository,
            $blogService,
        );
        $response = $handler->handle((new ServerRequest())->withAttribute('slug', 'gabi'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('<html lang="en"></html>', (string) $response->getBody());
    }

    /**
     * @throws Exception
     */
    public function testHandleReturnsNotFoundWhenAuthorDoesNotExist(): void
    {
        $authors = [$this->createStub(Author::class)];

        $authorRepository = $this->createMock(AuthorRepository::class);
        $authorRepository->expects($this->once())->method('getAuthorResource')->willReturn(null);
        $authorRepository->expects($this->once())->method('getAuthorsWithPublishedPosts')->willReturn($authors);

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository->expects($this->never())->method('getCategories');

        $postRepository = $this->createMock(PostRepository::class);
        $postRepository->expects($this->never())->method('getArticleByAuthor');

        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->never())->method('render');

        $blogService = $this->createMock(BlogServiceInterface::class);
        $blogService->expects($this->once())
            ->method('authorNotFound')
            ->with($authors)
            ->willReturn(new HtmlResponse('<html lang="en"></html>', StatusCodeInterface::STATUS_NOT_FOUND));

        $handler  = new GetAuthorResourceHandler(
            $template,
            $authorRepository,
            $postRepository,
            $categoryRepository,
            $blogService,
        );
        $response = $handler->handle((new ServerRequest())->withAttribute('slug', 'adminxx'));

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('<html lang="en"></html>', (string) $response->getBody());
    }
}
