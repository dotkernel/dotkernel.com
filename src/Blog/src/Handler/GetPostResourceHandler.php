<?php

declare(strict_types=1);

namespace Light\Blog\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Light\Blog\Entity\Category;
use Light\Blog\Enum\PostStatusEnum;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class GetPostResourceHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template,
        private readonly PostRepository $articleRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $slug         = $request->getAttribute('slug');
        $categorySlug = $request->getAttribute('categorySlug');
        $categories   = $this->categoryRepository->getCategories();
        $article      = $this->articleRepository->getArticleResource($slug, $categorySlug);
        if ($article === null) {
            return $this->notFound($categories);
        }
        if ($article->getStatus() === PostStatusEnum::Archived) {
            return $this->gone($categories);
        }
        if ($article->getStatus() !== PostStatusEnum::Published) {
            return $this->notFound($categories);
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
            return $this->notFound($categories);
        }
    }

    /**
     * @param Category[] $categories
     */
    private function notFound(array $categories): HtmlResponse
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
    private function gone(array $categories): HtmlResponse
    {
        return new HtmlResponse(
            $this->template->render('error::410', [
                'categories' => $categories,
            ]),
            StatusCodeInterface::STATUS_GONE
        );
    }
}
