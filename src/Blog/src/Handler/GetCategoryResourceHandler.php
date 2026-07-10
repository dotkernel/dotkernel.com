<?php

declare(strict_types=1);

namespace Light\Blog\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
        $category     = $this->categoryRepository->getCategoryResource($categorySlug);
        $meta         = $category;

        if (! $category) {
            return new HtmlResponse('Category not found', StatusCodeInterface::STATUS_NOT_FOUND);
        }
        $categories       = $this->categoryRepository->getCategories();
        $categoryArticles = $this->categoryRepository->getCategoryPost($category);
        return new HtmlResponse(
            $this->template->render('page::category-resource', [
                'categories'       => $categories,
                'category'         => $category,
                'meta'             => $meta,
                'categoryArticles' => $categoryArticles,
            ])
        );
    }
}
