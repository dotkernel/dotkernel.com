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

        $handler  = new GetIndexViewHandler($template, $postRepository);
        $response = $handler->handle(new ServerRequest());

        $this->assertSame($posts, $captured['posts']);
        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('<html lang="en"></html>', (string) $response->getBody());
    }
}
