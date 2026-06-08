<?php

declare(strict_types=1);

namespace Light\Blog\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetCategoriesHandler implements RequestHandlerInterface
{
    public function __construct(
        protected TemplateRendererInterface $template,
        protected CategoryRepository $categoryRepository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $categories = $this->categoryRepository->getCategories();

        return new HtmlResponse(
            $this->template->render('page::categories', [
                'categories' => $categories,
            ])
        );
    }
}
