<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Factory;

use Light\App\Factory\PackageGeneratorFactory;
use Light\App\Service\GitHubClientInterface;
use Light\App\Service\PackageGenerator;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerInterface;
use ReflectionProperty;
use stdClass;

class PackageGeneratorFactoryTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testInvokeAppliesTheConfiguredValues(): void
    {
        $generator = (new PackageGeneratorFactory())($this->createContainer([
            'github'   => ['org' => 'acme'],
            'packages' => [
                'dataFile'        => '/tmp/acme-packages.json',
                'ignoreRepos'     => ['acme.com'],
                'includeArchived' => false,
            ],
        ]));

        $this->assertSame('/tmp/acme-packages.json', $generator->getDataFile());
        $this->assertSame('acme', $this->readProperty($generator, 'org'));
        $this->assertSame(['acme.com'], $this->readProperty($generator, 'ignoreRepos'));
        $this->assertFalse($this->readProperty($generator, 'includeArchived'));
    }

    /**
     * @param mixed $config
     * @throws Exception
     */
    #[DataProvider('incompleteConfigProvider')]
    public function testInvokeFallsBackToDefaults($config): void
    {
        $generator = (new PackageGeneratorFactory())($this->createContainer($config));

        $this->assertSame('data/packages.json', $generator->getDataFile());
        $this->assertSame('dotkernel', $this->readProperty($generator, 'org'));
        $this->assertSame([], $this->readProperty($generator, 'ignoreRepos'));
        $this->assertTrue($this->readProperty($generator, 'includeArchived'));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function incompleteConfigProvider(): array
    {
        return [
            'config is not an array'      => ['not an array'],
            'config is empty'             => [[]],
            'sections are not arrays'     => [['github' => 'nope', 'packages' => 'nope']],
            'sections are empty'          => [['github' => [], 'packages' => []]],
            'ignoreRepos is not an array' => [['packages' => ['ignoreRepos' => 'dotkernel.com']]],
        ];
    }

    /**
     * Config is not guaranteed to hold a clean list of strings.
     *
     * @throws Exception
     */
    public function testInvokeKeepsOnlyScalarIgnoredRepositoriesAsStrings(): void
    {
        $generator = (new PackageGeneratorFactory())($this->createContainer([
            'packages' => [
                'ignoreRepos' => [
                    'dotkernel.com',
                    123,
                    true,
                    1.5,
                    ['nested', 'array'],
                    new stdClass(),
                    null,
                ],
            ],
        ]));

        $this->assertSame(
            ['dotkernel.com', '123', '1', '1.5'],
            $this->readProperty($generator, 'ignoreRepos')
        );
    }

    /**
     * @throws Exception
     */
    private function createContainer(mixed $config): ContainerInterface
    {
        $container = $this->createStub(ContainerInterface::class);
        $container
            ->method('get')
            ->willReturnCallback(
                fn (string $id): mixed => $id === 'config'
                    ? $config
                    : $this->createStub(GitHubClientInterface::class)
            );

        return $container;
    }

    private function readProperty(PackageGenerator $generator, string $name): mixed
    {
        return (new ReflectionProperty(PackageGenerator::class, $name))->getValue($generator);
    }
}
