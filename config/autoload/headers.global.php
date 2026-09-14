<?php

declare(strict_types=1);

$llmsTxtLink    = '</llms.txt>; rel="llms-txt", </llms-full.txt>; rel="llms-full-txt"';
$serviceDocLink = static fn (string $url): string => sprintf('<%s>; rel="service-doc", ', $url) . $llmsTxtLink;

return [
    'dot_response_headers' => [
        '*'                  => [
            'X-Powered-By' => [
                'value'     => 'Dotkernel',
                'overwrite' => true,
            ],
            'X-Llms-Txt'   => [
                'value'     => '/llms.txt',
                'overwrite' => true,
            ],
            'Link'         => [
                'value'     => $llmsTxtLink,
                'overwrite' => true,
            ],
        ],
        'app::index'         => [
            'Link' => [
                'value'     => $serviceDocLink('https://docs.dotkernel.org/'),
                'overwrite' => true,
            ],
        ],
        'page::api'          => [
            'Link' => [
                'value'     => $serviceDocLink('https://docs.dotkernel.org/api-documentation/'),
                'overwrite' => true,
            ],
        ],
        'page::admin'        => [
            'Link' => [
                'value'     => $serviceDocLink('https://docs.dotkernel.org/admin-documentation/'),
                'overwrite' => true,
            ],
        ],
        'page::queue'        => [
            'Link' => [
                'value'     => $serviceDocLink('https://docs.dotkernel.org/queue-documentation/'),
                'overwrite' => true,
            ],
        ],
        'page::light'        => [
            'Link' => [
                'value'     => $serviceDocLink('https://docs.dotkernel.org/light-documentation/'),
                'overwrite' => true,
            ],
        ],
        'page::frontend'     => [
            'Link' => [
                'value'     => $serviceDocLink('https://docs.dotkernel.org/frontend/'),
                'overwrite' => true,
            ],
        ],
        'page::wsl2'         => [
            'Link' => [
                'value'     => $serviceDocLink('https://docs.dotkernel.org/development/v2/terminal/'),
                'overwrite' => true,
            ],
        ],
        'page::architecture' => [
            'Link' => [
                'value'     => $serviceDocLink('https://docs.dotkernel.org/api-documentation/'),
                'overwrite' => true,
            ],
        ],
    ],
];
