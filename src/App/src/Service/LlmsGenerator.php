<?php

declare(strict_types=1);

namespace Light\App\Service;

use Light\Blog\Entity\Post;
use Light\Blog\Repository\PostRepository;
use RuntimeException;

use function array_key_exists;
use function basename;
use function count;
use function explode;
use function file_get_contents;
use function file_put_contents;
use function glob;
use function implode;
use function preg_match;
use function sprintf;
use function strcasecmp;
use function trim;
use function uasort;
use function usort;

class LlmsGenerator
{
    /** @var list<non-empty-string> */
    private const CATEGORY_ORDER = [
        'dotkernel',
        'php-development',
        'dotkernel-api',
        'dotkernel3',
        'headless-platform',
        'middleware',
        'architecture',
        'design-pattern',
        'how-to',
        'best-practice',
    ];

    /**
     * Categories for `## Optional` section
     *
     * @var list<non-empty-string>
     */
    private const OPTIONAL_CATEGORIES = [
        'javascript',
        'php-troubleshooting',
        'licensing',
        'phpstorm',
    ];

    /** @var array<non-empty-string, non-empty-string> */
    private const CATEGORY_BLURBS = [
        'dotkernel'           => 'the core framework - releases, caching, sessions, auth, WURFL/device detection',
        'php-development'     => 'general PHP tooling, environments, IDEs, security',
        'how-to'              => 'practical guides - migrations, CORS, PSR-7, routing',
        'best-practice'       => 'coding standards and database access patterns',
        'dotkernel-api'       => 'REST API package - auth, content negotiation, OpenAPI',
        'dotkernel3'          => 'the v3 rewrite on Zend Expressive/Mezzio',
        'headless-platform'   => 'the current Dotkernel headless architecture',
        'zend-framework'      => 'Zend Framework 1 history and end-of-life notes',
        'javascript'          => 'frontend/JS topics',
        'middleware'          => 'PSR-15 middleware and routing internals',
        'architecture'        => 'request lifecycle and application bootstrapping',
        'php-troubleshooting' => 'fixes for common PHP setup issues',
        'phpstorm'            => 'IDE setup',
        'licensing'           => 'open-source license comparisons',
        'design-pattern'      => 'naming conventions for PSR-15 handlers',
    ];

    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly string $sourceDir,
        private readonly string $outputFile,
        private readonly string $baseUrl,
        private readonly ?string $pagesDir = null,
        private readonly string $title = 'Dotkernel',
    ) {
    }

    public function getOutputFile(): string
    {
        return $this->outputFile;
    }

    public function write(): int
    {
        $postsByCategory = $this->groupPublishedPostsByCategory();

        $sections = [];
        foreach (self::CATEGORY_ORDER as $slug) {
            if (! isset($postsByCategory[$slug])) {
                continue;
            }
            $sections[] = $this->buildCategorySection($slug, $postsByCategory[$slug]);
            unset($postsByCategory[$slug]);
        }

        $optionalLines         = [];
        $optionalCategoryCount = 0;
        foreach (self::OPTIONAL_CATEGORIES as $slug) {
            if (! isset($postsByCategory[$slug])) {
                continue;
            }
            $optionalCategoryCount++;
            foreach ($postsByCategory[$slug] as $entry) {
                $optionalLines[] = $this->buildOptionalPostLink($slug, $entry);
            }
            unset($postsByCategory[$slug]);
        }

        uasort($postsByCategory, static fn (array $a, array $b): int => count($b) - count($a));
        foreach ($postsByCategory as $slug => $posts) {
            $sections[] = $this->buildCategorySection($slug, $posts);
        }

        $written = $this->buildHeader()
            . $this->buildPagesSection()
            . implode("\n", $sections);

        if ($optionalLines !== []) {
            $written .= "\n## Optional\n\n" . implode("\n", $optionalLines) . "\n";
        }

        if (file_put_contents($this->outputFile, $written) === false) {
            throw new RuntimeException('Unable to write llms.txt.');
        }

        return count($sections) + $optionalCategoryCount;
    }

    /**
     * @return array<string, list<array{post: Post, title: string, description: string}>>
     */
    private function groupPublishedPostsByCategory(): array
    {
        $byCategory = [];
        foreach ($this->postRepository->getPublishedPosts() as $post) {
            $slug                = $post->getCategory()->getSlug();
            $frontMatter         = $this->resolveFrontMatter($slug, $post);
            $byCategory[$slug][] = [
                'post'        => $post,
                'title'       => $frontMatter['title'],
                'description' => $frontMatter['description'],
            ];
        }

        foreach ($byCategory as $slug => $entries) {
            usort($entries, static fn (array $a, array $b): int => strcasecmp($a['title'], $b['title']));
            $byCategory[$slug] = $entries;
        }

        return $byCategory;
    }

    /**
     * @param list<array{post: Post, title: string, description: string}> $entries
     */
    private function buildCategorySection(string $slug, array $entries): string
    {
        $category = $entries[0]['post']->getCategory();
        $heading  = sprintf(
            '## %s (%d post%s)',
            $category->getName(),
            count($entries),
            count($entries) === 1 ? '' : 's',
        );

        $lines = [$heading, ''];

        if (array_key_exists($slug, self::CATEGORY_BLURBS)) {
            $lines[] = sprintf('*%s*', self::CATEGORY_BLURBS[$slug]);
            $lines[] = '';
        }

        foreach ($entries as $entry) {
            $lines[] = $this->buildPostLink($slug, $entry);
        }

        return implode("\n", $lines) . "\n";
    }

    /**
     * @param array{post: Post, title: string, description: string} $entry
     */
    private function buildPostLink(string $slug, array $entry): string
    {
        return sprintf(
            '- [%s](%s/%s/%s.md): %s',
            $entry['title'],
            $this->baseUrl,
            $slug,
            $entry['post']->getSlug(),
            $entry['description'],
        );
    }

    /**
     * @param array{post: Post, title: string, description: string} $entry
     */
    private function buildOptionalPostLink(string $slug, array $entry): string
    {
        return sprintf(
            '- [%s](%s/%s/%s.md) *(%s)*: %s',
            $entry['title'],
            $this->baseUrl,
            $slug,
            $entry['post']->getSlug(),
            $entry['post']->getCategory()->getName(),
            $entry['description'],
        );
    }

    /**
     * @return array{title: string, description: string}
     */
    private function resolveFrontMatter(string $categorySlug, Post $post): array
    {
        $path     = sprintf('%s/%s/%s.md', $this->sourceDir, $categorySlug, $post->getSlug());
        $contents = @file_get_contents($path);

        return [
            'title'       => $contents === false
                ? $post->getTitle()
                : $this->extractFrontMatterField($contents, 'title') ?? $post->getTitle(),
            'description' => $contents === false
                ? $post->getExcerpt()
                : $this->extractFrontMatterField($contents, 'description') ?? $post->getExcerpt(),
        ];
    }

    private function buildPagesSection(): string
    {
        if ($this->pagesDir === null) {
            return '';
        }

        $pages = [];
        foreach (glob($this->pagesDir . '/*.md') ?: [] as $path) {
            $contents = file_get_contents($path);
            if ($contents === false) {
                continue;
            }

            $title       = $this->extractFrontMatterField($contents, 'title');
            $description = $this->extractFrontMatterField($contents, 'description');
            if ($title === null || $description === null) {
                continue;
            }

            $pages[] = [
                'title'       => trim(explode('|', $title, 2)[0]),
                'slug'        => basename($path, '.md'),
                'description' => $description,
            ];
        }

        if ($pages === []) {
            return '';
        }

        usort($pages, static fn (array $a, array $b): int => strcasecmp($a['title'], $b['title']));

        $lines = ['## Pages', ''];
        foreach ($pages as $page) {
            $lines[] = sprintf(
                '- [%s](%s/%s.md): %s',
                $page['title'],
                $this->baseUrl,
                $page['slug'],
                $page['description'],
            );
        }

        return implode("\n", $lines) . "\n\n";
    }

    private function extractFrontMatterField(string $contents, string $field): ?string
    {
        if (preg_match(sprintf('/^%s:\s*"(.*)"\s*$/m', $field), $contents, $matches) === 1) {
            return trim($matches[1]);
        }

        return null;
    }

    private function buildHeader(): string
    {
        $intro = '> dotkernel.com is the technical blog for the Dotkernel headless PHP platform - a PSR-15 '
            . 'compliant application built on Mezzio and Laminas components. It publishes architecture '
            . 'write-ups, how-tos, and release notes for the platform applications - Dotkernel API, Admin '
            . 'and Queue - and for the standalone Dotkernel Light skeleton.';

        $body = 'Content spans foundational PHP/middleware architecture (PSR-7, PSR-15, request lifecycle, '
            . 'dependency injection), practical how-tos (Doctrine migrations, CORS, authentication, caching), '
            . 'and the history/release notes of the Dotkernel ecosystem going back to 2008. Posts are organized '
            . 'by category and attributed to an author; URLs follow the pattern `/{category-slug}/{post-slug}/`. '
            . 'Each post also has a Markdown version at `/{category-slug}/{post-slug}.md`.';

        $about = 'the team behind Dotkernel - how the team works, its commitment to open source and the PHP '
            . 'community, and how it uses AI under guardrails';

        return "# {$this->title}\n\n"
            . $intro . "\n\n"
            . $body . "\n\n"
            . "## Docs\n\n"
            . "- [OSS Package Lifecycle]({$this->baseUrl}/dotkernel-packages-oss-lifecycle.md): "
            . "support/maintenance status of Dotkernel's open-source packages\n"
            . "- [Contact]({$this->baseUrl}/contact/)\n\n";
    }
}
