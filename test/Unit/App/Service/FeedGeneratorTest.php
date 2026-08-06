<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Service;

use DateTimeImmutable;
use Light\App\Service\FeedGenerator;
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

class FeedGeneratorTest extends UnitTest
{
    private string $feedFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->feedFile = sprintf(
            '%s%slight-feed-%s%sfeed.xml',
            sys_get_temp_dir(),
            DIRECTORY_SEPARATOR,
            bin2hex(random_bytes(8)),
            DIRECTORY_SEPARATOR
        );

        mkdir(dirname($this->feedFile), 0775, true);
    }

    protected function tearDown(): void
    {
        if (is_file($this->feedFile)) {
            unlink($this->feedFile);
        }

        $directory = dirname($this->feedFile);
        if (is_dir($directory)) {
            rmdir($directory);
        }

        parent::tearDown();
    }

    public function testGetFeedFileReturnsTheConfiguredPath(): void
    {
        $this->assertSame($this->feedFile, $this->createGenerator([])->getFeedFile());
    }

    /**
     * @throws Exception
     */
    public function testWriteReturnsTheNumberOfPostsWritten(): void
    {
        $generator = $this->createGenerator([
            $this->createPost('First post', 'first-post', 'news'),
            $this->createPost('Second post', 'second-post', 'news'),
        ]);

        $this->assertSame(2, $generator->write());
    }

    /**
     * @throws Exception
     */
    public function testWriteProducesAChannelWithNoItemsWhenThereAreNoPosts(): void
    {
        $this->createGenerator([])->write();

        $xml = $this->loadFeed();

        $this->assertSame('Light Blog', (string) $xml->channel->title);
        $this->assertSame('https://example.test', (string) $xml->channel->link);
        $this->assertCount(0, $xml->channel->item);
    }

    /**
     * @throws Exception
     */
    public function testWriteFallsBackToTheExcerptWhenTlDrIsMissing(): void
    {
        $post = $this->createPost('A post', 'a-post', 'news', tlDr: null, excerpt: 'The excerpt');
        $this->createGenerator([$post])->write();

        $this->assertSame('The excerpt', (string) $this->loadFeed()->channel->item->description);
    }

    /**
     * @throws Exception
     */
    public function testWritePrefersTlDrOverTheExcerptWhenBothArePresent(): void
    {
        $post = $this->createPost('A post', 'a-post', 'news', tlDr: 'The tl;dr', excerpt: 'The excerpt');
        $this->createGenerator([$post])->write();

        $this->assertSame('The tl;dr', (string) $this->loadFeed()->channel->item->description);
    }

    /**
     * @throws Exception
     */
    public function testWriteBuildsTheItemLinkFromCategoryAndPostSlugs(): void
    {
        $post = $this->createPost('A post', 'a-post', 'news');
        $this->createGenerator([$post])->write();

        $this->assertSame('https://example.test/news/a-post/', (string) $this->loadFeed()->channel->item->link);
        $this->assertSame('https://example.test/news/a-post/', (string) $this->loadFeed()->channel->item->guid);
    }

    /**
     * @throws Exception
     */
    public function testWriteUsesTheConfiguredFallbackImageWhenThePostHasNone(): void
    {
        $post = $this->createPost('A post', 'a-post', 'news', openGraphImage: null);
        $this->createGenerator([$post], image: 'https://example.test/default.png')->write();

        $xml   = $this->loadFeed();
        $media = $xml->channel->item->children('media', true)->content;

        $this->assertSame('https://example.test/default.png', (string) $media->attributes()['url']);
    }

    /**
     * @throws Exception
     */
    public function testWriteQualifiesThePostsOwnOpenGraphImageWithTheBaseUrl(): void
    {
        $post = $this->createPost('A post', 'a-post', 'news', openGraphImage: '/uploads/a-post.png');
        $this->createGenerator([$post])->write();

        $xml   = $this->loadFeed();
        $media = $xml->channel->item->children('media', true)->content;

        $this->assertSame('https://example.test/uploads/a-post.png', (string) $media->attributes()['url']);
    }

    /**
     * DOMDocument::save() emits a native PHP warning on failure in addition to the return value
     * this method already checks; suppress it so it doesn't surface as a spurious test warning.
     *
     * @throws Exception
     */
    public function testWriteThrowsWhenTheFeedFileCannotBeWritten(): void
    {
        $generator = $this->createGenerator([], feedFile: '/nonexistent-directory/feed.xml');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to write RSS feed.');

        @$generator->write();
    }

    /**
     * @param list<Post> $posts
     * @throws Exception
     */
    private function createGenerator(
        array $posts,
        ?string $feedFile = null,
        string $image = 'https://example.test/default.png',
    ): FeedGenerator {
        $postRepository = $this->createStub(PostRepository::class);
        $postRepository->method('getPublishedPosts')->willReturn($posts);

        return new FeedGenerator(
            $postRepository,
            $feedFile ?? $this->feedFile,
            'https://example.test',
            'Light Blog',
            'A blog built with Dotkernel Light',
            $image,
        );
    }

    /**
     * @throws Exception
     */
    private function createPost(
        string $title,
        string $slug,
        string $categorySlug,
        ?string $tlDr = null,
        string $excerpt = 'An excerpt',
        ?string $openGraphImage = null,
    ): Post {
        $category = $this->createStub(Category::class);
        $category->method('getSlug')->willReturn($categorySlug);

        $post = $this->createStub(Post::class);
        $post->method('getTitle')->willReturn($title);
        $post->method('getSlug')->willReturn($slug);
        $post->method('getCategory')->willReturn($category);
        $post->method('getTlDr')->willReturn($tlDr);
        $post->method('getExcerpt')->willReturn($excerpt);
        $post->method('getPostDate')->willReturn(new DateTimeImmutable('2026-08-01 10:00:00'));
        $post->method('getUpdatedFormatted')->willReturn(null);
        $post->method('getOpenGraphImage')->willReturn($openGraphImage);

        return $post;
    }

    private function loadFeed(): SimpleXMLElement
    {
        $this->assertFileExists($this->feedFile);

        $xml = simplexml_load_file($this->feedFile);
        $this->assertInstanceOf(SimpleXMLElement::class, $xml);

        return $xml;
    }
}
