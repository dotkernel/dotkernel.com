<?php

declare(strict_types=1);

namespace Light\Page\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\TextResponse;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Light\Page\Service\PageServiceInterface;
use Mezzio\Router\RouteResult;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function file_get_contents;
use function str_contains;

class GetPageViewHandler implements RequestHandlerInterface
{
    public function __construct(
        protected TemplateRendererInterface $template,
        protected CategoryRepository $categoryRepository,
        protected PostRepository $postRepository,
        protected PageServiceInterface $pageService,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $template = $request->getAttribute(RouteResult::class)->getMatchedRouteName();

        if (str_contains($request->getHeaderLine('Accept'), 'text/markdown')) {
            $markdownFile = $this->pageService->resolveMarkdownFilePath($template);
            if ($markdownFile !== null) {
                return new TextResponse(
                    (string) file_get_contents($markdownFile),
                    StatusCodeInterface::STATUS_OK,
                    ['Content-Type' => 'text/markdown; charset=utf-8'],
                );
            }
        }

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
