<?php

declare(strict_types=1);

namespace Light\Blog\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Light\App\Helper\Paginator;
use Light\Blog\Repository\PostRepository;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function var_dump;

class GetPostCollectionHandler implements RequestHandlerInterface
{
    public function __construct(
        protected TemplateRendererInterface $template,
        protected PostRepository            $articleRepository,
        protected CategoryRepository        $categoryRepository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $params = Paginator::getParams($queryParams, 'articles.id');
        $data   = Paginator::wrapper(
            $this->articleRepository->getArticles($params),
            $params
        );

        $categories = $this->categoryRepository->getCategories();
        return new HtmlResponse(
            $this->template->render('page::blog', [
                'data'       => $data,
                'categories' => $categories,
            ])
        );
    }
}
