<?php

declare(strict_types=1);

$contactEmail = 'example@mail.com';

return [
    'dot_mail' => [
        'contact' => [
            'message_options' => [
                'from'        => $contactEmail,
                'from_name'   => 'Dotkernel',
                'to'          => [$contactEmail],
                'subject'     => 'New contact form submission',
                'body'        => [
                    'content' => '',
                    'charset' => 'utf-8',
                ],
                'attachments' => [
                    'files' => [],
                    'dir'   => [
                        'iterate'   => false,
                        'path'      => 'data/mail/attachments',
                        'recursive' => false,
                    ],
                ],
            ],
            'transport'       => 'sendmail',
        ],
        'log'     => [
            'sent' => getcwd() . '/log/mail-sent.log',
        ],
    ],
    'twig'     => [
        'globals' => [
            'contact_email' => $contactEmail,
        ],
    ],
];
