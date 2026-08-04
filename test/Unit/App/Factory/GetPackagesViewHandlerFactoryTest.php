<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Factory;

use Light\App\Factory\GetPackagesViewHandlerFactory;
use Light\App\Handler\GetPackagesViewHandler;
use Light\App\Service\PackageGenerator;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use LightTest\Unit\UnitTest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerInterface;
use ReflectionProperty;

use function array_key_exists;
use function sprintf;

class GetPackagesViewHandlerFactoryTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testInvokeWiresEveryDependency(): void
    {
        $dependencies = [
            TemplateRendererInterface::class => $this->createStub(TemplateRendererInterface::class),
            CategoryRepository::class        => $this->createStub(CategoryRepository::class),
            PostRepository::class            => $this->createStub(PostRepository::class),
            PackageGenerator::class          => $this->createStub(PackageGenerator::class),
        ];

        $container = $this->createStub(ContainerInterface::class);
        $container
            ->method('get')
            ->willReturnCallback(function (string $id) use ($dependencies): object {
                $this->assertTrue(
                    array_key_exists($id, $dependencies),
                    sprintf('The factory asked the container for an unexpected service: %s', $id)
                );

                return $dependencies[$id];
            });

        $handler = (new GetPackagesViewHandlerFactory())($container);

        $this->assertSame($dependencies[TemplateRendererInterface::class], $this->readProperty($handler, 'template'));
        $this->assertSame(
            $dependencies[CategoryRepository::class],
            $this->readProperty($handler, 'categoryRepository')
        );
        $this->assertSame($dependencies[PostRepository::class], $this->readProperty($handler, 'postRepository'));
        $this->assertSame($dependencies[PackageGenerator::class], $this->readProperty($handler, 'packageGenerator'));
    }

    private function readProperty(GetPackagesViewHandler $handler, string $name): mixed
    {
        return (new ReflectionProperty(GetPackagesViewHandler::class, $name))->getValue($handler);
    }
}
