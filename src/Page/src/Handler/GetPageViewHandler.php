<?php

declare(strict_types=1);

namespace Light\Page\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Mezzio\Router\RouteResult;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetPageViewHandler implements RequestHandlerInterface
{
    public function __construct(
        protected TemplateRendererInterface $template,
        protected CategoryRepository $categoryRepository,
        protected PostRepository $postRepository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $template   = $request->getAttribute(RouteResult::class)->getMatchedRouteName();
        $posts      = $this->postRepository->getRecentPosts(3);
        $categories = $this->categoryRepository->getCategories();
        return new HtmlResponse(
            $this->template->render($template, [
                'posts'      => $posts,
                'categories' => $categories,
            ])
        );
    }
}
