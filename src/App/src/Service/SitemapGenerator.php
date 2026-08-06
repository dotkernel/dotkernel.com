<?php

declare(strict_types=1);

namespace Light\App\Service;

use DateTimeInterface;
use DOMDocument;
use DOMElement;
use Light\Blog\Repository\PostRepository;
use RuntimeException;

use function count;

class SitemapGenerator
{
    public const CONTENT_TYPE = 'application/rss+xml; charset=UTF-8';

    private const SITEMAP_NAMESPACE = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    public function __construct(
        private readonly PostRepository $postRepository,
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
        $posts = $this->postRepository->getPublishedPosts();

        $dom               = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $urlset = $dom->createElementNS(self::SITEMAP_NAMESPACE, 'urlset');
        $dom->appendChild($urlset);

        $this->appendUrl($dom, $urlset, $this->baseUrl);

        foreach ($posts as $post) {
            $link = $this->baseUrl . '/' . $post->getCategory()->getSlug() . '/' . $post->getSlug() . '/';
            $this->appendUrl($dom, $urlset, $link, $post->getPostDate()->format(DateTimeInterface::W3C));
        }

        if ($dom->save($this->sitemapFile) === false) {
            throw new RuntimeException('Unable to write sitemap.');
        }

        return count($posts) + 1;
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
