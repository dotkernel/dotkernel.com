<?php

declare(strict_types=1);

namespace Light\App\Enum;

enum ContactTopicEnum: string
{
    case Migration = 'migration';
    case Project   = 'project';
    case Oss       = 'oss';
    case Other     = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Migration => 'Migration',
            self::Project   => 'Project work',
            self::Oss       => 'Open source',
            self::Other     => 'Something else',
        };
    }
}
