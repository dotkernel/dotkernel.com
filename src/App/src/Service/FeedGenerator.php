<?php

declare(strict_types=1);

namespace Light\App\Service;

use DateTimeImmutable;
use DateTimeInterface;
use DOMDocument;
use DOMElement;
use Light\Blog\Repository\PostRepository;
use RuntimeException;

use function count;

class FeedGenerator
{
    public const CONTENT_TYPE = 'application/rss+xml; charset=UTF-8';

    private const MEDIA_NAMESPACE = 'http://search.yahoo.com/mrss/';

    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly string $feedFile,
        private readonly string $baseUrl,
        private readonly string $title,
        private readonly string $description,
        private readonly string $image,
    ) {
    }

    public function getFeedFile(): string
    {
        return $this->feedFile;
    }

    public function write(): int
    {
        $posts = $this->postRepository->getPublishedPosts();

        $dom               = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $rss = $dom->createElement('rss');
        $rss->setAttribute('version', '2.0');
        $rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:media', self::MEDIA_NAMESPACE);
        $dom->appendChild($rss);

        $channel = $dom->createElement('channel');
        $rss->appendChild($channel);

        $this->appendText($dom, $channel, 'title', $this->title);
        $this->appendText($dom, $channel, 'link', $this->baseUrl);
        $this->appendText($dom, $channel, 'description', $this->description);
        $this->appendText($dom, $channel, 'lastBuildDate', (new DateTimeImmutable())->format(DateTimeInterface::RSS));

        foreach ($posts as $post) {
            $link = $this->baseUrl . $post->getCategory()->getSlug() . '/' . $post->getSlug() . '/';

            $item = $dom->createElement('item');
            $channel->appendChild($item);

            $this->appendText($dom, $item, 'title', $post->getTitle());
            $this->appendText($dom, $item, 'link', $link);
            $this->appendText($dom, $item, 'description', $post->getTldr() ?? $post->getExcerpt());
            $this->appendText($dom, $item, 'pubDate', $post->getPostDate()->format(DateTimeInterface::RSS));
            $this->appendText(
                $dom,
                $item,
                'updatedAt',
                $post->getUpdatedFormatted(DateTimeInterface::RSS) ??
                $post->getPostDate()->format(DateTimeInterface::RSS)
            );
            $this->appendText($dom, $item, 'guid', $link);

            if ($this->image !== '') {
                $media = $dom->createElementNS(self::MEDIA_NAMESPACE, 'media:content');
                $media->setAttribute('url', $this->image);
                $media->setAttribute('medium', 'image');
                $item->appendChild($media);
            }
        }

        if ($dom->save($this->feedFile) === false) {
            throw new RuntimeException('Unable to write RSS feed.');
        }

        return count($posts);
    }

    private function appendText(DOMDocument $dom, DOMElement $parent, string $name, string $text): void
    {
        $el = $dom->createElement($name);
        $el->appendChild($dom->createTextNode($text));
        $parent->appendChild($el);
    }
}
