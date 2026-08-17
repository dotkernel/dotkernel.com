<?php

declare(strict_types=1);

namespace Light\App\Service;

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
use function ltrim;
use function sort;
use function sprintf;
use function str_replace;
use function trim;

/**
 * Concatenates every article under the markdown source directory into a single
 * plain-text file for LLM consumption, in the same format as llms.txt/llms-full.txt
 * conventions (index.md first, then every other article sorted by relative path).
 *
 * When a pages directory is configured, the flat `*.md` files in it - the markdown
 * versions of the static pages - are appended after the articles, labelled with a
 * `md-pages/` prefix so their origin stays readable in the output.
 */
readonly class LlmsFullGenerator
{
    private const SEPARATOR  = '<!-- ============================================================ -->';
    private const PAGES_ROOT = 'md-pages';

    public function __construct(
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
        $categoryFiles = glob($this->sourceDir . '/*/*.md') ?: [];
        $categoryFiles = array_map(
            fn (string $path): string => ltrim(str_replace($this->sourceDir, '', $path), '/'),
            $categoryFiles
        );
        sort($categoryFiles);

        $labels = is_file($this->sourceDir . '/index.md') ? ['index.md'] : [];
        $labels = array_merge($labels, $categoryFiles);

        $entries = array_map(
            fn (string $label): array => [
                'file'  => $this->sourceDir . '/' . $label,
                'label' => $label,
            ],
            $labels
        );

        return array_merge($entries, $this->collectPageEntries());
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

        return sprintf(
            "%s\n<!-- %s -->\n%s\n\n%s",
            self::SEPARATOR,
            $relativePath,
            self::SEPARATOR,
            trim($contents)
        );
    }
}
