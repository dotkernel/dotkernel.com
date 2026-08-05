<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\XmlResponse;
use Laminas\Diactoros\ServerRequest;
use Light\App\Handler\GetFeedViewHandler;
use Light\App\Service\FeedGenerator;
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

class GetFeedViewHandlerTest extends UnitTest
{
    private string $feedFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->feedFile = sprintf(
            '%s%slight-feed-view-%s%sfeed.xml',
            sys_get_temp_dir(),
            DIRECTORY_SEPARATOR,
            bin2hex(random_bytes(8)),
            DIRECTORY_SEPARATOR
        );
    }

    protected function tearDown(): void
    {
        if (is_file($this->feedFile)) {
            unlink($this->feedFile);
        }

        $directory = dirname($this->feedFile);
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
        $this->writeFeedFile('<rss><channel><title>cached</title></channel></rss>');

        $feedGenerator = $this->createMock(FeedGenerator::class);
        $feedGenerator->method('getFeedFile')->willReturn($this->feedFile);
        $feedGenerator->expects($this->never())->method('write');

        $response = $this->createHandler($feedGenerator)->handle(new ServerRequest());

        $this->assertInstanceOf(XmlResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(FeedGenerator::CONTENT_TYPE, $response->getHeaderLine('Content-Type'));
        $this->assertSame('<rss><channel><title>cached</title></channel></rss>', (string) $response->getBody());
    }

    /**
     * @throws Exception
     */
    public function testHandleGeneratesTheFeedWhenTheFileIsMissing(): void
    {
        $feedGenerator = $this->createMock(FeedGenerator::class);
        $feedGenerator->method('getFeedFile')->willReturn($this->feedFile);
        $feedGenerator->expects($this->once())->method('write')
            ->willReturnCallback(function (): int {
                $this->writeFeedFile('<rss><channel><title>fresh</title></channel></rss>');
                return 1;
            });

        $response = $this->createHandler($feedGenerator)->handle(new ServerRequest());

        $this->assertInstanceOf(XmlResponse::class, $response);
        $this->assertSame('<rss><channel><title>fresh</title></channel></rss>', (string) $response->getBody());
    }

    /**
     * @throws Exception
     */
    public function testHandleGeneratesTheFeedWhenTheExistingFileIsEmpty(): void
    {
        $this->writeFeedFile('');

        $feedGenerator = $this->createMock(FeedGenerator::class);
        $feedGenerator->method('getFeedFile')->willReturn($this->feedFile);
        $feedGenerator->expects($this->once())->method('write')
            ->willReturnCallback(function (): int {
                $this->writeFeedFile('<rss><channel><title>fresh</title></channel></rss>');
                return 1;
            });

        $this->createHandler($feedGenerator)->handle(new ServerRequest());
    }

    /**
     * @throws Exception
     */
    public function testHandleReturns404WhenGenerationFailsAndNoFileExists(): void
    {
        $feedGenerator = $this->createStub(FeedGenerator::class);
        $feedGenerator->method('getFeedFile')->willReturn($this->feedFile);
        $feedGenerator->method('write')->willThrowException(new RuntimeException('boom'));

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

        $response = $this->createHandler($feedGenerator, $template, $categoryRepository)
            ->handle(new ServerRequest());

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    private function createHandler(
        FeedGenerator $feedGenerator,
        ?TemplateRendererInterface $template = null,
        ?CategoryRepository $categoryRepository = null,
    ): GetFeedViewHandler {
        return new GetFeedViewHandler(
            $template ?? $this->createStub(TemplateRendererInterface::class),
            $categoryRepository ?? $this->createStub(CategoryRepository::class),
            $feedGenerator,
        );
    }

    private function writeFeedFile(string $contents): void
    {
        $directory = dirname($this->feedFile);
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        file_put_contents($this->feedFile, $contents);
    }
}
