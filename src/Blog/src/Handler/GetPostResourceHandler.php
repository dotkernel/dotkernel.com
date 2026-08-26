<?php

declare(strict_types=1);

namespace Light\Blog\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\TextResponse;
use Light\Blog\Enum\PostStatusEnum;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Light\Blog\Service\BlogServiceInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function file_get_contents;
use function str_contains;

class GetPostResourceHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template,
        private readonly PostRepository $articleRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly BlogServiceInterface $blogService,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $slug         = (string) $request->getAttribute('slug');
        $categorySlug = (string) $request->getAttribute('categorySlug');

        if (str_contains($request->getHeaderLine('Accept'), 'text/markdown')) {
            $markdownFile = $this->blogService->resolveMarkdownFilePath($categorySlug, $slug);
            if ($markdownFile !== null) {
                return new TextResponse(
                    (string) file_get_contents($markdownFile),
                    StatusCodeInterface::STATUS_OK,
                    ['Content-Type' => 'text/markdown; charset=utf-8'],
                );
            }
        }

        $categories = $this->categoryRepository->getCategories();
        $article    = $this->articleRepository->getArticleResource($slug, $categorySlug);
        if ($article === null) {
            return $this->blogService->notFound($categories);
        }
        if ($article->getStatus() === PostStatusEnum::Archived) {
            return $this->blogService->gone($categories);
        }
        if ($article->getStatus() !== PostStatusEnum::Published) {
            return $this->blogService->notFound($categories);
        }
        $meta     = $article;
        $adjacent = $this->articleRepository->getAdjacentPosts($article);
        try {
            $html = $this->template->render(
                'page::blog-resource/' . $article->getCategory()->getSlug() . '/' . $slug,
                [
                    'article'      => $article,
                    'meta'         => $meta,
                    'categories'   => $categories,
                    'previousPost' => $adjacent['previous'],
                    'nextPost'     => $adjacent['next'],
                ]
            );
            return new HtmlResponse($html);
        } catch (Throwable $e) {
            return $this->blogService->notFound($categories);
        }
    }
}
