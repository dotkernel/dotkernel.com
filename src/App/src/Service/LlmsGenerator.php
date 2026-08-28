<?php

declare(strict_types=1);

namespace Light\App\Service;

use Light\Blog\Entity\Post;
use Light\Blog\Repository\PostRepository;
use RuntimeException;

use function array_key_exists;
use function array_merge;
use function basename;
use function count;
use function explode;
use function file_get_contents;
use function file_put_contents;
use function glob;
use function implode;
use function in_array;
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
        'headless-platform',
        'dotkernel-api',
        'architecture',
        'how-to',
        'middleware',
        'best-practice',
        'design-pattern',
        'dotkernel3',
        'dotkernel',
        'php-development',
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
        'zend-framework',
    ];

    /**
     * Individual posts demoted to `## Optional`, keyed by category slug - for a single article
     * that doesn't warrant moving its whole category into `OPTIONAL_CATEGORIES`.
     *
     * @var array<non-empty-string, list<non-empty-string>>
     */
    private const array OPTIONAL_POSTS = [
        'dotkernel'       => [
            'commitment-to-php-new-zend-certified-engineers-zce-in-our-team',
            'adding-composer-support-in-your-dotkernel-project',
            'using-dotkernel-with-composer-dependencies',
            'forcing-utf8-connections-and-character-set-in-mysql',
            'adding-a-cors-implementation-to-zend-expressive',
        ],
        'best-practice'   => [
            'insert-update-delete-statements-with-zend-db',
            'sql-queries-using-zend-db-select',
            'subqueries-with-zend-db',
            'using-like-wildcards-with-zend-db',
            'what-are-returning-the-fetch-functions-from-zend-db',
        ],
        'php-development' => [
            'almalinux-9-in-wsl2-install-php-apache-mariadb-composer-phpmyadmin',
            'mezzio-app-development-in-wsl2',
        ],
    ];

    /** @var array<non-empty-string, non-empty-string> */
    private const CATEGORY_BLURBS = [
        'dotkernel'           => 'the core framework - dot-* components (caching, logging, mail, dependency injection),'
            . ' Doctrine integration, and getting started with Dotkernel Light',
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

    /**
     * Static page slugs listed under `## Docs` instead of `## Pages` - reference/setup content
     * rather than product pages.
     *
     * @var list<non-empty-string>
     */
    private const array DOCS_PAGES = [
        'architecture',
        'wsl2',
        'dotboost',
    ];

    /**
     * Slug of the packages lifecycle page, hardcoded under `## Docs` in `buildHeader()` -
     * excluded from `## Pages` here so it is not listed twice.
     */
    private const string OSS_LIFECYCLE_SLUG = 'dotkernel-packages-oss-lifecycle';

    /**
     * The Contact page has no `public/contact.md` counterpart to source a title/description
     * from, so it is hardcoded directly into `## Pages` instead.
     */
    private const string CONTACT_TITLE       = 'Contact';
    private const string CONTACT_PATH        = 'contact/';
    private const string CONTACT_DESCRIPTION = 'Get in touch, report a security issue, or find where to ask a '
        . 'question and contribute to the project.';

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

        $sections      = [];
        $optionalLines = [];
        foreach (self::CATEGORY_ORDER as $slug) {
            if (! isset($postsByCategory[$slug])) {
                continue;
            }
            [$entries, $demoted] = $this->splitOptionalPosts($slug, $postsByCategory[$slug]);
            $optionalLines       = array_merge($optionalLines, $demoted);
            if ($entries !== []) {
                $sections[] = $this->buildCategorySection($slug, $entries);
            }
            unset($postsByCategory[$slug]);
        }

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
            [$entries, $demoted] = $this->splitOptionalPosts($slug, $posts);
            $optionalLines       = array_merge($optionalLines, $demoted);
            if ($entries !== []) {
                $sections[] = $this->buildCategorySection($slug, $entries);
            }
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
     * @return array{0: list<array{post: Post, title: string, description: string}>, 1: list<string>}
     */
    private function splitOptionalPosts(string $slug, array $entries): array
    {
        $optionalSlugs = self::OPTIONAL_POSTS[$slug] ?? [];
        if ($optionalSlugs === []) {
            return [$entries, []];
        }

        $kept     = [];
        $optional = [];
        foreach ($entries as $entry) {
            if (in_array($entry['post']->getSlug(), $optionalSlugs, true)) {
                $optional[] = $this->buildOptionalPostLink($slug, $entry);
                continue;
            }

            $kept[] = $entry;
        }

        return [$kept, $optional];
    }

    /**
     * @param list<array{post: Post, title: string, description: string}> $entries
     */
    private function buildCategorySection(string $slug, array $entries): string
    {
        $category = $entries[0]['post']->getCategory();
        $heading  = sprintf(
            '## %s',
            $category->getName()
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

        $pages = [
            [
                'title'       => self::CONTACT_TITLE,
                'path'        => self::CONTACT_PATH,
                'description' => self::CONTACT_DESCRIPTION,
            ],
        ];
        foreach (glob($this->pagesDir . '/*.md') ?: [] as $path) {
            $slug = basename($path, '.md');
            if ($slug === self::OSS_LIFECYCLE_SLUG || in_array($slug, self::DOCS_PAGES, true)) {
                continue;
            }

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
                'path'        => $slug . '.md',
                'description' => $description,
            ];
        }

        usort($pages, static fn (array $a, array $b): int => strcasecmp($a['title'], $b['title']));

        $lines = ['## Pages', ''];
        foreach ($pages as $page) {
            $lines[] = sprintf(
                '- [%s](%s/%s): %s',
                $page['title'],
                $this->baseUrl,
                $page['path'],
                $page['description'],
            );
        }

        return implode("\n", $lines) . "\n\n";
    }

    /**
     * Adds `self::DOCS_PAGES` under `## Docs`
     */
    private function buildDocsPageLinks(): string
    {
        if ($this->pagesDir === null) {
            return '';
        }

        $lines = [];
        foreach (self::DOCS_PAGES as $slug) {
            $path     = sprintf('%s/%s.md', $this->pagesDir, $slug);
            $contents = @file_get_contents($path);
            if ($contents === false) {
                continue;
            }

            $title       = $this->extractFrontMatterField($contents, 'title');
            $description = $this->extractFrontMatterField($contents, 'description');
            if ($title === null || $description === null) {
                continue;
            }

            $lines[] = sprintf(
                '- [%s](%s/%s.md): %s',
                trim(explode('|', $title, 2)[0]),
                $this->baseUrl,
                $slug,
                $description,
            );
        }

        return $lines === [] ? '' : implode("\n", $lines) . "\n";
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

        return "# {$this->title}\n\n"
            . $intro . "\n\n"
            . $body . "\n\n"
            . "## Docs\n\n"
            . "- [OSS Package Lifecycle]({$this->baseUrl}/" . self::OSS_LIFECYCLE_SLUG . ".md): "
            . "support/maintenance status of Dotkernel's open-source packages\n"
            . $this->buildDocsPageLinks() . "\n";
    }
}
