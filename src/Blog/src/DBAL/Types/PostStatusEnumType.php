<?php

declare(strict_types=1);

namespace Light\Blog\DBAL\Types;

use Light\App\DBAL\Types\AbstractEnumType;
use Light\Blog\Enum\PostStatusEnum;

class PostStatusEnumType extends AbstractEnumType
{
    public const NAME = 'post_status_enum';
    public function getEnumClass(): string
    {
        return PostStatusEnum::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
