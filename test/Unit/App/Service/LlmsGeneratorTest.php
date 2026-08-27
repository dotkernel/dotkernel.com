<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Service;

use Light\App\Service\LlmsGenerator;
use Light\Blog\Entity\Category;
use Light\Blog\Entity\Post;
use Light\Blog\Repository\PostRepository;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;
use RuntimeException;

use function array_diff;
use function bin2hex;
use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function mb_strpos;
use function mkdir;
use function random_bytes;
use function rmdir;
use function scandir;
use function sprintf;
use function sys_get_temp_dir;
use function unlink;

use const DIRECTORY_SEPARATOR;

class LlmsGeneratorTest extends UnitTest
{
    private string $workDir;
    private string $sourceDir;
    private string $pagesDir;
    private string $outputFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workDir = sprintf(
            '%s%slight-llms-index-%s',
            sys_get_temp_dir(),
            DIRECTORY_SEPARATOR,
            bin2hex(random_bytes(8))
        );

        $this->sourceDir  = $this->workDir . DIRECTORY_SEPARATOR . 'md-articles';
        $this->pagesDir   = $this->workDir . DIRECTORY_SEPARATOR . 'md-pages';
        $this->outputFile = $this->workDir . DIRECTORY_SEPARATOR . 'llms.txt';

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
    public function testWriteReturnsTheNumberOfCategorySectionsWritten(): void
    {
        $posts = [
            $this->createPost('A post', 'a-post', 'dotkernel', 'Dotkernel'),
            $this->createPost('Another post', 'another-post', 'best-practice', 'Best Practice'),
        ];

        $this->assertSame(2, $this->createGenerator($posts)->write());
    }

    /**
     * @throws Exception
     */
    public function testWriteReturnsZeroAndWritesOnlyTheHeaderWhenThereAreNoPublishedPosts(): void
    {
        $this->assertSame(0, $this->createGenerator()->write());

        $output = $this->writtenOutput();
        $this->assertStringStartsWith('# Dotkernel', $output);
        $this->assertStringContainsString('## Categories', $output);
        $this->assertStringNotContainsString('##  (', $output);
    }

    /**
     * @throws Exception
     */
    public function testWriteUsesTheConfiguredTitleAsTheHeading(): void
    {
        $this->createGenerator(title: 'Custom Blog Title')->write();

        $this->assertStringStartsWith('# Custom Blog Title', $this->writtenOutput());
    }

    /**
     * @throws Exception
     */
    public function testWriteOrdersCategorySectionsAccordingToTheCuratedOrder(): void
    {
        // Inserted in the "wrong" order on purpose - php-development is curated after dotkernel.
        $posts = [
            $this->createPost('PHP post', 'php-post', 'php-development', 'PHP Development'),
            $this->createPost('Dotkernel post', 'dotkernel-post', 'dotkernel', 'Dotkernel'),
        ];

        $this->createGenerator($posts)->write();

        $output      = $this->writtenOutput();
        $dotkernelAt = mb_strpos($output, '## Dotkernel (');
        $phpDevAt    = mb_strpos($output, '## PHP Development (');

        $this->assertNotFalse($dotkernelAt);
        $this->assertNotFalse($phpDevAt);
        $this->assertLessThan($phpDevAt, $dotkernelAt);
    }

    /**
     * @throws Exception
     */
    public function testWriteAppendsCategoriesNotInTheCuratedOrderAfterTheCuratedOnesSortedByPostCountDescending(): void
    {
        $posts = [
            $this->createPost('Curated post', 'curated-post', 'dotkernel', 'Dotkernel'),
            $this->createPost('Small extra post', 'small-extra-post', 'extra-small', 'Extra Small'),
            $this->createPost('Big extra post one', 'big-extra-post-one', 'extra-big', 'Extra Big'),
            $this->createPost('Big extra post two', 'big-extra-post-two', 'extra-big', 'Extra Big'),
        ];

        $this->createGenerator($posts)->write();

        $output      = $this->writtenOutput();
        $dotkernelAt = mb_strpos($output, '## Dotkernel (');
        $bigAt       = mb_strpos($output, '## Extra Big (2 posts)');
        $smallAt     = mb_strpos($output, '## Extra Small (1 post)');

        $this->assertNotFalse($dotkernelAt);
        $this->assertNotFalse($bigAt);
        $this->assertNotFalse($smallAt);
        $this->assertLessThan($bigAt, $dotkernelAt);
        $this->assertLessThan($smallAt, $bigAt);
    }

    /**
     * @throws Exception
     */
    public function testWriteSortsPostsWithinACategoryAlphabeticallyByTitleCaseInsensitively(): void
    {
        $posts = [
            $this->createPost('zebra post', 'zebra-post', 'dotkernel', 'Dotkernel'),
            $this->createPost('Alpha post', 'alpha-post', 'dotkernel', 'Dotkernel'),
            $this->createPost('banana post', 'banana-post', 'dotkernel', 'Dotkernel'),
        ];

        $this->createGenerator($posts)->write();

        $output   = $this->writtenOutput();
        $alphaAt  = mb_strpos($output, '[Alpha post]');
        $bananaAt = mb_strpos($output, '[banana post]');
        $zebraAt  = mb_strpos($output, '[zebra post]');

        $this->assertNotFalse($alphaAt);
        $this->assertNotFalse($bananaAt);
        $this->assertNotFalse($zebraAt);
        $this->assertLessThan($bananaAt, $alphaAt);
        $this->assertLessThan($zebraAt, $bananaAt);
    }

    /**
     * @throws Exception
     */
    public function testWriteIncludesTheBlurbLineForACuratedCategory(): void
    {
        $posts = [$this->createPost('A post', 'a-post', 'dotkernel', 'Dotkernel')];

        $this->createGenerator($posts)->write();

        $this->assertStringContainsString(
            "## Dotkernel (1 post)\n\n*the core framework",
            $this->writtenOutput()
        );
    }

    /**
     * @throws Exception
     */
    public function testWriteOmitsTheBlurbLineForAnUncuratedCategory(): void
    {
        $posts = [$this->createPost('A post', 'a-post', 'unlisted', 'Unlisted')];

        $this->createGenerator($posts)->write();

        $this->assertStringContainsString(
            "## Unlisted (1 post)\n\n- [A post]",
            $this->writtenOutput()
        );
    }

    /**
     * @throws Exception
     */
    public function testWriteUsesSingularPostWordingForACategoryWithExactlyOnePost(): void
    {
        $posts = [$this->createPost('A post', 'a-post', 'dotkernel', 'Dotkernel')];

        $this->createGenerator($posts)->write();

        $this->assertStringContainsString('## Dotkernel (1 post)', $this->writtenOutput());
        $this->assertStringNotContainsString('(1 posts)', $this->writtenOutput());
    }

    /**
     * @throws Exception
     */
    public function testWriteBuildsEachPostLinkFromTheBaseUrlCategorySlugAndPostSlugAsMarkdown(): void
    {
        $posts = [$this->createPost('A post', 'a-post', 'dotkernel', 'Dotkernel')];

        $this->createGenerator($posts, baseUrl: 'https://example.test')->write();

        $this->assertStringContainsString(
            '[A post](https://example.test/dotkernel/a-post.md):',
            $this->writtenOutput()
        );
    }

    /**
     * @throws Exception
     */
    public function testWriteBuildsTheDocsSectionLinksFromTheBaseUrl(): void
    {
        $this->createGenerator(baseUrl: 'https://example.test')->write();

        $output = $this->writtenOutput();
        $this->assertStringContainsString('[Blog](https://example.test/blog/):', $output);
        $this->assertStringContainsString('[Contact](https://example.test/contact/)', $output);
    }

    /**
     * @throws Exception
     */
    public function testWritePrefersTheTitleAndDescriptionFromTheArticleFrontMatterOverThePostEntity(): void
    {
        $this->writeArticle('dotkernel', 'a-post', 'Clean title', 'Clean description.');
        $posts = [$this->createPost('Raw  title', 'a-post', 'dotkernel', 'Dotkernel', 'Raw excerpt.')];

        $this->createGenerator($posts)->write();

        $output = $this->writtenOutput();
        $this->assertStringContainsString('[Clean title](', $output);
        $this->assertStringContainsString(': Clean description.', $output);
        $this->assertStringNotContainsString('Raw  title', $output);
        $this->assertStringNotContainsString('Raw excerpt.', $output);
    }

    /**
     * @throws Exception
     */
    public function testWriteFallsBackToThePostEntityWhenTheArticleFileIsMissing(): void
    {
        $posts = [$this->createPost('Raw  title', 'a-post', 'dotkernel', 'Dotkernel', 'Raw excerpt.')];

        $this->createGenerator($posts)->write();

        $output = $this->writtenOutput();
        $this->assertStringContainsString('[Raw  title](', $output);
        $this->assertStringContainsString(': Raw excerpt.', $output);
    }

    /**
     * @throws Exception
     */
    public function testWriteOmitsThePagesSectionWhenNoPagesDirectoryIsConfigured(): void
    {
        $this->writePage('api', 'Dotkernel API | Tagline');

        $this->createGenerator(pagesDir: null)->write();

        $this->assertStringNotContainsString('## Pages', $this->writtenOutput());
    }

    /**
     * @throws Exception
     */
    public function testWriteOmitsThePagesSectionWhenThePagesDirectoryIsEmpty(): void
    {
        $this->createGenerator()->write();

        $this->assertStringNotContainsString('## Pages', $this->writtenOutput());
    }

    /**
     * @throws Exception
     */
    public function testWriteIncludesEachPageSortedAlphabeticallyByTitle(): void
    {
        $this->writePage('api', 'Zebra page | Some tagline');
        $this->writePage('admin', 'Alpha page | Some tagline');

        $this->createGenerator(baseUrl: 'https://example.test')->write();

        $output  = $this->writtenOutput();
        $alphaAt = mb_strpos($output, '[Alpha page]');
        $zebraAt = mb_strpos($output, '[Zebra page]');

        $this->assertStringContainsString('## Pages', $output);
        $this->assertNotFalse($alphaAt);
        $this->assertNotFalse($zebraAt);
        $this->assertLessThan($zebraAt, $alphaAt);
    }

    /**
     * @throws Exception
     */
    public function testWriteTrimsThePageTitleAtTheTaglineSeparator(): void
    {
        $this->writePage('api', 'Dotkernel API | Open-source REST API skeleton for PHP', 'The description.');

        $this->createGenerator(baseUrl: 'https://example.test')->write();

        $this->assertStringContainsString(
            '- [Dotkernel API](https://example.test/md-pages/api.md): The description.',
            $this->writtenOutput()
        );
        $this->assertStringNotContainsString('Open-source REST API skeleton for PHP]', $this->writtenOutput());
    }

    /**
     * @throws Exception
     */
    public function testWriteSkipsAPageMissingATitleOrDescription(): void
    {
        $this->writePage('api', 'Dotkernel API | Tagline', null);
        $this->writePage('admin', 'Dotkernel Admin | Tagline', 'Has a description.');

        $this->createGenerator()->write();

        $output = $this->writtenOutput();
        $this->assertStringNotContainsString('[Dotkernel API]', $output);
        $this->assertStringContainsString('[Dotkernel Admin]', $output);
    }

    /**
     * @throws Exception
     */
    public function testWritePlacesThePagesSectionBetweenDocsAndCategories(): void
    {
        $this->writePage('api', 'Dotkernel API | Tagline');
        $posts = [$this->createPost('A post', 'a-post', 'dotkernel', 'Dotkernel')];

        $this->createGenerator($posts)->write();

        $output       = $this->writtenOutput();
        $docsAt       = mb_strpos($output, '## Docs');
        $pagesAt      = mb_strpos($output, '## Pages');
        $categoriesAt = mb_strpos($output, '## Categories');

        $this->assertNotFalse($docsAt);
        $this->assertNotFalse($pagesAt);
        $this->assertNotFalse($categoriesAt);
        $this->assertLessThan($pagesAt, $docsAt);
        $this->assertLessThan($categoriesAt, $pagesAt);
    }

    /**
     * @throws Exception
     */
    public function testWriteOverwritesAnExistingOutputFile(): void
    {
        file_put_contents($this->outputFile, 'stale content');

        $this->createGenerator()->write();

        $this->assertStringNotContainsString('stale content', $this->writtenOutput());
    }

    /**
     * @throws Exception
     */
    public function testWriteThrowsWhenTheOutputFileCannotBeWritten(): void
    {
        $generator = $this->createGenerator(
            outputFile: $this->workDir . DIRECTORY_SEPARATOR . 'missing' . DIRECTORY_SEPARATOR . 'llms.txt'
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to write llms.txt.');

        @$generator->write();
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
        string $title = 'Dotkernel',
    ): LlmsGenerator {
        $postRepository = $this->createStub(PostRepository::class);
        $postRepository->method('getPublishedPosts')->willReturn($posts);

        return new LlmsGenerator(
            $postRepository,
            $this->sourceDir,
            $outputFile ?? $this->outputFile,
            $baseUrl,
            $pagesDir === '' ? $this->pagesDir : $pagesDir,
            $title,
        );
    }

    /**
     * @throws Exception
     */
    private function createPost(
        string $title,
        string $slug,
        string $categorySlug,
        string $categoryName,
        string $excerpt = 'An excerpt.',
    ): Post {
        $category = $this->createStub(Category::class);
        $category->method('getSlug')->willReturn($categorySlug);
        $category->method('getName')->willReturn($categoryName);

        $post = $this->createStub(Post::class);
        $post->method('getTitle')->willReturn($title);
        $post->method('getSlug')->willReturn($slug);
        $post->method('getCategory')->willReturn($category);
        $post->method('getExcerpt')->willReturn($excerpt);

        return $post;
    }

    private function writeArticle(string $categorySlug, string $slug, string $title, string $description): void
    {
        $directory = $this->sourceDir . DIRECTORY_SEPARATOR . $categorySlug;
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        file_put_contents(
            $directory . DIRECTORY_SEPARATOR . $slug . '.md',
            sprintf("---\ntitle: \"%s\"\ndescription: \"%s\"\n---\n\nBody.\n", $title, $description)
        );
    }

    private function writePage(string $slug, string $title, ?string $description = 'A description.'): void
    {
        $frontMatter = $description === null
            ? sprintf("---\ntitle: \"%s\"\n---\n\nBody.\n", $title)
            : sprintf("---\ntitle: \"%s\"\ndescription: \"%s\"\n---\n\nBody.\n", $title, $description);

        file_put_contents($this->pagesDir . DIRECTORY_SEPARATOR . $slug . '.md', $frontMatter);
    }

    private function writtenOutput(): string
    {
        $contents = file_get_contents($this->outputFile);
        $this->assertIsString($contents);

        return $contents;
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
