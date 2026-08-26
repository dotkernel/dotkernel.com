<?php

declare(strict_types=1);

namespace Light\Blog\Service;

use Laminas\Diactoros\Response\HtmlResponse;
use Light\Blog\Entity\Author;
use Light\Blog\Entity\Category;

interface BlogServiceInterface
{
    /**
     * @param Category[] $categories
     */
    public function notFound(array $categories): HtmlResponse;

    /**
     * @param Category[] $categories
     */
    public function gone(array $categories): HtmlResponse;

    /**
     * @param Author[] $authors
     */
    public function authorNotFound(array $authors): HtmlResponse;

    /**
     * Resolves the markdown file backing an article (`{categorySlug}/{slug}.md`),
     * or null when the category/slug is invalid or has no markdown counterpart.
     */
    public function resolveMarkdownFilePath(string $categorySlug, string $slug): ?string;
}
