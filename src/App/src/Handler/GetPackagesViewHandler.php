<?php

declare(strict_types=1);

namespace Light\App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Light\App\Service\PackageGenerator;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_array;
use function is_string;

class GetPackagesViewHandler implements RequestHandlerInterface
{
    public const string TEMPLATE = 'page::dotkernel-packages-oss-lifecycle';

    public function __construct(
        private readonly TemplateRendererInterface $template,
        private readonly CategoryRepository $categoryRepository,
        private readonly PostRepository $postRepository,
        private readonly PackageGenerator $packageGenerator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // no inline regeneration
        // data is cached in `public/dotkernel-packages.json` by `bin/generate-packages`
        $data = $this->packageGenerator->read();
        if (! is_array($data)) {
            $data = [];
        }
        $packages    = isset($data['packages']) && is_array($data['packages'])
            ? $data['packages']
            : [];
        $generatedAt = isset($data['generated_at']) && is_string($data['generated_at'])
            ? $data['generated_at']
            : null;

        return new HtmlResponse(
            $this->template->render(self::TEMPLATE, [
                'posts'       => $this->postRepository->getRecentPosts(3),
                'categories'  => $this->categoryRepository->getCategories(),
                'packages'    => $packages,
                'generatedAt' => $generatedAt,
            ])
        );
    }
}
