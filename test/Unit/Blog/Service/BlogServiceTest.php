<?php

declare(strict_types=1);

namespace LightTest\Unit\Blog\Service;

use Light\Blog\Entity\Author;
use Light\Blog\Entity\Category;
use Light\Blog\Service\BlogService;
use Light\Blog\Service\BlogServiceInterface;
use LightTest\Unit\UnitTest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;

use function file_put_contents;
use function mkdir;
use function rmdir;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

class BlogServiceTest extends UnitTest
{
    public function testWillInstantiate(): void
    {
        $service = new BlogService($this->createStub(TemplateRendererInterface::class), '');

        $this->assertContainsOnlyInstancesOf(BlogServiceInterface::class, [$service]);
    }

    /**
     * @throws Exception
     */
    public function testNotFoundRendersThe404TemplateWithTheCategories(): void
    {
        $categories = [$this->createStub(Category::class)];

        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->once())->method('render')
            ->with('error::404', ['categories' => $categories])
            ->willReturn('<html lang="en"></html>');

        $response = (new BlogService($template, ''))->notFound($categories);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('<html lang="en"></html>', (string) $response->getBody());
    }

    /**
     * @throws Exception
     */
    public function testGoneRendersThe410TemplateWithTheCategories(): void
    {
        $categories = [$this->createStub(Category::class)];

        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->once())->method('render')
            ->with('error::410', ['categories' => $categories])
            ->willReturn('<html lang="en"></html>');

        $response = (new BlogService($template, ''))->gone($categories);

        $this->assertSame(410, $response->getStatusCode());
        $this->assertSame('<html lang="en"></html>', (string) $response->getBody());
    }

    /**
     * @throws Exception
     */
    public function testAuthorNotFoundRendersThe404TemplateWithTheAuthors(): void
    {
        $authors = [$this->createStub(Author::class)];

        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->once())->method('render')
            ->with('error::404', ['authors' => $authors])
            ->willReturn('<html lang="en"></html>');

        $response = (new BlogService($template, ''))->authorNotFound($authors);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('<html lang="en"></html>', (string) $response->getBody());
    }

    /**
     * @throws Exception
     */
    public function testResolveMarkdownFilePathReturnsTheFileForTheCategoryAndSlug(): void
    {
        $articlesPath = sys_get_temp_dir() . '/' . uniqid('dk-articles-', true);
        mkdir($articlesPath . '/a-category', 0777, true);
        file_put_contents($articlesPath . '/a-category/a-slug.md', '# A slug');

        $service  = new BlogService($this->createStub(TemplateRendererInterface::class), $articlesPath);
        $filePath = $service->resolveMarkdownFilePath('a-category', 'a-slug');

        $this->assertSame($articlesPath . '/a-category/a-slug.md', $filePath);

        unlink($articlesPath . '/a-category/a-slug.md');
        rmdir($articlesPath . '/a-category');
        rmdir($articlesPath);
    }

    /**
     * @throws Exception
     */
    public function testResolveMarkdownFilePathReturnsNullWhenNoFileExists(): void
    {
        $service = new BlogService($this->createStub(TemplateRendererInterface::class), sys_get_temp_dir());

        $this->assertNull($service->resolveMarkdownFilePath('a-category', 'missing-slug'));
    }

    /**
     * @throws Exception
     */
    public function testResolveMarkdownFilePathReturnsNullForAnEmptyCategoryOrSlug(): void
    {
        $service = new BlogService($this->createStub(TemplateRendererInterface::class), sys_get_temp_dir());

        $this->assertNull($service->resolveMarkdownFilePath('', 'a-slug'));
        $this->assertNull($service->resolveMarkdownFilePath('a-category', ''));
    }

    /**
     * @throws Exception
     */
    public function testResolveMarkdownFilePathRefusesToEscapeTheArticlesPath(): void
    {
        $articlesPath = sys_get_temp_dir() . '/' . uniqid('dk-articles-', true);
        mkdir($articlesPath);

        $service = new BlogService($this->createStub(TemplateRendererInterface::class), $articlesPath);

        $this->assertNull($service->resolveMarkdownFilePath('..', 'passwd'));

        rmdir($articlesPath);
    }
}
