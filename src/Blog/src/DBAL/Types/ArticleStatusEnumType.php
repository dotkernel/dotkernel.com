<?php

declare(strict_types=1);

namespace Light\Blog\DBAL\Types;

use Light\App\DBAL\Types\AbstractEnumType;
use Light\Blog\Enum\ArticleStatusEnum;

class ArticleStatusEnumType extends AbstractEnumType
{
    public const NAME = 'article_status_enum';
    public function getEnumClass(): string
    {
        return ArticleStatusEnum::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
