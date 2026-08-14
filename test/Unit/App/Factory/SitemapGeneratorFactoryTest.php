<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Factory;

use Light\App\Factory\SitemapGeneratorFactory;
use Light\App\Service\SitemapGenerator;
use Light\Blog\Repository\AuthorRepository;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerInterface;
use ReflectionProperty;

class SitemapGeneratorFactoryTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testInvokeAppliesTheConfiguredValues(): void
    {
        $generator = (new SitemapGeneratorFactory())($this->createContainer([
            'sitemap'     => ['path' => '/tmp/sitemap.xml'],
            'application' => ['url' => 'https://example.test'],
            'routes'      => ['page' => ['contact' => 'contact', 'api' => 'api']],
        ]));

        $this->assertSame('/tmp/sitemap.xml', $generator->getSitemapFile());
        $this->assertSame('https://example.test', $this->readProperty($generator, 'baseUrl'));
        $this->assertSame(['contact', 'api'], $this->readProperty($generator, 'pageRoutes'));
    }

    /**
     * The URL comes from `application.url`, the site's single URL key.
     *
     * @throws Exception
     */
    public function testInvokeDoesNotReadTheRetiredBaseUrlKey(): void
    {
        $generator = (new SitemapGeneratorFactory())($this->createContainer([
            'sitemap'     => ['path' => '/tmp/sitemap.xml'],
            'application' => ['baseUrl' => 'https://stale.test'],
        ]));

        $this->assertSame('', $this->readProperty($generator, 'baseUrl'));
    }

    /**
     * The route slugs are the keys of every module's route list, which is what the sitemap needs.
     *
     * @throws Exception
     */
    public function testInvokeCollectsRouteSlugsFromEveryModule(): void
    {
        $generator = (new SitemapGeneratorFactory())($this->createContainer([
            'sitemap' => ['path' => '/tmp/sitemap.xml'],
            'routes'  => [
                'page'  => ['contact' => 'contact', 'api' => 'api'],
                'other' => ['extra' => 'extra'],
            ],
        ]));

        $this->assertSame(['contact', 'api', 'extra'], $this->readProperty($generator, 'pageRoutes'));
    }

    /**
     * @throws Exception
     */
    public function testInvokeSkipsRouteEntriesThatAreNotArrays(): void
    {
        $generator = (new SitemapGeneratorFactory())($this->createContainer([
            'sitemap' => ['path' => '/tmp/sitemap.xml'],
            'routes'  => ['page' => ['contact' => 'contact'], 'broken' => 'not an array'],
        ]));

        $this->assertSame(['contact'], $this->readProperty($generator, 'pageRoutes'));
    }

    /**
     * @throws Exception
     */
    public function testInvokeYieldsNoRoutesWhenNoneAreConfigured(): void
    {
        $generator = (new SitemapGeneratorFactory())($this->createContainer([
            'sitemap' => ['path' => '/tmp/sitemap.xml'],
        ]));

        $this->assertSame([], $this->readProperty($generator, 'pageRoutes'));
    }

    /**
     * @param array<string, mixed> $config
     * @throws Exception
     */
    private function createContainer(array $config): ContainerInterface
    {
        $container = $this->createStub(ContainerInterface::class);
        $container
            ->method('get')
            ->willReturnCallback(fn (string $id): mixed => match ($id) {
                'config'                   => $config,
                CategoryRepository::class  => $this->createStub(CategoryRepository::class),
                AuthorRepository::class    => $this->createStub(AuthorRepository::class),
                default                    => $this->createStub(PostRepository::class),
            });

        return $container;
    }

    private function readProperty(SitemapGenerator $generator, string $name): mixed
    {
        return (new ReflectionProperty(SitemapGenerator::class, $name))->getValue($generator);
    }
}
