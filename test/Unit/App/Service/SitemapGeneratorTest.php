<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Service;

use DateTimeImmutable;
use DateTimeZone;
use Light\App\Service\SitemapGenerator;
use Light\Blog\Entity\Category;
use Light\Blog\Entity\Post;
use Light\Blog\Repository\PostRepository;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;
use RuntimeException;
use SimpleXMLElement;

use function bin2hex;
use function dirname;
use function is_dir;
use function is_file;
use function mkdir;
use function random_bytes;
use function rmdir;
use function simplexml_load_file;
use function sprintf;
use function sys_get_temp_dir;
use function unlink;

use const DIRECTORY_SEPARATOR;

class SitemapGeneratorTest extends UnitTest
{
    private string $sitemapFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sitemapFile = sprintf(
            '%s%slight-sitemap-%s%ssitemap.xml',
            sys_get_temp_dir(),
            DIRECTORY_SEPARATOR,
            bin2hex(random_bytes(8)),
            DIRECTORY_SEPARATOR
        );

        mkdir(dirname($this->sitemapFile), 0775, true);
    }

    protected function tearDown(): void
    {
        if (is_file($this->sitemapFile)) {
            unlink($this->sitemapFile);
        }

        $directory = dirname($this->sitemapFile);
        if (is_dir($directory)) {
            rmdir($directory);
        }

        parent::tearDown();
    }

    public function testGetSitemapFileReturnsTheConfiguredPath(): void
    {
        $this->assertSame($this->sitemapFile, $this->createGenerator([])->getSitemapFile());
    }

    /**
     * The count includes the homepage entry in addition to one entry per post.
     *
     * @throws Exception
     */
    public function testWriteReturnsTheNumberOfPostsPlusTheHomepage(): void
    {
        $generator = $this->createGenerator([
            $this->createPost('first-post', 'news'),
            $this->createPost('second-post', 'news'),
        ]);

        $this->assertSame(3, $generator->write());
    }

    /**
     * @throws Exception
     */
    public function testWriteAlwaysIncludesTheHomepageEvenWithoutPosts(): void
    {
        $generator = $this->createGenerator([]);

        $this->assertSame(1, $generator->write());

        $urls = $this->loadSitemap()->url;
        $this->assertCount(1, $urls);
        $this->assertSame('https://example.test', (string) $urls[0]->loc);
        $this->assertCount(0, $urls[0]->lastmod);
    }

    /**
     * @throws Exception
     */
    public function testWriteAddsOneUrlEntryPerPostWithACategoryQualifiedLink(): void
    {
        $post = $this->createPost('a-post', 'news', '2026-08-01 10:00:00');
        $this->createGenerator([$post])->write();

        $urls = $this->loadSitemap()->url;

        $this->assertCount(2, $urls);
        $this->assertSame('https://example.test/news/a-post/', (string) $urls[1]->loc);
        $this->assertSame('2026-08-01T10:00:00+00:00', (string) $urls[1]->lastmod);
    }

    /**
     * DOMDocument::save() emits a native PHP warning on failure in addition to the return value
     * this method already checks; suppress it so it doesn't surface as a spurious test warning.
     *
     * @throws Exception
     */
    public function testWriteThrowsWhenTheSitemapFileCannotBeWritten(): void
    {
        $generator = $this->createGenerator([], sitemapFile: '/nonexistent-directory/sitemap.xml');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to write sitemap.');

        @$generator->write();
    }

    /**
     * @param list<Post> $posts
     * @throws Exception
     */
    private function createGenerator(array $posts, ?string $sitemapFile = null): SitemapGenerator
    {
        $postRepository = $this->createStub(PostRepository::class);
        $postRepository->method('getPublishedPosts')->willReturn($posts);

        return new SitemapGenerator(
            $postRepository,
            $sitemapFile ?? $this->sitemapFile,
            'https://example.test',
        );
    }

    /**
     * @throws Exception
     */
    private function createPost(string $slug, string $categorySlug, string $postDate = '2026-08-01 10:00:00'): Post
    {
        $category = $this->createStub(Category::class);
        $category->method('getSlug')->willReturn($categorySlug);

        $post = $this->createStub(Post::class);
        $post->method('getSlug')->willReturn($slug);
        $post->method('getCategory')->willReturn($category);
        $post->method('getPostDate')->willReturn(new DateTimeImmutable($postDate, new DateTimeZone('UTC')));

        return $post;
    }

    private function loadSitemap(): SimpleXMLElement
    {
        $this->assertFileExists($this->sitemapFile);

        $xml = simplexml_load_file($this->sitemapFile);
        $this->assertInstanceOf(SimpleXMLElement::class, $xml);

        return $xml;
    }
}
