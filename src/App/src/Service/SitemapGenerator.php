<?php

declare(strict_types=1);

namespace Light\App\Service;

use DateTimeInterface;
use DOMDocument;
use DOMElement;
use Light\Blog\Entity\Post;
use Light\Blog\Repository\AuthorRepository;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use RuntimeException;

use function sprintf;

class SitemapGenerator
{
    public const CONTENT_TYPE = 'application/rss+xml; charset=UTF-8';

    private const SITEMAP_NAMESPACE = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    /**
     * @param array<int, string> $pageRoutes
     */
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly AuthorRepository $authorRepository,
        private readonly array $pageRoutes,
        private readonly string $sitemapFile,
        private readonly string $baseUrl,
    ) {
    }

    public function getSitemapFile(): string
    {
        return $this->sitemapFile;
    }

    public function write(): int
    {
        $posts      = $this->postRepository->getPublishedPosts();
        $categories = $this->categoryRepository->getCategoriesWithPublishedPosts();
        $authors    = $this->authorRepository->getAuthorsWithPublishedPosts();

        [$latestOverall, $latestByCategory, $latestByAuthor] = $this->buildLastmodIndex($posts);
        $siteLastmod                                         = $latestOverall?->format(DateTimeInterface::W3C);

        $dom               = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $urlset = $dom->createElementNS(self::SITEMAP_NAMESPACE, 'urlset');
        $dom->appendChild($urlset);

        $count = 0;

        $this->appendUrl($dom, $urlset, $this->baseUrl . '/', $siteLastmod);
        $count++;

        $this->appendUrl($dom, $urlset, $this->baseUrl . '/blog/', $siteLastmod);
        $count++;

        $this->appendUrl($dom, $urlset, $this->baseUrl . '/categories/', $siteLastmod);
        $count++;

        $this->appendUrl($dom, $urlset, $this->baseUrl . '/authors/', $siteLastmod);
        $count++;

        $this->appendUrl($dom, $urlset, $this->baseUrl . '/dotkernel-packages-oss-lifecycle/');
        $count++;

        foreach ($this->pageRoutes as $routeUri) {
            $this->appendUrl($dom, $urlset, sprintf('%s/%s/', $this->baseUrl, $routeUri));
            $count++;
        }

        foreach ($categories as $category) {
            $lastmod = $latestByCategory[$category->getSlug()] ?? null;
            $this->appendUrl(
                $dom,
                $urlset,
                sprintf('%s/category/%s/', $this->baseUrl, $category->getSlug()),
                $lastmod?->format(DateTimeInterface::W3C)
            );
            $count++;
        }

        foreach ($authors as $author) {
            $lastmod = $latestByAuthor[$author->getSlug()] ?? null;
            $this->appendUrl(
                $dom,
                $urlset,
                sprintf('%s/author/%s/', $this->baseUrl, $author->getSlug()),
                $lastmod?->format(DateTimeInterface::W3C)
            );
            $count++;
        }

        foreach ($posts as $post) {
            $link = sprintf(
                '%s/%s/%s/',
                $this->baseUrl,
                $post->getCategory()->getSlug(),
                $post->getSlug()
            );
            $this->appendUrl($dom, $urlset, $link, $post->getPostDate()->format(DateTimeInterface::W3C));
            $count++;
        }

        if ($dom->save($this->sitemapFile) === false) {
            throw new RuntimeException('Unable to write sitemap.');
        }

        return $count;
    }

    /**
     * @param array<int, Post> $posts
     * @return array{0: ?DateTimeInterface, 1: array<string, DateTimeInterface>, 2: array<string, DateTimeInterface>}
     */
    private function buildLastmodIndex(array $posts): array
    {
        $latestOverall    = null;
        $latestByCategory = [];
        $latestByAuthor   = [];

        foreach ($posts as $post) {
            $postDate = $post->getPostDate();

            if ($latestOverall === null || $postDate > $latestOverall) {
                $latestOverall = $postDate;
            }

            $categorySlug = $post->getCategory()->getSlug();
            if (! isset($latestByCategory[$categorySlug]) || $postDate > $latestByCategory[$categorySlug]) {
                $latestByCategory[$categorySlug] = $postDate;
            }

            $authorSlug = $post->getAuthor()->getSlug();
            if (! isset($latestByAuthor[$authorSlug]) || $postDate > $latestByAuthor[$authorSlug]) {
                $latestByAuthor[$authorSlug] = $postDate;
            }
        }

        return [$latestOverall, $latestByCategory, $latestByAuthor];
    }

    private function appendUrl(DOMDocument $dom, DOMElement $urlset, string $loc, ?string $lastmod = null): void
    {
        $url = $dom->createElement('url');
        $urlset->appendChild($url);

        $this->appendText($dom, $url, 'loc', $loc);
        if ($lastmod !== null) {
            $this->appendText($dom, $url, 'lastmod', $lastmod);
        }
    }

    private function appendText(DOMDocument $dom, DOMElement $parent, string $name, string $text): void
    {
        $el = $dom->createElement($name);
        $el->appendChild($dom->createTextNode($text));
        $parent->appendChild($el);
    }
}
