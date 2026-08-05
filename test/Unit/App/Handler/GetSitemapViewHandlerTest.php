<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\XmlResponse;
use Laminas\Diactoros\ServerRequest;
use Light\App\Handler\GetSitemapViewHandler;
use Light\App\Service\SitemapGenerator;
use Light\Blog\Entity\Category;
use Light\Blog\Repository\CategoryRepository;
use LightTest\Unit\UnitTest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use RuntimeException;

use function bin2hex;
use function dirname;
use function file_put_contents;
use function is_dir;
use function is_file;
use function mkdir;
use function random_bytes;
use function rmdir;
use function sprintf;
use function sys_get_temp_dir;
use function unlink;

use const DIRECTORY_SEPARATOR;

class GetSitemapViewHandlerTest extends UnitTest
{
    private string $sitemapFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sitemapFile = sprintf(
            '%s%slight-sitemap-view-%s%ssitemap.xml',
            sys_get_temp_dir(),
            DIRECTORY_SEPARATOR,
            bin2hex(random_bytes(8)),
            DIRECTORY_SEPARATOR
        );
    }

    protected function tearDown(): void
    {
        if (is_file($this->sitemapFile)) {
            unlink($this->sitemapFile);
        }

        $directory = dirname($this->sitemapFile);
        if (is_dir($directory)) {
            rmdir($directory);
        }

        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testHandleServesTheExistingFileWithoutRegenerating(): void
    {
        $this->writeSitemapFile('<urlset><url><loc>cached</loc></url></urlset>');

        $sitemapGenerator = $this->createMock(SitemapGenerator::class);
        $sitemapGenerator->method('getSitemapFile')->willReturn($this->sitemapFile);
        $sitemapGenerator->expects($this->never())->method('write');

        $response = $this->createHandler($sitemapGenerator)->handle(new ServerRequest());

        $this->assertInstanceOf(XmlResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(SitemapGenerator::CONTENT_TYPE, $response->getHeaderLine('Content-Type'));
        $this->assertSame('<urlset><url><loc>cached</loc></url></urlset>', (string) $response->getBody());
    }

    /**
     * @throws Exception
     */
    public function testHandleGeneratesTheSitemapWhenTheFileIsMissing(): void
    {
        $sitemapGenerator = $this->createMock(SitemapGenerator::class);
        $sitemapGenerator->method('getSitemapFile')->willReturn($this->sitemapFile);
        $sitemapGenerator->expects($this->once())->method('write')
            ->willReturnCallback(function (): int {
                $this->writeSitemapFile('<urlset><url><loc>fresh</loc></url></urlset>');
                return 1;
            });

        $response = $this->createHandler($sitemapGenerator)->handle(new ServerRequest());

        $this->assertInstanceOf(XmlResponse::class, $response);
        $this->assertSame('<urlset><url><loc>fresh</loc></url></urlset>', (string) $response->getBody());
    }

    /**
     * @throws Exception
     */
    public function testHandleReturns404WhenGenerationFailsAndNoFileExists(): void
    {
        $sitemapGenerator = $this->createStub(SitemapGenerator::class);
        $sitemapGenerator->method('getSitemapFile')->willReturn($this->sitemapFile);
        $sitemapGenerator->method('write')->willThrowException(new RuntimeException('boom'));

        $categories         = [$this->createStub(Category::class)];
        $categoryRepository = $this->createStub(CategoryRepository::class);
        $categoryRepository->method('getCategories')->willReturn($categories);

        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->once())->method('render')
            ->willReturnCallback(function (string $name, mixed $parameters = []) use ($categories): string {
                $this->assertSame('error::404', $name);
                $this->assertIsArray($parameters);
                $this->assertSame($categories, $parameters['categories']);

                return '<html lang="en">not found</html>';
            });

        $response = $this->createHandler($sitemapGenerator, $template, $categoryRepository)
            ->handle(new ServerRequest());

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    private function createHandler(
        SitemapGenerator $sitemapGenerator,
        ?TemplateRendererInterface $template = null,
        ?CategoryRepository $categoryRepository = null,
    ): GetSitemapViewHandler {
        return new GetSitemapViewHandler(
            $template ?? $this->createStub(TemplateRendererInterface::class),
            $categoryRepository ?? $this->createStub(CategoryRepository::class),
            $sitemapGenerator,
        );
    }

    private function writeSitemapFile(string $contents): void
    {
        $directory = dirname($this->sitemapFile);
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        file_put_contents($this->sitemapFile, $contents);
    }
}
