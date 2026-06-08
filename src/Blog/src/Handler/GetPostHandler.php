<?php

declare(strict_types=1);

namespace Light\Blog\Handler;

use Light\Blog\Repository\ArticleRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;

class GetPostHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template,
        private readonly ArticleRepository $articleRepository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $slug    = $request->getAttribute('slug');
        $article = $this->articleRepository->getPostBySlug($slug);
        return new HtmlResponse(
            $this->template->render('page::posts/' . $slug, [
                'article' => $article,
            ])
        );
    }
}