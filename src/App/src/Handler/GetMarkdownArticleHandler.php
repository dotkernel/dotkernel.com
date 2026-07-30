<?php

declare(strict_types=1);

namespace Light\App\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\TextResponse;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function file_get_contents;
use function is_file;
use function realpath;
use function rtrim;
use function str_starts_with;

class GetMarkdownArticleHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template,
        private readonly CategoryRepository $categoryRepository,
        private readonly string $articlesPath,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $categorySlug = (string) $request->getAttribute('categorySlug');
        $slug         = (string) $request->getAttribute('slug');

        $filePath = $this->resolveFilePath($categorySlug, $slug);
        if ($filePath === null) {
            return $this->notFound();
        }

        return new TextResponse(
            (string) file_get_contents($filePath),
            StatusCodeInterface::STATUS_OK,
            ['Content-Type' => 'text/markdown; charset=utf-8'],
        );
    }

    private function resolveFilePath(string $categorySlug, string $slug): ?string
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

    private function notFound(): HtmlResponse
    {
        return new HtmlResponse(
            $this->template->render('error::404', [
                'categories' => $this->categoryRepository->getCategories(),
            ]),
            StatusCodeInterface::STATUS_NOT_FOUND,
        );
    }
}
