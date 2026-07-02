<?php

declare(strict_types=1);
use LightTest\Common\TestMode;
if (!TestMode::class::isEnabled()) {
    return [];
}

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'params' => [
                    'url' => 'sqlite3:///:memory:',
                ],
            ],
        ],
    ],
];