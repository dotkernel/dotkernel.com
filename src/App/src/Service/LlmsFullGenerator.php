<?php

declare(strict_types=1);

namespace Light\App\Service;

use RuntimeException;

use function array_map;
use function array_merge;
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
 */
readonly class LlmsFullGenerator
{
    private const SEPARATOR = '<!-- ============================================================ -->';

    public function __construct(
        private string $sourceDir,
        private string $outputFile,
        private string $baseUrl,
    ) {
    }

    public function getOutputFile(): string
    {
        return $this->outputFile;
    }

    public function write(): int
    {
        $relativePaths = $this->collectRelativePaths();

        $sections = array_map(
            fn (string $relativePath): string => $this->buildSection($relativePath),
            $relativePaths
        );

        $written = implode("\n\n\n", $sections) . "\n\n";

        if (file_put_contents($this->outputFile, $written) === false) {
            throw new RuntimeException('Unable to write llms-full.txt.');
        }

        return count($relativePaths);
    }

    /**
     * @return list<string>
     */
    private function collectRelativePaths(): array
    {
        $categoryFiles = glob($this->sourceDir . '/*/*.md') ?: [];
        $categoryFiles = array_map(
            fn (string $path): string => ltrim(str_replace($this->sourceDir, '', $path), '/'),
            $categoryFiles
        );
        sort($categoryFiles);

        $paths = is_file($this->sourceDir . '/index.md') ? ['index.md'] : [];

        return array_merge($paths, $categoryFiles);
    }

    private function buildSection(string $relativePath): string
    {
        $contents = file_get_contents($this->sourceDir . '/' . $relativePath);
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
