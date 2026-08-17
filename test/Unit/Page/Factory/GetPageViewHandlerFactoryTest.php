<?php

declare(strict_types=1);

namespace LightTest\Unit\Page\Factory;

use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Light\Page\Factory\GetPageViewHandlerFactory;
use Light\Page\Handler\GetPageViewHandler;
use LightTest\Unit\UnitTest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerInterface;
use ReflectionProperty;

class GetPageViewHandlerFactoryTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testInvokeInjectsTheRendererAndBothRepositories(): void
    {
        $template           = $this->createStub(TemplateRendererInterface::class);
        $categoryRepository = $this->createStub(CategoryRepository::class);
        $postRepository     = $this->createStub(PostRepository::class);

        $handler = (new GetPageViewHandlerFactory())(
            $this->createContainer($template, $categoryRepository, $postRepository),
            GetPageViewHandler::class
        );

        $this->assertSame($template, $this->readProperty($handler, 'template'));
        $this->assertSame($categoryRepository, $this->readProperty($handler, 'categoryRepository'));
        $this->assertSame($postRepository, $this->readProperty($handler, 'postRepository'));
    }

    /**
     * @throws Exception
     */
    private function createContainer(
        TemplateRendererInterface $template,
        CategoryRepository $categoryRepository,
        PostRepository $postRepository,
    ): ContainerInterface {
        $container = $this->createStub(ContainerInterface::class);
        $container
            ->method('get')
            ->willReturnCallback(fn (string $id): mixed => match ($id) {
                TemplateRendererInterface::class => $template,
                CategoryRepository::class        => $categoryRepository,
                default                          => $postRepository,
            });

        return $container;
    }

    private function readProperty(GetPageViewHandler $handler, string $name): mixed
    {
        return (new ReflectionProperty(GetPageViewHandler::class, $name))->getValue($handler);
    }
}
