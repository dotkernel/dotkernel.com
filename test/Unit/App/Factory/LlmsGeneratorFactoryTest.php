<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Factory;

use Light\App\Factory\LlmsGeneratorFactory;
use Light\App\Service\LlmsGenerator;
use Light\Blog\Repository\PostRepository;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerInterface;
use ReflectionProperty;

class LlmsGeneratorFactoryTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testInvokeAppliesTheConfiguredValues(): void
    {
        $generator = (new LlmsGeneratorFactory())($this->createContainer([
            'llms'        => [
                'sourceDir' => '/tmp/md-articles',
                'indexFile' => '/tmp/llms.txt',
                'pagesDir'  => '/tmp/md-pages',
            ],
            'application' => [
                'url'  => 'https://example.test',
                'meta' => ['title' => 'Custom Title'],
            ],
        ]));

        $this->assertSame('/tmp/llms.txt', $generator->getOutputFile());
        $this->assertSame('/tmp/md-articles', $this->readProperty($generator, 'sourceDir'));
        $this->assertSame('/tmp/md-pages', $this->readProperty($generator, 'pagesDir'));
        $this->assertSame('https://example.test', $this->readProperty($generator, 'baseUrl'));
        $this->assertSame('Custom Title', $this->readProperty($generator, 'title'));
    }

    /**
     * A config without a meta title (e.g. the `application.meta` section is absent) simply
     * keeps the historical default.
     *
     * @throws Exception
     */
    public function testInvokeFallsBackToTheDefaultTitleWhenItIsNotConfigured(): void
    {
        $generator = (new LlmsGeneratorFactory())($this->createContainer([
            'llms'        => [
                'sourceDir' => '/tmp/md-articles',
                'indexFile' => '/tmp/llms.txt',
            ],
            'application' => ['url' => 'https://example.test'],
        ]));

        $this->assertSame('Dotkernel', $this->readProperty($generator, 'title'));
    }

    /**
     * A config predating the static-page markdown simply skips that section.
     *
     * @throws Exception
     */
    public function testInvokeLeavesThePagesDirectoryUnsetWhenItIsNotConfigured(): void
    {
        $generator = (new LlmsGeneratorFactory())($this->createContainer([
            'llms'        => [
                'sourceDir' => '/tmp/md-articles',
                'indexFile' => '/tmp/llms.txt',
            ],
            'application' => ['url' => 'https://example.test'],
        ]));

        $this->assertNull($this->readProperty($generator, 'pagesDir'));
    }

    /**
     * The application URL is read from `application.url` - the site's single URL key. A config
     * without it must not blow up, because the placeholder substitution is optional.
     *
     * @param array<string, mixed> $application
     * @throws Exception
     */
    #[DataProvider('missingUrlProvider')]
    public function testInvokeFallsBackToAnEmptyBaseUrl(array $application): void
    {
        $generator = (new LlmsGeneratorFactory())($this->createContainer([
            'llms'        => [
                'sourceDir' => '/tmp/md-articles',
                'indexFile' => '/tmp/llms.txt',
            ],
            'application' => $application,
        ]));

        $this->assertSame('', $this->readProperty($generator, 'baseUrl'));
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function missingUrlProvider(): array
    {
        return [
            'application section is empty' => [[]],
            'url is null'                  => [['url' => null]],
        ];
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
                'config' => $config,
                default  => $this->createStub(PostRepository::class),
            });

        return $container;
    }

    private function readProperty(LlmsGenerator $generator, string $name): mixed
    {
        return (new ReflectionProperty(LlmsGenerator::class, $name))->getValue($generator);
    }
}
