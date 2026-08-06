<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\ServerRequest;
use Light\App\Handler\GetMarkdownArticleHandler;
use Light\Blog\Entity\Category;
use Light\Blog\Repository\CategoryRepository;
use LightTest\Unit\UnitTest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;

use function bin2hex;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function random_bytes;
use function rmdir;
use function scandir;
use function sprintf;
use function sys_get_temp_dir;
use function unlink;

use const DIRECTORY_SEPARATOR;

class GetMarkdownArticleHandlerTest extends UnitTest
{
    private string $articlesPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->articlesPath = sprintf(
            '%s%slight-articles-%s',
            sys_get_temp_dir(),
            DIRECTORY_SEPARATOR,
            bin2hex(random_bytes(8)),
        );

        mkdir($this->articlesPath . DIRECTORY_SEPARATOR . 'news', 0775, true);
        file_put_contents(
            $this->articlesPath . DIRECTORY_SEPARATOR . 'news' . DIRECTORY_SEPARATOR . 'a-post.md',
            '# A post'
        );
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->articlesPath);

        parent::tearDown();
    }

    public function testHandleReturnsTheMarkdownFileContents(): void
    {
        $response = $this->createHandler()->handle($this->request('news', 'a-post'));

        $this->assertInstanceOf(TextResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/markdown; charset=utf-8', $response->getHeaderLine('Content-Type'));
        $this->assertSame('# A post', (string) $response->getBody());
    }

    /**
     * @throws Exception
     */
    public function testHandleReturns404WhenTheCategorySlugIsMissing(): void
    {
        $this->assertNotFound($this->createHandler()->handle($this->request('', 'a-post')));
    }

    /**
     * @throws Exception
     */
    public function testHandleReturns404WhenTheSlugIsMissing(): void
    {
        $this->assertNotFound($this->createHandler()->handle($this->request('news', '')));
    }

    /**
     * @throws Exception
     */
    public function testHandleReturns404WhenTheFileDoesNotExist(): void
    {
        $this->assertNotFound($this->createHandler()->handle($this->request('news', 'missing-post')));
    }

    /**
     * @throws Exception
     */
    public function testHandleReturns404WhenTheCategoryDoesNotExist(): void
    {
        $this->assertNotFound($this->createHandler()->handle($this->request('unknown-category', 'a-post')));
    }

    /**
     * A slug containing ".." must never escape the configured articles directory.
     *
     * @throws Exception
     */
    public function testHandleReturns404WhenTheSlugAttemptsToEscapeTheArticlesDirectory(): void
    {
        $this->assertNotFound($this->createHandler()->handle($this->request('news', '../../etc/passwd')));
    }

    /**
     * @throws Exception
     */
    private function createHandler(): GetMarkdownArticleHandler
    {
        $categoryRepository = $this->createStub(CategoryRepository::class);
        $categoryRepository->method('getCategories')->willReturn([$this->createStub(Category::class)]);

        $template = $this->createStub(TemplateRendererInterface::class);
        $template->method('render')->willReturn('<html lang="en">not found</html>');

        return new GetMarkdownArticleHandler($template, $categoryRepository, $this->articlesPath);
    }

    private function request(string $categorySlug, string $slug): ServerRequest
    {
        return (new ServerRequest())
            ->withAttribute('categorySlug', $categorySlug)
            ->withAttribute('slug', $slug);
    }

    private function assertNotFound(mixed $response): void
    {
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertSame(404, $response->getStatusCode());
    }

    private function removeDirectory(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $items = scandir($path);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $itemPath = $path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($itemPath)) {
                $this->removeDirectory($itemPath);
            } else {
                unlink($itemPath);
            }
        }

        rmdir($path);
    }
}
