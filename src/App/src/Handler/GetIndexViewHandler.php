<?php

declare(strict_types=1);

namespace Light\App\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\TextResponse;
use Light\Blog\Repository\PostRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_map;
use function basename;
use function file_get_contents;
use function glob;
use function is_file;
use function pathinfo;
use function sort;
use function str_contains;
use function str_replace;
use function ucwords;

use const GLOB_BRACE;
use const PATHINFO_FILENAME;

class GetIndexViewHandler implements RequestHandlerInterface
{
    public function __construct(
        protected TemplateRendererInterface $template,
        protected PostRepository $postRepository,
        protected string $mdPagesPath,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (str_contains($request->getHeaderLine('Accept'), 'text/markdown')) {
            $markdownFile = $this->mdPagesPath . '/index.md';
            if (is_file($markdownFile)) {
                return new TextResponse(
                    (string) file_get_contents($markdownFile),
                    StatusCodeInterface::STATUS_OK,
                    ['Content-Type' => 'text/markdown; charset=utf-8'],
                );
            }
        }

        $posts = $this->postRepository->getRecentPosts(3);
        return new HtmlResponse(
            $this->template->render('app::index', [
                'posts'         => $posts,
                'adopterLogos'  => $this->getAdopterLogos(),
            ])
        );
    }

    /**
     * @return list<array{file: non-empty-string, alt: string}>
     */
    private function getAdopterLogos(): array
    {
        $files = glob($this->mdPagesPath . '/images/app/content/adopters/*.{png,jpg,jpeg,svg,webp}', GLOB_BRACE);
        if ($files === false) {
            return [];
        }

        $names = array_map(basename(...), $files);
        sort($names);

        return array_map(
            static fn (string $name): array => [
                'file' => $name,
                'alt'  => ucwords(str_replace(['-', '_'], ' ', pathinfo($name, PATHINFO_FILENAME))),
            ],
            $names
        );
    }
}
