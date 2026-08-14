<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Service;

use Light\App\Service\LlmsFullGenerator;
use LightTest\Unit\UnitTest;
use RuntimeException;

use function array_diff;
use function bin2hex;
use function chmod;
use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function is_readable;
use function mkdir;
use function preg_match_all;
use function random_bytes;
use function restore_error_handler;
use function rmdir;
use function scandir;
use function set_error_handler;
use function sprintf;
use function sys_get_temp_dir;
use function unlink;

use const DIRECTORY_SEPARATOR;

class LlmsFullGeneratorTest extends UnitTest
{
    private const SEPARATOR = '<!-- ============================================================ -->';

    private string $workDir;
    private string $sourceDir;
    private string $pagesDir;
    private string $outputFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workDir = sprintf(
            '%s%slight-llms-%s',
            sys_get_temp_dir(),
            DIRECTORY_SEPARATOR,
            bin2hex(random_bytes(8))
        );

        $this->sourceDir  = $this->workDir . DIRECTORY_SEPARATOR . 'md-articles';
        $this->pagesDir   = $this->workDir . DIRECTORY_SEPARATOR . 'md-pages';
        $this->outputFile = $this->workDir . DIRECTORY_SEPARATOR . 'llms-full.txt';

        mkdir($this->sourceDir, 0775, true);
        mkdir($this->pagesDir, 0775, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->workDir);

        parent::tearDown();
    }

    public function testGetOutputFileReturnsTheConfiguredPath(): void
    {
        $this->assertSame($this->outputFile, $this->createGenerator()->getOutputFile());
    }

    public function testWriteReturnsTheNumberOfSectionsWritten(): void
    {
        $this->writeIndex();
        $this->writeArticle('news', 'first');
        $this->writeArticle('news', 'second');
        $this->writePage('api');

        $this->assertSame(4, $this->createGenerator()->write());
    }

    public function testWritePlacesTheIndexFirst(): void
    {
        $this->writeIndex();
        $this->writeArticle('news', 'a-post');

        $this->createGenerator()->write();

        $this->assertSame(['index.md', 'news/a-post.md'], $this->sectionLabels());
    }

    public function testWriteOmitsTheIndexWhenItDoesNotExist(): void
    {
        $this->writeArticle('news', 'a-post');

        $this->createGenerator()->write();

        $this->assertSame(['news/a-post.md'], $this->sectionLabels());
    }

    public function testWriteSortsArticlesByRelativePath(): void
    {
        $this->writeArticle('zebra', 'last');
        $this->writeArticle('alpha', 'first');
        $this->writeArticle('alpha', 'second');

        $this->createGenerator()->write();

        $this->assertSame(
            ['alpha/first.md', 'alpha/second.md', 'zebra/last.md'],
            $this->sectionLabels()
        );
    }

    public function testWriteAppendsPagesAfterTheArticles(): void
    {
        $this->writeIndex();
        $this->writeArticle('news', 'a-post');
        $this->writePage('api');
        $this->writePage('admin');

        $this->createGenerator()->write();

        $this->assertSame(
            ['index.md', 'news/a-post.md', 'md-pages/admin.md', 'md-pages/api.md'],
            $this->sectionLabels()
        );
    }

    public function testWriteLeavesPagesOutWhenNoPagesDirectoryIsConfigured(): void
    {
        $this->writeArticle('news', 'a-post');
        $this->writePage('api');

        $this->createGenerator(pagesDir: null)->write();

        $this->assertSame(['news/a-post.md'], $this->sectionLabels());
    }

    public function testWriteIgnoresNestedFilesUnderThePagesDirectory(): void
    {
        mkdir($this->pagesDir . DIRECTORY_SEPARATOR . 'nested', 0775, true);
        file_put_contents($this->pagesDir . '/nested/deep.md', 'deep');
        $this->writePage('api');

        $this->createGenerator()->write();

        $this->assertSame(['md-pages/api.md'], $this->sectionLabels());
    }

    public function testWriteIgnoresNonMarkdownFiles(): void
    {
        file_put_contents($this->sourceDir . '/index.md', 'index body');
        mkdir($this->sourceDir . DIRECTORY_SEPARATOR . 'news', 0775, true);
        file_put_contents($this->sourceDir . '/news/notes.txt', 'not markdown');
        file_put_contents($this->pagesDir . '/notes.txt', 'not markdown');

        $this->createGenerator()->write();

        $this->assertSame(['index.md'], $this->sectionLabels());
    }

    public function testWriteWrapsEachSectionInTheSeparatorAndItsLabel(): void
    {
        $this->writePage('api');

        $this->createGenerator()->write();

        $this->assertStringContainsString(
            sprintf("%s\n<!-- md-pages/api.md -->\n%s\n\n", self::SEPARATOR, self::SEPARATOR),
            $this->writtenOutput()
        );
    }

    public function testWriteSubstitutesThePlaceholderWithTheBaseUrl(): void
    {
        file_put_contents($this->sourceDir . '/index.md', 'Contact: {{base_url}}/contact/');

        $this->createGenerator(baseUrl: 'https://example.test')->write();

        $output = $this->writtenOutput();
        $this->assertStringContainsString('Contact: https://example.test/contact/', $output);
        $this->assertStringNotContainsString('{{base_url}}', $output);
    }

    public function testWriteSubstitutesThePlaceholderInPagesToo(): void
    {
        file_put_contents($this->pagesDir . '/api.md', 'Docs: {{base_url}}/api/');

        $this->createGenerator(baseUrl: 'https://example.test')->write();

        $this->assertStringContainsString('Docs: https://example.test/api/', $this->writtenOutput());
    }

    public function testWriteTrimsSurroundingWhitespaceFromEachSectionBody(): void
    {
        file_put_contents($this->pagesDir . '/api.md', "\n\n  # Body  \n\n\n");

        $this->createGenerator()->write();

        $this->assertStringEndsWith("# Body\n\n", $this->writtenOutput());
    }

    public function testWriteSeparatesSectionsByTwoBlankLinesAndEndsWithOne(): void
    {
        $this->writePage('admin');
        $this->writePage('api');

        $this->createGenerator()->write();

        $output = $this->writtenOutput();

        // Two blank lines between the end of one section body and the next section's separator.
        $this->assertStringContainsString("# admin\n\n\n" . self::SEPARATOR, $output);
        $this->assertStringEndsWith("\n\n", $output);
    }

    public function testWriteOverwritesAnExistingOutputFile(): void
    {
        file_put_contents($this->outputFile, 'stale content');
        $this->writePage('api');

        $this->createGenerator()->write();

        $this->assertStringNotContainsString('stale content', $this->writtenOutput());
    }

    public function testWriteIsIdempotent(): void
    {
        $this->writeIndex();
        $this->writeArticle('news', 'a-post');
        $this->writePage('api');

        $generator = $this->createGenerator();
        $generator->write();
        $first = $this->writtenOutput();
        $generator->write();

        $this->assertSame($first, $this->writtenOutput());
    }

    public function testWriteProducesOnlyTheTrailingBlankLinesWhenThereIsNothingToConcatenate(): void
    {
        $this->assertSame(0, $this->createGenerator()->write());
        $this->assertSame("\n\n", $this->writtenOutput());
    }

    public function testWriteThrowsWhenTheOutputFileCannotBeWritten(): void
    {
        $this->writePage('api');

        $generator = $this->createGenerator(
            outputFile: $this->workDir . DIRECTORY_SEPARATOR . 'missing' . DIRECTORY_SEPARATOR . 'out.txt'
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to write llms-full.txt.');

        $this->writeSilencingPhpWarnings($generator);
    }

    public function testWriteThrowsWhenASourceFileCannotBeRead(): void
    {
        $unreadable = $this->pagesDir . DIRECTORY_SEPARATOR . 'api.md';
        file_put_contents($unreadable, 'body');
        if (chmod($unreadable, 0000) === false || is_readable($unreadable)) {
            $this->markTestSkipped('Cannot make a file unreadable as this user.');
        }

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to read file: md-pages/api.md');

        try {
            $this->writeSilencingPhpWarnings($this->createGenerator());
        } finally {
            chmod($unreadable, 0644);
        }
    }

    /**
     * The failure paths under test make `file_get_contents`/`file_put_contents` emit a PHP warning
     * before returning false. That warning is the documented behaviour of the code being exercised,
     * not a problem with the test, so it is swallowed to keep the suite output clean.
     */
    private function writeSilencingPhpWarnings(LlmsFullGenerator $generator): void
    {
        set_error_handler(static fn (): bool => true);

        try {
            $generator->write();
        } finally {
            restore_error_handler();
        }
    }

    private function createGenerator(
        ?string $pagesDir = '',
        string $baseUrl = 'https://example.test',
        ?string $outputFile = null,
    ): LlmsFullGenerator {
        return new LlmsFullGenerator(
            $this->sourceDir,
            $outputFile ?? $this->outputFile,
            $baseUrl,
            $pagesDir === '' ? $this->pagesDir : $pagesDir,
        );
    }

    private function writeIndex(): void
    {
        file_put_contents($this->sourceDir . DIRECTORY_SEPARATOR . 'index.md', '# Index');
    }

    private function writeArticle(string $category, string $slug): void
    {
        $directory = $this->sourceDir . DIRECTORY_SEPARATOR . $category;
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        file_put_contents($directory . DIRECTORY_SEPARATOR . $slug . '.md', sprintf('# %s', $slug));
    }

    private function writePage(string $slug): void
    {
        file_put_contents($this->pagesDir . DIRECTORY_SEPARATOR . $slug . '.md', sprintf('# %s', $slug));
    }

    private function writtenOutput(): string
    {
        $contents = file_get_contents($this->outputFile);
        $this->assertIsString($contents);

        return $contents;
    }

    /**
     * The section labels written into the header comments, in the order they appear.
     *
     * @return list<string>
     */
    private function sectionLabels(): array
    {
        preg_match_all('/^<!-- ((?!=)[^ ]+\.md) -->$/m', $this->writtenOutput(), $matches);

        return $matches[1];
    }

    private function removeDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        foreach (array_diff((array) scandir($directory), ['.', '..']) as $entry) {
            $path = $directory . DIRECTORY_SEPARATOR . $entry;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }

        rmdir($directory);
    }
}
