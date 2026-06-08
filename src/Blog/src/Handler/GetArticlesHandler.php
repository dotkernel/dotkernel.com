<?php

namespace Light\Blog\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Light\Blog\Repository\ArticleRepository;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetArticlesHandler implements RequestHandlerInterface
{
    public function __construct(
        protected TemplateRendererInterface $template,
        protected ArticleRepository $articleRepository,
        protected CategoryRepository $categoryRepository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $articles = $this->articleRepository->getArticles();
        $categories = $this->categoryRepository->getCategories();
        return new HtmlResponse(
            $this->template->render('page::posts', [
                'articles'  => $articles,
                'categories' => $categories,
            ])
        );
    }
}