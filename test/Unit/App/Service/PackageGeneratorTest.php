<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Service;

use JsonException;
use Light\App\Service\GitHubClientInterface;
use Light\App\Service\PackageGenerator;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use RuntimeException;

use function array_column;
use function array_key_exists;
use function bin2hex;
use function dirname;
use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function is_file;
use function json_decode;
use function mkdir;
use function random_bytes;
use function rmdir;
use function sprintf;
use function sys_get_temp_dir;
use function unlink;

use const DIRECTORY_SEPARATOR;
use const JSON_THROW_ON_ERROR;

class PackageGeneratorTest extends UnitTest
{
    private const string ORG = 'dotkernel';

    /**
     * Rewritten for every test so a run never depends on, or clobbers, the real listing.
     */
    private string $dataFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dataFile = sprintf(
            '%s%slight-packages-%s%spackages.json',
            sys_get_temp_dir(),
            DIRECTORY_SEPARATOR,
            bin2hex(random_bytes(8)),
            DIRECTORY_SEPARATOR
        );
    }

    protected function tearDown(): void
    {
        foreach ([$this->dataFile, $this->dataFile . '.tmp'] as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $directory = dirname($this->dataFile);
        if (is_dir($directory)) {
            rmdir($directory);
        }

        parent::tearDown();
    }

    public function testGetDataFileReturnsTheConfiguredPath(): void
    {
        $generator = $this->createGenerator([], []);

        $this->assertSame($this->dataFile, $generator->getDataFile());
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function testWriteCreatesTheDataFileWithTheExpectedPayload(): void
    {
        $generator = $this->createGenerator(
            [['name' => 'dot-cache', 'archived' => false]],
            [
                $this->metadataPath('dot-cache') => 'osslifecycle=active',
                $this->composerPath('dot-cache') => '{"require":{"php":"~8.3.0 || ~8.4.0"}}',
            ]
        );

        $report = $generator->write();

        $this->assertSame(1, $report['written']);
        $this->assertSame([], $report['skipped']);
        $this->assertSame([], $report['ignoredMisses']);
        $this->assertSame([], $report['warnings']);

        $payload = $this->decodeDataFile();

        $this->assertSame(self::ORG, $payload['org']);
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/',
            $payload['generated_at']
        );
        $this->assertSame([
            [
                'name'      => 'dot-cache',
                'url'       => 'https://github.com/dotkernel/dot-cache',
                'lifecycle' => 'active',
                'php'       => '~8.3.0 || ~8.4.0',
                'archived'  => false,
            ],
        ], $payload['packages']);
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function testWriteCreatesTheDataDirectoryWhenItDoesNotExist(): void
    {
        $this->assertDirectoryDoesNotExist(dirname($this->dataFile));

        $this->createGenerator(
            [['name' => 'dot-cache', 'archived' => false]],
            [$this->metadataPath('dot-cache') => 'osslifecycle=active']
        )->write();

        $this->assertFileExists($this->dataFile);
    }

    /**
     * The listing is written to a sibling file and renamed, so a crash cannot leave truncated
     * JSON behind for the request handler to read.
     *
     * @throws Exception
     * @throws JsonException
     */
    public function testWriteLeavesNoTemporaryFileBehind(): void
    {
        $this->createGenerator(
            [['name' => 'dot-cache', 'archived' => false]],
            [$this->metadataPath('dot-cache') => 'osslifecycle=active']
        )->write();

        $this->assertFileDoesNotExist($this->dataFile . '.tmp');
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function testWriteSkipsIgnoredRepositoriesRegardlessOfCase(): void
    {
        $generator = $this->createGenerator(
            [
                ['name' => 'DotKernel.com', 'archived' => false],
                ['name' => 'dot-cache', 'archived' => false],
            ],
            [
                $this->metadataPath('DotKernel.com') => 'osslifecycle=active',
                $this->metadataPath('dot-cache')     => 'osslifecycle=active',
            ],
            ['DOTKERNEL.COM']
        );

        $report = $generator->write();

        $this->assertSame(1, $report['written']);
        $this->assertSame(['DotKernel.com'], $report['skipped']);
        $this->assertSame([], $report['ignoredMisses']);
        $this->assertSame(['dot-cache'], array_column($this->decodeDataFile()['packages'], 'name'));
    }

    /**
     * A renamed or deleted repository must not silently reappear on the site.
     *
     * @throws Exception
     * @throws JsonException
     */
    public function testWriteReportsIgnoredEntriesThatMatchedNothing(): void
    {
        $generator = $this->createGenerator(
            [['name' => 'dot-cache', 'archived' => false]],
            [$this->metadataPath('dot-cache') => 'osslifecycle=active'],
            ['  Renamed-Repo  ', 'dot-cache', '', '   ']
        );

        $report = $generator->write();

        $this->assertSame(['dot-cache'], $report['skipped']);
        $this->assertSame(['renamed-repo'], $report['ignoredMisses']);
        $this->assertSame(0, $report['written']);
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function testWriteIgnoresRepositoriesWithoutOssMetadata(): void
    {
        $generator = $this->createGenerator(
            [
                ['name' => 'dot-cache', 'archived' => false],
                ['name' => 'not-a-package', 'archived' => false],
            ],
            [$this->metadataPath('dot-cache') => 'osslifecycle=active']
        );

        $report = $generator->write();

        $this->assertSame(1, $report['written']);
        $this->assertSame([], $report['skipped']);
        $this->assertSame([], $report['warnings']);
        $this->assertSame(['dot-cache'], array_column($this->decodeDataFile()['packages'], 'name'));
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function testWriteWarnsWhenOssMetadataHasNoLifecycleValue(): void
    {
        $generator = $this->createGenerator(
            [['name' => 'dot-cache', 'archived' => false]],
            [$this->metadataPath('dot-cache') => '# nothing useful in here']
        );

        $report = $generator->write();

        $this->assertSame(0, $report['written']);
        $this->assertSame(
            ['dot-cache: OSSMETADATA present but no osslifecycle value found, skipped'],
            $report['warnings']
        );
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function testWriteIgnoresRepositoriesWithoutAUsableName(): void
    {
        $generator = $this->createGenerator(
            [
                ['name' => '   '],
                ['name' => 42],
                ['archived' => false],
                ['name' => 'dot-cache', 'archived' => false],
            ],
            [$this->metadataPath('dot-cache') => 'osslifecycle=active']
        );

        $report = $generator->write();

        $this->assertSame(1, $report['written']);
        $this->assertSame([], $report['warnings']);
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function testWriteExcludesArchivedRepositoriesWhenTheyAreNotIncluded(): void
    {
        $generator = $this->createGenerator(
            [
                ['name' => 'dot-console', 'archived' => true],
                ['name' => 'dot-cache', 'archived' => false],
            ],
            [
                $this->metadataPath('dot-console') => 'osslifecycle=archived',
                $this->metadataPath('dot-cache')   => 'osslifecycle=active',
            ],
            [],
            false
        );

        $report = $generator->write();

        $this->assertSame(1, $report['written']);
        $this->assertSame(['dot-cache'], array_column($this->decodeDataFile()['packages'], 'name'));
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function testWriteFlagsArchivedRepositoriesWhenTheyAreIncluded(): void
    {
        $generator = $this->createGenerator(
            [['name' => 'dot-console', 'archived' => true]],
            [$this->metadataPath('dot-console') => 'osslifecycle=archived']
        );

        $report = $generator->write();

        $this->assertSame(1, $report['written']);
        $this->assertTrue($this->decodeDataFile()['packages'][0]['archived']);
    }

    /**
     * Unrecognised lifecycles sort last; equal lifecycles sort by name.
     *
     * @throws Exception
     * @throws JsonException
     */
    public function testWriteSortsByLifecycleThenName(): void
    {
        $lifecycles = [
            'frobnicate-repo' => 'frobnicate',
            'zeta-repo'       => 'active',
            'archived-repo'   => 'archived',
            'alpha-repo'      => 'active',
            'security-repo'   => 'security-only',
            'maint-repo'      => 'maintenance',
        ];

        $repositories = [];
        $files        = [];
        foreach ($lifecycles as $name => $lifecycle) {
            $repositories[]                    = ['name' => $name, 'archived' => false];
            $files[$this->metadataPath($name)] = sprintf('osslifecycle=%s', $lifecycle);
        }

        $this->createGenerator($repositories, $files)->write();

        $this->assertSame([
            'alpha-repo',
            'zeta-repo',
            'maint-repo',
            'security-repo',
            'archived-repo',
            'frobnicate-repo',
        ], array_column($this->decodeDataFile()['packages'], 'name'));
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function testWriteRecordsANullPhpConstraintWhenComposerJsonIsMissing(): void
    {
        $this->createGenerator(
            [['name' => 'dot-cache', 'archived' => false]],
            [$this->metadataPath('dot-cache') => 'osslifecycle=active']
        )->write();

        $this->assertNull($this->decodeDataFile()['packages'][0]['php']);
    }

    /**
     * @param non-empty-string $composerJson
     * @throws Exception
     * @throws JsonException
     */
    #[DataProvider('unusablePhpConstraintProvider')]
    public function testWriteRecordsANullPhpConstraintWhenItCannotBeRead(string $composerJson): void
    {
        $this->createGenerator(
            [['name' => 'dot-cache', 'archived' => false]],
            [
                $this->metadataPath('dot-cache') => 'osslifecycle=active',
                $this->composerPath('dot-cache') => $composerJson,
            ]
        )->write();

        $this->assertNull($this->decodeDataFile()['packages'][0]['php']);
    }

    /**
     * @return array<string, array{non-empty-string}>
     */
    public static function unusablePhpConstraintProvider(): array
    {
        return [
            'not json'             => ['this is not json'],
            'json scalar'          => ['"just a string"'],
            'no require section'   => ['{"name":"dotkernel/dot-cache"}'],
            'require not an array' => ['{"require":"php"}'],
            'no php requirement'   => ['{"require":{"ext-json":"*"}}'],
            'php not a string'     => ['{"require":{"php":8.4}}'],
            'blank php'            => ['{"require":{"php":"   "}}'],
        ];
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function testWriteTrimsThePhpConstraint(): void
    {
        $this->createGenerator(
            [['name' => 'dot-cache', 'archived' => false]],
            [
                $this->metadataPath('dot-cache') => 'osslifecycle=active',
                $this->composerPath('dot-cache') => '{"require":{"php":"  ~8.4.0  "}}',
            ]
        )->write();

        $this->assertSame('~8.4.0', $this->decodeDataFile()['packages'][0]['php']);
    }

    /**
     * @param non-empty-string $metadata
     * @throws Exception
     * @throws JsonException
     */
    #[DataProvider('lifecycleProvider')]
    public function testWriteParsesTheLifecycleValue(string $metadata, string $expected): void
    {
        $this->createGenerator(
            [['name' => 'dot-cache', 'archived' => false]],
            [$this->metadataPath('dot-cache') => $metadata]
        )->write();

        $this->assertSame($expected, $this->decodeDataFile()['packages'][0]['lifecycle']);
    }

    /**
     * @return array<string, array{non-empty-string, string}>
     */
    public static function lifecycleProvider(): array
    {
        return [
            'bare'                => ['osslifecycle=active', 'active'],
            'padded equals'       => ['osslifecycle = maintenance', 'maintenance'],
            'uppercase key'       => ['OSSLIFECYCLE=active', 'active'],
            'uppercase value'     => ['osslifecycle=ACTIVE', 'active'],
            'trailing comment'    => ['osslifecycle=active#stable', 'active'],
            'surrounded by lines' => ["# header\nosslifecycle=security-only\n", 'security-only'],
        ];
    }

    /**
     * A partial listing must never overwrite a known-good one.
     *
     * @throws Exception
     * @throws JsonException
     */
    public function testWriteAbortsWithoutTouchingTheDataFileWhenTooManyRequestsFail(): void
    {
        mkdir(dirname($this->dataFile), 0775, true);
        file_put_contents($this->dataFile, '{"packages":["previous"]}');

        $generator = $this->createGenerator(
            [['name' => 'dot-cache', 'archived' => false]],
            [$this->metadataPath('dot-cache') => new RuntimeException('HTTP 503')]
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Aborting without writing: 1 of 1 requests failed');

        try {
            $generator->write();
        } finally {
            $this->assertSame('{"packages":["previous"]}', file_get_contents($this->dataFile));
            $this->assertFileDoesNotExist($this->dataFile . '.tmp');
        }
    }

    /**
     * One failure in nine attempts stays under the 20% threshold, so the run still writes.
     *
     * @throws Exception
     * @throws JsonException
     */
    public function testWriteWarnsButStillWritesWhenAMetadataRequestFailsBelowTheThreshold(): void
    {
        $repositories = [['name' => 'broken-repo', 'archived' => false]];
        $files        = [$this->metadataPath('broken-repo') => new RuntimeException('HTTP 503')];

        foreach (['repo-a', 'repo-b', 'repo-c', 'repo-d'] as $name) {
            $repositories[]                    = ['name' => $name, 'archived' => false];
            $files[$this->metadataPath($name)] = 'osslifecycle=active';
            $files[$this->composerPath($name)] = '{"require":{"php":"~8.4.0"}}';
        }

        $report = $this->createGenerator($repositories, $files)->write();

        $this->assertSame(4, $report['written']);
        $this->assertSame(
            ['broken-repo: could not read OSSMETADATA (HTTP 503)'],
            $report['warnings']
        );
        $this->assertSame(
            ['repo-a', 'repo-b', 'repo-c', 'repo-d'],
            array_column($this->decodeDataFile()['packages'], 'name')
        );
    }

    /**
     * A failed composer.json read costs the constraint, not the package.
     *
     * @throws Exception
     * @throws JsonException
     */
    public function testWriteKeepsThePackageWhenTheComposerRequestFails(): void
    {
        $repositories = [['name' => 'broken-repo', 'archived' => false]];
        $files        = [
            $this->metadataPath('broken-repo') => 'osslifecycle=active',
            $this->composerPath('broken-repo') => new RuntimeException('HTTP 500'),
        ];

        foreach (['repo-a', 'repo-b', 'repo-c', 'repo-d'] as $name) {
            $repositories[]                    = ['name' => $name, 'archived' => false];
            $files[$this->metadataPath($name)] = 'osslifecycle=active';
            $files[$this->composerPath($name)] = '{"require":{"php":"~8.4.0"}}';
        }

        $report = $this->createGenerator($repositories, $files)->write();

        $this->assertSame(5, $report['written']);
        $this->assertSame(
            ['broken-repo: could not read composer.json (HTTP 500)'],
            $report['warnings']
        );

        $packages = array_column($this->decodeDataFile()['packages'], 'php', 'name');
        $this->assertNull($packages['broken-repo']);
        $this->assertSame('~8.4.0', $packages['repo-a']);
    }

    public function testReadReturnsNullWhenTheDataFileIsMissing(): void
    {
        $this->assertNull($this->createGenerator([], [])->read());
    }

    /**
     * @param non-empty-string $contents
     */
    #[DataProvider('unusableDataFileProvider')]
    public function testReadReturnsNullWhenTheDataFileCannotBeUsed(string $contents): void
    {
        $this->writeDataFile($contents);

        $this->assertNull($this->createGenerator([], [])->read());
    }

    /**
     * @return array<string, array{non-empty-string}>
     */
    public static function unusableDataFileProvider(): array
    {
        return [
            'empty'              => ['   '],
            'invalid json'       => ['{not json'],
            'json scalar'        => ['"a string"'],
            'no packages key'    => ['{"generated_at":"now"}'],
            'packages not array' => ['{"packages":"nope"}'],
        ];
    }

    public function testReadReturnsTheDecodedPayload(): void
    {
        $this->writeDataFile('{"generated_at":"2026-08-04T12:00:00+00:00","org":"dotkernel","packages":[]}');

        $this->assertSame([
            'generated_at' => '2026-08-04T12:00:00+00:00',
            'org'          => 'dotkernel',
            'packages'     => [],
        ], $this->createGenerator([], [])->read());
    }

    /**
     * @param list<array<string, mixed>> $repositories
     * @param array<string, string|RuntimeException> $files Response per requested path; an absent
     *                                                      key stands in for a 404.
     * @param list<string> $ignoreRepos
     * @throws Exception
     */
    private function createGenerator(
        array $repositories,
        array $files,
        array $ignoreRepos = [],
        bool $includeArchived = true,
    ): PackageGenerator {
        $client = $this->createMock(GitHubClientInterface::class);

        $client
            ->method('getAllPages')
            ->with(sprintf('/orgs/%s/repos?per_page=100&type=public', self::ORG))
            ->willReturn($repositories);

        $client
            ->method('get')
            ->willReturnCallback(
                function (string $path, string $accept = GitHubClientInterface::ACCEPT_JSON) use ($files): ?string {
                    $this->assertSame(GitHubClientInterface::ACCEPT_RAW, $accept);

                    if (! array_key_exists($path, $files)) {
                        return null;
                    }

                    $response = $files[$path];
                    if ($response instanceof RuntimeException) {
                        throw $response;
                    }

                    return $response;
                }
            );

        return new PackageGenerator(
            $client,
            $this->dataFile,
            self::ORG,
            $ignoreRepos,
            $includeArchived
        );
    }

    /**
     * @return non-empty-string
     */
    private function metadataPath(string $repository): string
    {
        return sprintf('/repos/%s/%s/contents/OSSMETADATA', self::ORG, $repository);
    }

    /**
     * @return non-empty-string
     */
    private function composerPath(string $repository): string
    {
        return sprintf('/repos/%s/%s/contents/composer.json', self::ORG, $repository);
    }

    private function writeDataFile(string $contents): void
    {
        $directory = dirname($this->dataFile);
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        file_put_contents($this->dataFile, $contents);
    }

    /**
     * @return array{generated_at: string, org: string, packages: list<array<string, mixed>>}
     * @throws JsonException
     */
    private function decodeDataFile(): array
    {
        $this->assertFileExists($this->dataFile);

        $contents = file_get_contents($this->dataFile);
        $this->assertIsString($contents);

        /** @var array{generated_at: string, org: string, packages: list<array<string, mixed>>} $decoded */
        $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('generated_at', $decoded);
        $this->assertArrayHasKey('org', $decoded);
        $this->assertArrayHasKey('packages', $decoded);
        $this->assertIsString($decoded['generated_at']);
        $this->assertIsString($decoded['org']);
        $this->assertIsArray($decoded['packages']);

        return $decoded;
    }
}
