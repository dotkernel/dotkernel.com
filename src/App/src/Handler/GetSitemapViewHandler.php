<?php

declare(strict_types=1);

namespace Light\App\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\XmlResponse;
use Light\App\Service\SitemapGenerator;
use Light\Blog\Entity\Category;
use Light\Blog\Repository\CategoryRepository;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function file_get_contents;
use function filesize;
use function is_file;

class GetSitemapViewHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template,
        private readonly CategoryRepository $categoryRepository,
        private readonly SitemapGenerator $sitemapGenerator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $sitemapFile = $this->sitemapGenerator->getSitemapFile();

        if (! is_file($sitemapFile) || filesize($sitemapFile) === 0) {
            try {
                $this->sitemapGenerator->write();
            } catch (Throwable) {
            }
        }

        if (! is_file($sitemapFile) || filesize($sitemapFile) === 0) {
            return $this->notFound($this->categoryRepository->getCategories());
        }

        return new XmlResponse(
            (string) file_get_contents($sitemapFile),
            StatusCodeInterface::STATUS_OK,
            ['Content-Type' => SitemapGenerator::CONTENT_TYPE]
        );
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
