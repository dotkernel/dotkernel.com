<?php

declare(strict_types=1);

namespace Light\Blog\Handler;

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
        if (! $category) {
            return new HtmlResponse('Category not found', 404);
        }
        $categories       = $this->categoryRepository->getCategories();
        $categoryArticles = $this->categoryRepository->getCategoryArticles($category);
        return new HtmlResponse(
            $this->template->render('page::category-resource', [
                'categories'       => $categories,
                'category'         => $category,
                'categoryArticles' => $categoryArticles,
            ])
        );
    }
}
