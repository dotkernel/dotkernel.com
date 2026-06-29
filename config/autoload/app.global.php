<?php

declare(strict_types=1);
$baseUrl = 'https://light-blog.localhost/';
$app = [
    'baseUrl' => 'https://light-blog.localhost/',
    'name' => 'Dotkernel Light | PSR-15 compliant application',
    'meta' => [
        'title'       => 'Dotkernel Light',
        'description' => 'Dotkernel is a Headless Platformfor building modern web applications				
					      Dotkernel is a collection of applications (skeletons) that use a middleware-first architecture 
					      built on top of the Mezzio microframework using Laminas components. The goal is to provide a 
					      pre-configured environment for app',

        'image'       => $baseUrl . 'uploads/opengraph/dotkernel.png',
        'type'        => 'website',
        'siteName'    => 'Dotkernel Light',
        'locale'      => 'en_US',
        'url'         => $baseUrl,
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
