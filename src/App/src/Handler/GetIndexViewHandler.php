<?php

declare(strict_types=1);

namespace Light\App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Light\Blog\Repository\PostRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetIndexViewHandler implements RequestHandlerInterface
{
    public function __construct(
        protected TemplateRendererInterface $template,
        protected PostRepository $postRepository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $posts = $this->postRepository->getRecentPosts(3);
        return new HtmlResponse(
            $this->template->render('app::index', ['posts' => $posts])
        );
    }
}
