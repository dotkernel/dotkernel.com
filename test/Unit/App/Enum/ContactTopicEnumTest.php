<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Enum;

use Light\App\Enum\ContactTopicEnum;
use LightTest\Unit\UnitTest;

use function array_map;

class ContactTopicEnumTest extends UnitTest
{
    public function testCasesKeepTheValuesPostedByTheForm(): void
    {
        $this->assertSame(
            ['migration', 'project', 'oss', 'other'],
            array_map(fn (ContactTopicEnum $topic): string => $topic->value, ContactTopicEnum::cases())
        );
    }

    public function testLabel(): void
    {
        $this->assertSame('Migration', ContactTopicEnum::Migration->label());
        $this->assertSame('Project work', ContactTopicEnum::Project->label());
        $this->assertSame('Open source', ContactTopicEnum::Oss->label());
        $this->assertSame('Something else', ContactTopicEnum::Other->label());
    }
}
