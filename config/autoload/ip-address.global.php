<?php

declare(strict_types=1);

return [
    'ip_address' => [
        'trusted_proxies' => [],
        'headers'         => ['Forwarded', 'X-Forwarded-For', 'X-Real-IP'],
    ],
];
