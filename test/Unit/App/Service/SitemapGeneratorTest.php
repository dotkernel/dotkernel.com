<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Service;

use DateTimeImmutable;
use DateTimeZone;
use Light\App\Service\SitemapGenerator;
use Light\Blog\Entity\Author;
use Light\Blog\Entity\Category;
use Light\Blog\Entity\Post;
use Light\Blog\Repository\AuthorRepository;
use Light\Blog\Repository\CategoryRepository;
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
    /** Homepage, /blog/, /categories/, /authors/ and the packages-lifecycle page are always present. */
    private const FIXED_URL_COUNT = 5;

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
        $this->assertSame($this->sitemapFile, $this->createGenerator()->getSitemapFile());
    }

    /**
     * @throws Exception
     */
    public function testWriteAlwaysIncludesTheFixedPagesEvenWithoutAnyContent(): void
    {
        $generator = $this->createGenerator();

        $this->assertSame(self::FIXED_URL_COUNT, $generator->write());

        $urls = $this->loadSitemap()->url;
        $this->assertCount(self::FIXED_URL_COUNT, $urls);
        $this->assertSame('https://example.test/', (string) $urls[0]->loc);
        $this->assertSame('https://example.test/blog/', (string) $urls[1]->loc);
        $this->assertSame('https://example.test/categories/', (string) $urls[2]->loc);
        $this->assertSame('https://example.test/authors/', (string) $urls[3]->loc);
        $this->assertSame(
            'https://example.test/dotkernel-packages-oss-lifecycle/',
            (string) $urls[4]->loc
        );
        $this->assertCount(0, $urls[0]->lastmod);
    }

    /**
     * @throws Exception
     */
    public function testWriteAddsOneUrlEntryPerConfiguredStaticPage(): void
    {
        $generator = $this->createGenerator(pageRoutes: ['contact']);

        $this->assertSame(self::FIXED_URL_COUNT + 1, $generator->write());

        $urls = $this->loadSitemap()->url;
        $this->assertSame('https://example.test/contact/', (string) $urls[self::FIXED_URL_COUNT]->loc);
        $this->assertCount(0, $urls[self::FIXED_URL_COUNT]->lastmod);
    }

    /**
     * @throws Exception
     */
    public function testWriteSetsLastmodOnHomeBlogCategoriesAndAuthorsFromTheNewestPost(): void
    {
        $olderPost = $this->createPost('older-post', 'news', '2026-08-01 10:00:00');
        $newerPost = $this->createPost('newer-post', 'news', '2026-08-05 10:00:00');

        $generator = $this->createGenerator(posts: [$olderPost, $newerPost]);
        $generator->write();

        $urls = $this->loadSitemap()->url;
        foreach ([0, 1, 2, 3] as $index) {
            $this->assertSame('2026-08-05T10:00:00+00:00', (string) $urls[$index]->lastmod);
        }
    }

    /**
     * @throws Exception
     */
    public function testWriteAddsOneUrlEntryPerCategoryWithItsLastModifiedDateDerivedFromItsNewestPost(): void
    {
        $category = $this->createCategory('news');
        $posts    = [
            $this->createPost('older-post', 'news', '2026-08-01 10:00:00'),
            $this->createPost('newer-post', 'news', '2026-08-03 10:00:00'),
            $this->createPost('other-category-post', 'other', '2026-08-09 10:00:00'),
        ];

        $generator = $this->createGenerator(categories: [$category], posts: $posts);
        $generator->write();

        $urls = $this->loadSitemap()->url;
        $this->assertSame('https://example.test/category/news/', (string) $urls[self::FIXED_URL_COUNT]->loc);
        $this->assertSame(
            '2026-08-03T10:00:00+00:00',
            (string) $urls[self::FIXED_URL_COUNT]->lastmod
        );
    }

    /**
     * @throws Exception
     */
    public function testWriteAddsOneUrlEntryPerAuthor(): void
    {
        $author = $this->createStub(Author::class);
        $author->method('getSlug')->willReturn('jane-doe');

        $generator = $this->createGenerator(authors: [$author]);

        $this->assertSame(self::FIXED_URL_COUNT + 1, $generator->write());

        $urls = $this->loadSitemap()->url;
        $this->assertSame('https://example.test/author/jane-doe/', (string) $urls[self::FIXED_URL_COUNT]->loc);
        $this->assertCount(0, $urls[self::FIXED_URL_COUNT]->lastmod);
    }

    /**
     * @throws Exception
     */
    public function testWriteAddsOneUrlEntryPerAuthorWithItsLastModifiedDateDerivedFromItsNewestPost(): void
    {
        $author = $this->createStub(Author::class);
        $author->method('getSlug')->willReturn('jane-doe');

        $posts = [
            $this->createPost('older-post', 'news', '2026-08-01 10:00:00', 'jane-doe'),
            $this->createPost('newer-post', 'news', '2026-08-04 10:00:00', 'jane-doe'),
            $this->createPost('other-author-post', 'news', '2026-08-09 10:00:00', 'john-doe'),
        ];

        $generator = $this->createGenerator(authors: [$author], posts: $posts);
        $generator->write();

        $urls = $this->loadSitemap()->url;
        $this->assertSame('https://example.test/author/jane-doe/', (string) $urls[self::FIXED_URL_COUNT]->loc);
        $this->assertSame(
            '2026-08-04T10:00:00+00:00',
            (string) $urls[self::FIXED_URL_COUNT]->lastmod
        );
    }

    /**
     * @throws Exception
     */
    public function testWriteAddsOneUrlEntryPerPostWithACategoryQualifiedLink(): void
    {
        $post = $this->createPost('a-post', 'news', '2026-08-01 10:00:00');
        $this->createGenerator(posts: [$post])->write();

        $urls = $this->loadSitemap()->url;

        $this->assertCount(self::FIXED_URL_COUNT + 1, $urls);
        $this->assertSame(
            'https://example.test/news/a-post/',
            (string) $urls[self::FIXED_URL_COUNT]->loc
        );
        $this->assertSame(
            '2026-08-01T10:00:00+00:00',
            (string) $urls[self::FIXED_URL_COUNT]->lastmod
        );
    }

    /**
     * DOMDocument::save() emits a native PHP warning on failure in addition to the return value
     * this method already checks; suppress it so it doesn't surface as a spurious test warning.
     *
     * @throws Exception
     */
    public function testWriteThrowsWhenTheSitemapFileCannotBeWritten(): void
    {
        $generator = $this->createGenerator(sitemapFile: '/nonexistent-directory/sitemap.xml');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to write sitemap.');

        @$generator->write();
    }

    /**
     * @param list<Post> $posts
     * @param list<Category> $categories
     * @param list<Author> $authors
     * @param list<string> $pageRoutes
     * @throws Exception
     */
    private function createGenerator(
        array $posts = [],
        array $categories = [],
        array $authors = [],
        array $pageRoutes = [],
        ?string $sitemapFile = null,
    ): SitemapGenerator {
        $postRepository = $this->createStub(PostRepository::class);
        $postRepository->method('getPublishedPosts')->willReturn($posts);

        $categoryRepository = $this->createStub(CategoryRepository::class);
        $categoryRepository->method('getCategoriesWithPublishedPosts')->willReturn($categories);

        $authorRepository = $this->createStub(AuthorRepository::class);
        $authorRepository->method('getAuthorsWithPublishedPosts')->willReturn($authors);

        return new SitemapGenerator(
            $postRepository,
            $categoryRepository,
            $authorRepository,
            $pageRoutes,
            $sitemapFile ?? $this->sitemapFile,
            'https://example.test',
        );
    }

    /**
     * @throws Exception
     */
    private function createPost(
        string $slug,
        string $categorySlug,
        string $postDate = '2026-08-01 10:00:00',
        string $authorSlug = 'author',
    ): Post {
        $category = $this->createStub(Category::class);
        $category->method('getSlug')->willReturn($categorySlug);

        $author = $this->createStub(Author::class);
        $author->method('getSlug')->willReturn($authorSlug);

        $post = $this->createStub(Post::class);
        $post->method('getSlug')->willReturn($slug);
        $post->method('getCategory')->willReturn($category);
        $post->method('getAuthor')->willReturn($author);
        $post->method('getPostDate')->willReturn(new DateTimeImmutable($postDate, new DateTimeZone('UTC')));

        return $post;
    }

    /**
     * @throws Exception
     */
    private function createCategory(string $slug): Category
    {
        $category = $this->createStub(Category::class);
        $category->method('getSlug')->willReturn($slug);

        return $category;
    }

    private function loadSitemap(): SimpleXMLElement
    {
        $this->assertFileExists($this->sitemapFile);

        $xml = simplexml_load_file($this->sitemapFile);
        $this->assertInstanceOf(SimpleXMLElement::class, $xml);

        return $xml;
    }
}
