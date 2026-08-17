<?php

declare(strict_types=1);

namespace LightTest\Unit\Blog\Enum;

use Light\Blog\Enum\PostStatusEnum;
use LightTest\Unit\UnitTest;

class PostStatusEnumTest extends UnitTest
{
    public function testValuesReturnsEveryCaseValueInDeclarationOrder(): void
    {
        $this->assertSame(['draft', 'published', 'private', 'archived'], PostStatusEnum::values());
    }

    public function testEachCaseIsBackedByItsLowercaseName(): void
    {
        $this->assertSame('draft', PostStatusEnum::Draft->value);
        $this->assertSame('published', PostStatusEnum::Published->value);
        $this->assertSame('private', PostStatusEnum::Private->value);
        $this->assertSame('archived', PostStatusEnum::Archived->value);
    }

    public function testFromResolvesAStoredValueBackToItsCase(): void
    {
        $this->assertSame(PostStatusEnum::Published, PostStatusEnum::from('published'));
    }

    public function testTryFromReturnsNullForAValueThatIsNotACase(): void
    {
        $this->assertNull(PostStatusEnum::tryFrom('publish'));
    }
}
