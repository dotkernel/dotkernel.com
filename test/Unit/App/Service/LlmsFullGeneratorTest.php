<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Service;

use Light\App\Service\LlmsFullGenerator;
use Light\Blog\Entity\Category;
use Light\Blog\Entity\Post;
use Light\Blog\Repository\PostRepository;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;
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

    /**
     * @throws Exception
     */
    public function testWriteReturnsTheNumberOfSectionsWritten(): void
    {
        $this->writeIndex();
        $this->writeArticle('news', 'first');
        $this->writeArticle('news', 'second');
        $this->writePage('api');

        $posts = [$this->createPost('news', 'first'), $this->createPost('news', 'second')];

        $this->assertSame(4, $this->createGenerator($posts)->write());
    }

    /**
     * @throws Exception
     */
    public function testWritePlacesTheIndexFirst(): void
    {
        $this->writeIndex();
        $this->writeArticle('news', 'a-post');

        $this->createGenerator([$this->createPost('news', 'a-post')])->write();

        $this->assertSame(['index.md', 'news/a-post.md'], $this->sectionLabels());
    }

    /**
     * @throws Exception
     */
    public function testWriteOmitsTheIndexWhenItDoesNotExist(): void
    {
        $this->writeArticle('news', 'a-post');

        $this->createGenerator([$this->createPost('news', 'a-post')])->write();

        $this->assertSame(['news/a-post.md'], $this->sectionLabels());
    }

    /**
     * @throws Exception
     */
    public function testWriteSortsArticlesByRelativePath(): void
    {
        $this->writeArticle('zebra', 'last');
        $this->writeArticle('alpha', 'first');
        $this->writeArticle('alpha', 'second');

        $posts = [
            $this->createPost('zebra', 'last'),
            $this->createPost('alpha', 'first'),
            $this->createPost('alpha', 'second'),
        ];

        $this->createGenerator($posts)->write();

        $this->assertSame(
            ['alpha/first.md', 'alpha/second.md', 'zebra/last.md'],
            $this->sectionLabels()
        );
    }

    /**
     * @throws Exception
     */
    public function testWriteOnlyIncludesArticlesForPostsReturnedAsPublished(): void
    {
        $this->writeArticle('news', 'published-post');
        $this->writeArticle('news', 'unpublished-post');

        $this->createGenerator([$this->createPost('news', 'published-post')])->write();

        $this->assertSame(['news/published-post.md'], $this->sectionLabels());
    }

    /**
     * @throws Exception
     */
    public function testWriteThrowsWhenAPublishedPostHasNoMatchingMarkdownFile(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing markdown file for published post: news/missing-post.md');

        $this->createGenerator([$this->createPost('news', 'missing-post')])->write();
    }

    /**
     * @throws Exception
     */
    public function testWriteAppendsPagesAfterTheArticles(): void
    {
        $this->writeIndex();
        $this->writeArticle('news', 'a-post');
        $this->writePage('api');
        $this->writePage('admin');

        $this->createGenerator([$this->createPost('news', 'a-post')])->write();

        $this->assertSame(
            ['index.md', 'news/a-post.md', 'md-pages/admin.md', 'md-pages/api.md'],
            $this->sectionLabels()
        );
    }

    /**
     * @throws Exception
     */
    public function testWriteLeavesPagesOutWhenNoPagesDirectoryIsConfigured(): void
    {
        $this->writeArticle('news', 'a-post');
        $this->writePage('api');

        $this->createGenerator([$this->createPost('news', 'a-post')], pagesDir: null)->write();

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

    public function testWriteIgnoresNonMarkdownFilesInThePagesDirectory(): void
    {
        file_put_contents($this->sourceDir . '/index.md', 'index body');
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

    /**
     * @throws Exception
     */
    public function testWriteIsIdempotent(): void
    {
        $this->writeIndex();
        $this->writeArticle('news', 'a-post');
        $this->writePage('api');

        $generator = $this->createGenerator([$this->createPost('news', 'a-post')]);
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
     * @throws Exception
     */
    public function testWriteStripsTheFaqSectionFromAnArticle(): void
    {
        $this->writeArticle('news', 'a-post', "# A post\n\nBody text.\n\n## FAQ\n\n**Q: Question?**\nA: Answer.\n");

        $this->createGenerator([$this->createPost('news', 'a-post')])->write();

        $output = $this->writtenOutput();
        $this->assertStringContainsString('Body text.', $output);
        $this->assertStringNotContainsString('## FAQ', $output);
        $this->assertStringNotContainsString('Question?', $output);
    }

    /**
     * @throws Exception
     */
    public function testWriteKeepsAnySectionThatFollowsTheFaqSection(): void
    {
        $this->writeArticle(
            'news',
            'a-post',
            "# A post\n\n## FAQ\n\n**Q: Question?**\nA: Answer.\n\n## Resources\n\n- A link\n"
        );

        $this->createGenerator([$this->createPost('news', 'a-post')])->write();

        $output = $this->writtenOutput();
        $this->assertStringNotContainsString('## FAQ', $output);
        $this->assertStringContainsString('## Resources', $output);
        $this->assertStringContainsString('- A link', $output);
    }

    public function testWriteStripsTheFaqSectionFromAPageToo(): void
    {
        file_put_contents($this->pagesDir . '/api.md', "# API\n\n## FAQ\n\n**Q: Question?**\nA: Answer.\n");

        $this->createGenerator()->write();

        $output = $this->writtenOutput();
        $this->assertStringNotContainsString('## FAQ', $output);
        $this->assertStringNotContainsString('Question?', $output);
    }

    /**
     * @throws Exception
     */
    public function testWriteDoesNotStripAHeadingThatIsNotExactlyFaq(): void
    {
        $this->writeArticle('news', 'a-post', "# A post\n\n## Common questions\n\nStill here.\n");

        $this->createGenerator([$this->createPost('news', 'a-post')])->write();

        $output = $this->writtenOutput();
        $this->assertStringContainsString('## Common questions', $output);
        $this->assertStringContainsString('Still here.', $output);
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

    /**
     * @param list<Post> $posts
     * @throws Exception
     */
    private function createGenerator(
        array $posts = [],
        ?string $outputFile = null,
        string $baseUrl = 'https://example.test',
        ?string $pagesDir = '',
    ): LlmsFullGenerator {
        $postRepository = $this->createStub(PostRepository::class);
        $postRepository->method('getPublishedPosts')->willReturn($posts);

        return new LlmsFullGenerator(
            $postRepository,
            $this->sourceDir,
            $outputFile ?? $this->outputFile,
            $baseUrl,
            $pagesDir === '' ? $this->pagesDir : $pagesDir,
        );
    }

    /**
     * @throws Exception
     */
    private function createPost(string $categorySlug, string $slug): Post
    {
        $category = $this->createStub(Category::class);
        $category->method('getSlug')->willReturn($categorySlug);

        $post = $this->createStub(Post::class);
        $post->method('getSlug')->willReturn($slug);
        $post->method('getCategory')->willReturn($category);

        return $post;
    }

    private function writeIndex(): void
    {
        file_put_contents($this->sourceDir . DIRECTORY_SEPARATOR . 'index.md', '# Index');
    }

    private function writeArticle(string $category, string $slug, ?string $body = null): void
    {
        $directory = $this->sourceDir . DIRECTORY_SEPARATOR . $category;
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        file_put_contents($directory . DIRECTORY_SEPARATOR . $slug . '.md', $body ?? sprintf('# %s', $slug));
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
