<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;
use Light\App\Handler\GetPackagesViewHandler;
use Light\App\Service\PackageGenerator;
use Light\Blog\Entity\Category;
use Light\Blog\Entity\Post;
use Light\Blog\Repository\CategoryRepository;
use Light\Blog\Repository\PostRepository;
use LightTest\Unit\UnitTest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;

use function array_keys;

class GetPackagesViewHandlerTest extends UnitTest
{
    /**
     * The listing is never regenerated inline; the handler only reads what the cron wrote.
     *
     * @throws Exception
     */
    public function testHandlePassesTheGeneratedListingToTheTemplate(): void
    {
        $packages = [
            [
                'name'        => 'dot-cache',
                'description' => 'Dotkernel cache component',
                'lifecycle'   => 'active',
                'php'         => '~8.4.0',
                'archived'    => false,
            ],
        ];

        $parameters = $this->render([
            'generated_at' => '2026-08-04T12:00:00+00:00',
            'org'          => 'dotkernel',
            'packages'     => $packages,
        ]);

        $this->assertSame($packages, $parameters['packages']);
        $this->assertSame('2026-08-04T12:00:00+00:00', $parameters['generatedAt']);
    }

    /**
     * @throws Exception
     */
    public function testHandleAlsoSuppliesThePostsAndCategoriesTheLayoutNeeds(): void
    {
        $parameters = $this->render(null);

        $this->assertSame(['posts', 'categories', 'packages', 'generatedAt'], array_keys($parameters));
        $this->assertCount(1, $parameters['posts']);
        $this->assertCount(1, $parameters['categories']);
    }

    /**
     * A missing, empty, or malformed data file must render an empty listing rather than fail.
     *
     * @param array<string, mixed>|null $data
     * @throws Exception
     */
    #[DataProvider('unusablePackagesProvider')]
    public function testHandleFallsBackToAnEmptyPackageList(?array $data): void
    {
        $this->assertSame([], $this->render($data)['packages']);
    }

    /**
     * @return array<string, array{array<string, mixed>|null}>
     */
    public static function unusablePackagesProvider(): array
    {
        return [
            'nothing read'          => [null],
            'empty payload'         => [[]],
            'no packages key'       => [['generated_at' => '2026-08-04T12:00:00+00:00']],
            'packages not an array' => [['packages' => 'nope']],
        ];
    }

    /**
     * `generated_at` drives the "last updated" line, which is simply omitted when it is unusable.
     *
     * @param array<string, mixed>|null $data
     * @throws Exception
     */
    #[DataProvider('unusableGeneratedAtProvider')]
    public function testHandleOmitsAnUnusableGeneratedAt(?array $data): void
    {
        $this->assertNull($this->render($data)['generatedAt']);
    }

    /**
     * @return array<string, array{array<string, mixed>|null}>
     */
    public static function unusableGeneratedAtProvider(): array
    {
        return [
            'nothing read'  => [null],
            'empty payload' => [[]],
            'key missing'   => [['packages' => []]],
            'not a string'  => [['packages' => [], 'generated_at' => 1234567890]],
            'null'          => [['packages' => [], 'generated_at' => null]],
        ];
    }

    /**
     * @throws Exception
     */
    public function testHandleReturnsAnHtmlResponse(): void
    {
        $template = $this->createStub(TemplateRendererInterface::class);
        $template->method('render')->willReturn('<html lang="en"></html>');

        $response = $this->createHandler($template, null)->handle(new ServerRequest());

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('<html lang="en"></html>', (string) $response->getBody());
    }

    /**
     * Renders the page and returns the parameters the handler passed to the template.
     *
     * @param array<string, mixed>|null $data
     * @return array<string, mixed>
     * @throws Exception
     */
    private function render(?array $data): array
    {
        $captured = [];
        $template = $this->createMock(TemplateRendererInterface::class);

        $template
            ->expects($this->once())
            ->method('render')
            ->willReturnCallback(
                function (string $name, mixed $parameters = []) use (&$captured): string {
                    $this->assertSame(GetPackagesViewHandler::TEMPLATE, $name);
                    $this->assertIsArray($parameters);
                    $captured = $parameters;

                    return '<html lang="en"></html>';
                }
            );

        $this->createHandler($template, $data)->handle(new ServerRequest());

        return $captured;
    }

    /**
     * @param array<string, mixed>|null $data
     * @throws Exception
     */
    private function createHandler(TemplateRendererInterface $template, ?array $data): GetPackagesViewHandler
    {
        $packageGenerator = $this->createStub(PackageGenerator::class);
        $packageGenerator->method('read')->willReturn($data);

        $postRepository = $this->createStub(PostRepository::class);
        $postRepository->method('getRecentPosts')->willReturn([$this->createStub(Post::class)]);

        $categoryRepository = $this->createStub(CategoryRepository::class);
        $categoryRepository->method('getCategories')->willReturn([$this->createStub(Category::class)]);

        return new GetPackagesViewHandler($template, $categoryRepository, $postRepository, $packageGenerator);
    }
}
