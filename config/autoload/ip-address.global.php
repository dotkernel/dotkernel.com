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

// @see https://github.com/akrabat/ip-address-middleware/blob/main/src/IpAddress.php
return [
    'rka' => [
        'ip_address' => [
            // Inspect proxy headers below instead of using REMOTE_ADDR only.
            'check_proxy_headers' => true,
            // Proxy IPs/CIDRs/wildcards trusted to set the headers below.
            'trusted_proxies' => [],
            // Request attribute the resolved IP is stored under.
            'attribute_name' => 'ip_address',
            // Headers checked, in order, for the client IP.
            'headers_to_inspect' => [
                // nginx specific
                'X-Real-IP',
                'X-Forwarded-For',
                // cloudflare specific
                'CF-Connecting-IP',
                'True-Client-IP',
                // generic
                'Forwarded',
                'X-Forwarded',
                'X-Cluster-Client-Ip',
                'Client-Ip',
            ],
        ],
    ],
];
