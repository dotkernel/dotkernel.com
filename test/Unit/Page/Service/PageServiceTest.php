<?php

declare(strict_types=1);

namespace LightTest\Unit\Page\Service;

use Light\Page\Service\PageService;
use Light\Page\Service\PageServiceInterface;
use LightTest\Unit\UnitTest;

class PageServiceTest extends UnitTest
{
    public function testWillInstantiate(): void
    {
        $this->assertContainsOnlyInstancesOf(PageServiceInterface::class, [new PageService()]);
    }
}
