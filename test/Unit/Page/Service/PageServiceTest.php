<?php

declare(strict_types=1);

namespace LightTest\Unit\Page\Service;

use Light\Page\Service\PageService;
use Light\Page\Service\PageServiceInterface;
use LightTest\Unit\UnitTest;

use function file_put_contents;
use function mkdir;
use function rmdir;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

class PageServiceTest extends UnitTest
{
    public function testWillInstantiate(): void
    {
        $this->assertContainsOnlyInstancesOf(PageServiceInterface::class, [new PageService('')]);
    }

    public function testResolveMarkdownFilePathReturnsTheFileForTheRouteSlug(): void
    {
        $mdPagesPath = sys_get_temp_dir() . '/' . uniqid('dk-pages-', true);
        mkdir($mdPagesPath);
        file_put_contents($mdPagesPath . '/api.md', '# Dotkernel API');

        $filePath = (new PageService($mdPagesPath))->resolveMarkdownFilePath('page::api');

        $this->assertSame($mdPagesPath . '/api.md', $filePath);

        unlink($mdPagesPath . '/api.md');
        rmdir($mdPagesPath);
    }

    public function testResolveMarkdownFilePathReturnsNullWhenNoFileExists(): void
    {
        $filePath = (new PageService(sys_get_temp_dir()))->resolveMarkdownFilePath('page::contact');

        $this->assertNull($filePath);
    }

    public function testResolveMarkdownFilePathReturnsNullForARouteNameWithNoSlug(): void
    {
        $filePath = (new PageService(sys_get_temp_dir()))->resolveMarkdownFilePath('routewithnoslug');

        $this->assertNull($filePath);
    }
}
