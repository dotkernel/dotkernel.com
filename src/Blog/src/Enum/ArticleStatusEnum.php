<?php

declare(strict_types=1);

namespace Light\Blog\Enum;

use function array_column;

enum ArticleStatusEnum: string
{
    case Draft     = 'draft';
    case Published = 'published';
    case Private   = 'private';

    /**
     * @return non-empty-string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
