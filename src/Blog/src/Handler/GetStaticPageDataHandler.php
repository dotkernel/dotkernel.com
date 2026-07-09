<?php

declare(strict_types=1);

namespace Light\Blog\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class GetStaticPageDataHandler implements RequestHandlerInterface
{
    public function __construct(
        protected TemplateRendererInterface $template,
        protected PostRepository $articleRepository,
        protected CategoryRepository $categoryRepository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $staticPage = $request->getAttribute('static-page');

        $posts      = $this->articleRepository->getRecentPosts(3);
        $categories = $this->categoryRepository->getCategories();

        try {
            $html = $this->template->render('page::' . $staticPage, [
                'categories' => $categories,
                'posts'      => $posts,
            ]);
        } catch (Throwable $e) {
            return $this->notFound();
        }

        return new HtmlResponse($html);
    }

    private function notFound(): HtmlResponse
    {
        $categories = $this->categoryRepository->getCategories();

        return new HtmlResponse(
            $this->template->render('error::404', [
                'categories' => $categories,
            ]),
            404
        );
    }
}
