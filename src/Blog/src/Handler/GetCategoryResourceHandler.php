<?php

declare(strict_types=1);

namespace Light\Blog\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Light\Blog\Entity\Category;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class GetCategoryResourceHandler implements RequestHandlerInterface
{
    public function __construct(
        protected TemplateRendererInterface $template,
        protected CategoryRepository $categoryRepository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $categorySlug = $request->getAttribute('slug');
        $categories   = $this->categoryRepository->getCategories();
        $category     = $this->categoryRepository->getCategoryResource($categorySlug);
        if ($category === null) {
            return $this->notFound($categories);
        }
        $meta             = $category;
        $categoryArticles = $this->categoryRepository->getCategoryPost($category);

        try {
            $html = $this->template->render(
                'page::category-resource',
                [
                    'categories'       => $categories,
                    'category'         => $category,
                    'meta'             => $meta,
                    'categoryArticles' => $categoryArticles,
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
}
