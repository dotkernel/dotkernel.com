<?php

declare(strict_types=1);

// return [
//     'rka' => [
//         'ip_address' => [
//             'check_proxy_headers' => false,
//             'trusted_proxies'     => [],
//             'attribute_name'      => 'ip_address',
//             'headers_to_inspect'  => [],
//         ],
//     ],
// ];

return [
    'rka' => [
        'ip_address' => [
            'check_proxy_headers' => true,
            'trusted_proxies'     => [],
            'attribute_name'      => 'ip_address',
            'headers_to_inspect'  => [
                'Forwarded',
                'X-Forwarded-For',
                'X-Forwarded',
                'X-Cluster-Client-Ip',
                'Client-Ip',
            ],
        ],
    ],
];
