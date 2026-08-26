<?php

declare(strict_types=1);

namespace Light\Blog\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Light\App\Helper\Paginator;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Service\BlogServiceInterface;
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
        protected BlogServiceInterface $blogService,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $categorySlug = $request->getAttribute('slug');
        $categories   = $this->categoryRepository->getCategories();
        $category     = $this->categoryRepository->getCategoryResource($categorySlug);
        if ($category === null) {
            return $this->blogService->notFound($categories);
        }
        if (! $category->isVisible()) {
            return $this->blogService->gone($categories);
        }
        $meta = $category;

        $queryParams = $request->getQueryParams();
        $params      = Paginator::getParams($queryParams, 'articles.id');
        $data        = Paginator::wrapper(
            $this->categoryRepository->getCategoryPost($category, $params),
            $params
        );

        try {
            $html = $this->template->render(
                'page::category-resource',
                [
                    'categories' => $categories,
                    'category'   => $category,
                    'meta'       => $meta,
                    'data'       => $data,
                ]
            );
            return new HtmlResponse($html);
        } catch (Throwable $e) {
            return $this->blogService->notFound($categories);
        }
    }
}
