<?php

declare(strict_types=1);

namespace LightTest\Unit\Blog\Factory;

use Light\Blog\Factory\BlogServiceFactory;
use Light\Blog\Service\BlogService;
use LightTest\Unit\UnitTest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerInterface;
use ReflectionProperty;

class BlogServiceFactoryTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testInvokeInjectsTheRendererAndTheArticlesPath(): void
    {
        $template  = $this->createStub(TemplateRendererInterface::class);
        $container = $this->createStub(ContainerInterface::class);
        $container
            ->method('get')
            ->willReturnCallback(fn (string $id): mixed => match ($id) {
                TemplateRendererInterface::class => $template,
                'config'                          => ['llms' => ['sourceDir' => '/some/path/md-articles']],
                default                           => null,
            });

        $service = (new BlogServiceFactory())($container, BlogService::class);

        $this->assertInstanceOf(BlogService::class, $service);
        $this->assertSame($template, (new ReflectionProperty(BlogService::class, 'template'))->getValue($service));
        $this->assertSame(
            '/some/path/md-articles',
            (new ReflectionProperty(BlogService::class, 'articlesPath'))->getValue($service)
        );
    }
}
