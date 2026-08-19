<?php

declare(strict_types=1);

namespace Light\App\Service;

use Light\Blog\Entity\Post;
use Light\Blog\Repository\PostRepository;
use RuntimeException;

use function array_key_exists;
use function count;
use function file_get_contents;
use function file_put_contents;
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
        'how-to',
        'best-practice',
        'dotkernel-api',
        'dotkernel3',
        'headless-platform',
        'zend-framework',
        'javascript',
        'middleware',
        'architecture',
        'php-troubleshooting',
        'phpstorm',
        'licensing',
        'design-pattern',
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

        uasort($postsByCategory, static fn (array $a, array $b): int => count($b) - count($a));
        foreach ($postsByCategory as $slug => $posts) {
            $sections[] = $this->buildCategorySection($slug, $posts);
        }

        $written = $this->buildHeader() . implode("\n", $sections);

        if (file_put_contents($this->outputFile, $written) === false) {
            throw new RuntimeException('Unable to write llms.txt.');
        }

        return count($sections);
    }

    /**
     * @return array<non-empty-string, list<array{post: Post, title: string, description: string}>>
     */
    private function groupPublishedPostsByCategory(): array
    {
        $byCategory = [];
        foreach ($this->postRepository->getPublishedPosts() as $post) {
            $slug         = $post->getCategory()->getSlug();
            $frontMatter  = $this->resolveFrontMatter($slug, $post);
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
            $lines[] = sprintf(
                '- [%s](%s/%s/%s/): %s',
                $entry['title'],
                $this->baseUrl,
                $slug,
                $entry['post']->getSlug(),
                $entry['description'],
            );
        }

        return implode("\n", $lines) . "\n";
    }

    /**
     * @return array{title: string, description: string}
     */
    private function resolveFrontMatter(string $categorySlug, Post $post): array
    {
        $result   = ['title' => $post->getTitle(), 'description' => $post->getExcerpt()];
        $path     = sprintf('%s/%s/%s.md', $this->sourceDir, $categorySlug, $post->getSlug());
        $contents = @file_get_contents($path);

        if ($contents === false) {
            return $result;
        }

        if (preg_match('/^title:\s*"(.*)"\s*$/m', $contents, $matches) === 1) {
            $result['title'] = trim($matches[1]);
        }

        if (preg_match('/^description:\s*"(.*)"\s*$/m', $contents, $matches) === 1) {
            $result['description'] = trim($matches[1]);
        }

        return $result;
    }

    private function buildHeader(): string
    {
        return <<<HEADER
        # Dotkernel Light

        > Dotkernel Light is the technical blog for the Dotkernel headless PHP platform - a PSR-15 compliant application built on Mezzio and Laminas components. It publishes architecture write-ups, how-tos, and release notes for the platform applications - Dotkernel API, Admin and Queue - and for the standalone Dotkernel Light skeleton.

        Content spans foundational PHP/middleware architecture (PSR-7, PSR-15, request lifecycle, dependency injection), practical how-tos (Doctrine migrations, CORS, authentication, caching), and the history/release notes of the Dotkernel ecosystem going back to 2008. Posts are organized by category and attributed to an author; URLs follow the pattern `/{category-slug}/{post-slug}/`.

        ## Docs

        - [Blog]({$this->baseUrl}/blog/): full list of posts, most recent first, paginated
        - [Categories]({$this->baseUrl}/categories/): all categories with post counts
        - [About]({$this->baseUrl}/about/): the team behind Dotkernel - how the team works, its commitment to open source and the PHP community, and how it uses AI under guardrails
        - [OSS Package Lifecycle]({$this->baseUrl}/dotkernel-packages-oss-lifecycle/): support/maintenance status of Dotkernel's open-source packages
        - [Contact]({$this->baseUrl}/contact/)

        ## Categories


        HEADER;
    }
}
