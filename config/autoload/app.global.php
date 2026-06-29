<?php

declare(strict_types=1);

$app = [
    'name' => 'Dotkernel Light | PSR-15 compliant application',
    'meta' => [
        'title'       => 'Dotkernel Light',
        'description' => 'Dotkernel is a Headless Platformfor building modern web applications				
					      Dotkernel is a collection of applications (skeletons) that use a middleware-first architecture 
					      built on top of the Mezzio microframework using Laminas components. The goal is to provide a 
					      pre-configured environment for app',

        'image'       => '/uploads/logos/logo.png',
        'type'        => 'website',
        'siteName'    => 'Dotkernel Light',
        'locale'      => 'en_US',
        'url'         => 'https://www.dotkernel.com',
        'twitterCard' => 'summary_large_image',
        'twitterSite' => '@dotkernel',
    ],
];

return [
    'app'  => $app,
    'twig' => [
        'globals' => [
            'app' => $app,
        ],
    ],
];
