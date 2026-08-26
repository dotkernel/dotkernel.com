<?php

declare(strict_types=1);

namespace Light\Blog\Service;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Light\Blog\Entity\Author;
use Light\Blog\Entity\Category;
use Mezzio\Template\TemplateRendererInterface;

use function is_file;
use function realpath;
use function rtrim;
use function str_starts_with;

class BlogService implements BlogServiceInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template,
        private readonly string $articlesPath,
    ) {
    }

    /**
     * @param Category[] $categories
     */
    public function notFound(array $categories): HtmlResponse
    {
        return new HtmlResponse(
            $this->template->render('error::404', [
                'categories' => $categories,
            ]),
            StatusCodeInterface::STATUS_NOT_FOUND
        );
    }

    /**
     * @param Category[] $categories
     */
    public function gone(array $categories): HtmlResponse
    {
        return new HtmlResponse(
            $this->template->render('error::410', [
                'categories' => $categories,
            ]),
            StatusCodeInterface::STATUS_GONE
        );
    }

    /**
     * @param Author[] $authors
     */
    public function authorNotFound(array $authors): HtmlResponse
    {
        return new HtmlResponse(
            $this->template->render('error::404', [
                'authors' => $authors,
            ]),
            StatusCodeInterface::STATUS_NOT_FOUND
        );
    }

    public function resolveMarkdownFilePath(string $categorySlug, string $slug): ?string
    {
        if ($categorySlug === '' || $slug === '') {
            return null;
        }

        $base = realpath($this->articlesPath);
        if ($base === false) {
            return null;
        }
        $base     = rtrim($base, '/');
        $realPath = realpath($base . '/' . $categorySlug . '/' . $slug . '.md');
        if ($realPath === false || ! is_file($realPath)) {
            return null;
        }

        if (! str_starts_with($realPath, $base . '/')) {
            return null;
        }

        return $realPath;
    }
}
