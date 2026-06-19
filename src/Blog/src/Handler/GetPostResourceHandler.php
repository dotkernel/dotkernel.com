<?php

declare(strict_types=1);

namespace Light\Blog\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Light\Blog\Repository\PostRepository;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetPostResourceHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template,
        private readonly PostRepository            $articleRepository,
        private readonly CategoryRepository        $categoryRepository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $slug         = $request->getAttribute('slug');
        $categorySlug = $request->getAttribute('categorySlug');
        $article    = $this->articleRepository->getArticleResource($slug, $categorySlug);
        $categories = $this->categoryRepository->getCategories();

        return new HtmlResponse(
            $this->template->render('page::blog-resource/' . $article->getCategory()->getSlug() . '/' . $slug, [
                'article'    => $article,
                'categories' => $categories,
            ])
        );
    }
}
