<?php

declare(strict_types=1);

namespace Light\Blog\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Light\App\Helper\Paginator;
use Light\Blog\Entity\Category;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\TagRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class GetTagResourceHandler implements RequestHandlerInterface
{
    public function __construct(
        protected TemplateRendererInterface $template,
        protected TagRepository $tagRepository,
        protected CategoryRepository $categoryRepository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tagSlug    = $request->getAttribute('slug');
        $categories = $this->categoryRepository->getCategories();
        $tag        = $this->tagRepository->getTagResource($tagSlug);
        if ($tag === null) {
            return $this->notFound($categories);
        }
        $meta = $tag;

        $queryParams = $request->getQueryParams();
        $params      = Paginator::getParams($queryParams, 'articles.id');
        $data        = Paginator::wrapper(
            $this->tagRepository->getTagPost($tag, $params),
            $params
        );

        try {
            $html = $this->template->render(
                'page::tag-resource',
                [
                    'categories' => $categories,
                    'tag'        => $tag,
                    'meta'       => $meta,
                    'data'       => $data,
                ]
            );
            return new HtmlResponse($html);
        } catch (Throwable $e) {
            return $this->notFound($categories);
        }
    }

    /**
     * @param Category[] $categories
     */
    private function notFound(array $categories): HtmlResponse
    {
        return new HtmlResponse(
            $this->template->render('error::404', [
                'categories' => $categories,
            ]),
            StatusCodeInterface::STATUS_NOT_FOUND
        );
    }
}
