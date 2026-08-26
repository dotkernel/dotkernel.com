<?php

declare(strict_types=1);

namespace Light\Blog\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Light\App\Helper\Paginator;
use Light\Blog\Repository\AuthorRepository;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Light\Blog\Service\BlogServiceInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetAuthorResourceHandler implements RequestHandlerInterface
{
    public function __construct(
        protected TemplateRendererInterface $template,
        protected AuthorRepository $authorRepository,
        protected PostRepository $postRepository,
        protected CategoryRepository $categoryRepository,
        protected BlogServiceInterface $blogService,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $authorSlug = $request->getAttribute('slug');
        $author     = $this->authorRepository->getAuthorResource($authorSlug);
        if (! $author) {
            return $this->blogService->authorNotFound($this->authorRepository->getAuthorsWithPublishedPosts());
        }
        $categories = $this->categoryRepository->getCategories();

        $queryParams = $request->getQueryParams();
        $params      = Paginator::getParams($queryParams, 'posts.postDate');
        $data        = Paginator::wrapper(
            $this->postRepository->getArticleByAuthor($author, $params),
            $params
        );
        $authorPost  = $this->postRepository->getArticleByAuthor($author, $params);

        return new HtmlResponse(
            $this->template->render('page::author-resource', [
                'author'     => $author,
                'categories' => $categories,
                'data'       => $data,
            ])
        );
    }
}
