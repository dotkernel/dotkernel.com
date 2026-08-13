<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Factory;

use Light\App\Factory\FeedGeneratorFactory;
use Light\App\Service\FeedGenerator;
use Light\Blog\Repository\PostRepository;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerInterface;
use ReflectionProperty;

class FeedGeneratorFactoryTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testInvokeAppliesTheConfiguredValues(): void
    {
        $generator = (new FeedGeneratorFactory())($this->createContainer([
            'feed'        => ['path' => '/tmp/feed.xml'],
            'application' => [
                'url'  => 'https://example.test',
                'meta' => [
                    'title'       => 'Light Blog',
                    'description' => 'A description',
                    'image'       => 'https://example.test/default.png',
                ],
            ],
        ]));

        $this->assertSame('/tmp/feed.xml', $generator->getFeedFile());
        $this->assertSame('https://example.test', $this->readProperty($generator, 'baseUrl'));
        $this->assertSame('Light Blog', $this->readProperty($generator, 'title'));
        $this->assertSame('A description', $this->readProperty($generator, 'description'));
        $this->assertSame('https://example.test/default.png', $this->readProperty($generator, 'image'));
    }

    /**
     * The URL comes from `application.url`, the site's single URL key.
     *
     * @throws Exception
     */
    public function testInvokeDoesNotReadTheRetiredBaseUrlKey(): void
    {
        $generator = (new FeedGeneratorFactory())($this->createContainer([
            'feed'        => ['path' => '/tmp/feed.xml'],
            'application' => ['baseUrl' => 'https://stale.test'],
        ]));

        $this->assertSame('', $this->readProperty($generator, 'baseUrl'));
    }

    /**
     * @throws Exception
     */
    public function testInvokeFallsBackToEmptyStringsForMissingMetadata(): void
    {
        $generator = (new FeedGeneratorFactory())($this->createContainer([
            'feed'        => ['path' => '/tmp/feed.xml'],
            'application' => [],
        ]));

        $this->assertSame('', $this->readProperty($generator, 'baseUrl'));
        $this->assertSame('', $this->readProperty($generator, 'title'));
        $this->assertSame('', $this->readProperty($generator, 'description'));
        $this->assertSame('', $this->readProperty($generator, 'image'));
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
            ->willReturnCallback(
                fn (string $id): mixed => $id === 'config'
                    ? $config
                    : $this->createStub(PostRepository::class)
            );

        return $container;
    }

    private function readProperty(FeedGenerator $generator, string $name): mixed
    {
        return (new ReflectionProperty(FeedGenerator::class, $name))->getValue($generator);
    }
}
