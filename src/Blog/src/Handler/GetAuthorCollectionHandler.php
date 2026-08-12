<?php

declare(strict_types=1);

namespace Light\Blog\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Light\Blog\Repository\AuthorRepository;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetAuthorCollectionHandler implements RequestHandlerInterface
{
    public function __construct(
        protected TemplateRendererInterface $template,
        protected AuthorRepository $authorRepository,
        protected CategoryRepository $categoryRepository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $authors    = $this->authorRepository->getAuthorsWithPublishedPosts();
        $categories = $this->categoryRepository->getCategories();

        return new HtmlResponse(
            $this->template->render('page::authors', [
                'authors'    => $authors,
                'categories' => $categories,
            ])
        );
    }
}
