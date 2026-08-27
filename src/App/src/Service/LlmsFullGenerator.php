<?php

declare(strict_types=1);

namespace Light\App\Service;

use Light\Blog\Entity\Post;
use Light\Blog\Repository\PostRepository;
use RuntimeException;

use function array_map;
use function array_merge;
use function basename;
use function count;
use function file_get_contents;
use function file_put_contents;
use function glob;
use function implode;
use function is_file;
use function preg_replace;
use function sort;
use function sprintf;
use function str_replace;
use function trim;

/**
 * Generate llms-full.txt from index, published articles, pages
 * FAQ is removed
 */
readonly class LlmsFullGenerator
{
    private const SEPARATOR  = '<!-- ============================================================ -->';
    private const PAGES_ROOT = 'md-pages';

    public function __construct(
        private PostRepository $postRepository,
        private string $sourceDir,
        private string $outputFile,
        private string $baseUrl,
        private ?string $pagesDir = null,
    ) {
    }

    public function getOutputFile(): string
    {
        return $this->outputFile;
    }

    public function write(): int
    {
        $entries = $this->collectEntries();

        $sections = array_map(
            fn (array $entry): string => $this->buildSection($entry['file'], $entry['label']),
            $entries
        );

        $written = implode("\n\n\n", $sections) . "\n\n";

        if (file_put_contents($this->outputFile, $written) === false) {
            throw new RuntimeException('Unable to write llms-full.txt.');
        }

        return count($entries);
    }

    /**
     * Absolute file path plus the label written into each section header.
     *
     * @return list<array{file: string, label: string}>
     */
    private function collectEntries(): array
    {
        $articleLabels = array_map(
            fn (Post $post): string => sprintf('%s/%s.md', $post->getCategory()->getSlug(), $post->getSlug()),
            $this->postRepository->getPublishedPosts()
        );
        sort($articleLabels);

        $labels = is_file($this->sourceDir . '/index.md') ? ['index.md'] : [];
        $labels = array_merge($labels, $articleLabels);

        $entries = array_map(
            fn (string $label): array => [
                'file'  => $this->resolveArticleFile($label),
                'label' => $label,
            ],
            $labels
        );

        return array_merge($entries, $this->collectPageEntries());
    }

    private function resolveArticleFile(string $label): string
    {
        $file = $this->sourceDir . '/' . $label;

        if (! is_file($file)) {
            throw new RuntimeException("Missing markdown file for published post: {$label}");
        }

        return $file;
    }

    /**
     * The flat `*.md` files in the pages directory, appended after the articles.
     *
     * @return list<array{file: string, label: string}>
     */
    private function collectPageEntries(): array
    {
        if ($this->pagesDir === null) {
            return [];
        }

        $pageFiles = glob($this->pagesDir . '/*.md') ?: [];
        sort($pageFiles);

        return array_map(
            fn (string $path): array => [
                'file'  => $path,
                'label' => self::PAGES_ROOT . '/' . basename($path),
            ],
            $pageFiles
        );
    }

    private function buildSection(string $file, string $relativePath): string
    {
        $contents = file_get_contents($file);
        if ($contents === false) {
            throw new RuntimeException("Unable to read file: {$relativePath}");
        }

        $contents = str_replace('{{base_url}}', $this->baseUrl, $contents);
        $contents = $this->stripFaqSection($contents);

        return sprintf(
            "%s\n<!-- %s -->\n%s\n\n%s",
            self::SEPARATOR,
            $relativePath,
            self::SEPARATOR,
            trim($contents)
        );
    }

    /**
     * Removes the FAQ section, leaves the rest of the content
     */
    private function stripFaqSection(string $contents): string
    {
        $contents = preg_replace('/^## FAQ\s*$.*?(?=^## |\z)/ms', '', $contents) ?? $contents;

        return preg_replace('/\n{3,}/', "\n\n", $contents) ?? $contents;
    }
}
