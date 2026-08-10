<?php

declare(strict_types=1);

namespace Light\App\Service;

use DateTimeInterface;
use DOMDocument;
use DOMElement;
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
     * @param array<int, string> $pageRoutes Route URIs registered under config['routes'][*],
     *                                        e.g. ['contact'] for the /contact/ static page.
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
        $categories = $this->categoryRepository->getCategories();
        $authors    = $this->authorRepository->getAuthorsWithPublishedPosts();

        $dom               = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $urlset = $dom->createElementNS(self::SITEMAP_NAMESPACE, 'urlset');
        $dom->appendChild($urlset);

        $count = 0;

        $this->appendUrl($dom, $urlset, $this->baseUrl . '/');
        $count++;

        $this->appendUrl($dom, $urlset, $this->baseUrl . '/blog/');
        $count++;

        $this->appendUrl($dom, $urlset, $this->baseUrl . '/categories/');
        $count++;

        $this->appendUrl($dom, $urlset, $this->baseUrl . '/dotkernel-packages-oss-lifecycle/');
        $count++;

        foreach ($this->pageRoutes as $routeUri) {
            $this->appendUrl($dom, $urlset, sprintf('%s/%s/', $this->baseUrl, $routeUri));
            $count++;
        }

        foreach ($categories as $category) {
            $lastmod = $category->getUpdated() ?? $category->getCreated();
            $this->appendUrl(
                $dom,
                $urlset,
                sprintf('%s/category/%s/', $this->baseUrl, $category->getSlug()),
                $lastmod?->format(DateTimeInterface::W3C)
            );
            $count++;
        }

        foreach ($authors as $author) {
            $this->appendUrl($dom, $urlset, sprintf('%s/author/%s/', $this->baseUrl, $author->getSlug()));
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
