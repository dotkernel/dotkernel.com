<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;
use Light\App\Handler\GetIndexViewHandler;
use Light\Blog\Entity\Post;
use Light\Blog\Repository\PostRepository;
use LightTest\Unit\UnitTest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;

use function file_put_contents;
use function mkdir;
use function rmdir;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

class GetIndexViewHandlerTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testHandleRendersTheIndexTemplateWithTheThreeMostRecentPosts(): void
    {
        $posts = [$this->createStub(Post::class), $this->createStub(Post::class)];

        $postRepository = $this->createMock(PostRepository::class);
        $postRepository->expects($this->once())->method('getRecentPosts')->with(3)->willReturn($posts);

        $captured = [];
        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->once())->method('render')
            ->willReturnCallback(function (string $name, mixed $parameters = []) use (&$captured): string {
                $this->assertSame('app::index', $name);
                $this->assertIsArray($parameters);
                $captured = $parameters;

                return '<html lang="en"></html>';
            });

        $handler  = new GetIndexViewHandler($template, $postRepository, '');
        $response = $handler->handle(new ServerRequest());

        $this->assertSame($posts, $captured['posts']);
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('<html lang="en"></html>', (string) $response->getBody());
    }

    /**
     * @throws Exception
     */
    public function testHandleReturnsTheMarkdownFileWhenAcceptRequestsIt(): void
    {
        $mdPagesPath = sys_get_temp_dir() . '/' . uniqid('dk-pages-', true);
        mkdir($mdPagesPath);
        file_put_contents($mdPagesPath . '/index.md', '# Dotkernel');

        $postRepository = $this->createMock(PostRepository::class);
        $postRepository->expects($this->never())->method('getRecentPosts');

        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->never())->method('render');

        $handler  = new GetIndexViewHandler($template, $postRepository, $mdPagesPath);
        $response = $handler->handle(
            (new ServerRequest())->withHeader('Accept', 'text/markdown')
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('text/markdown', $response->getHeaderLine('Content-Type'));
        $this->assertSame('# Dotkernel', (string) $response->getBody());

        unlink($mdPagesPath . '/index.md');
        rmdir($mdPagesPath);
    }

    /**
     * @throws Exception
     */
    public function testHandleFallsBackToHtmlWhenNoMarkdownFileExists(): void
    {
        $postRepository = $this->createMock(PostRepository::class);
        $postRepository->expects($this->once())->method('getRecentPosts')->willReturn([]);

        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->once())->method('render')->willReturn('<html lang="en"></html>');

        $handler  = new GetIndexViewHandler($template, $postRepository, sys_get_temp_dir());
        $response = $handler->handle(
            (new ServerRequest())->withHeader('Accept', 'text/markdown')
        );

        $this->assertStringContainsString('text/html', $response->getHeaderLine('Content-Type'));
    }
}
