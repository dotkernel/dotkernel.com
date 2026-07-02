<?php

declare(strict_types=1);

$baseUrl = 'https://new.dotkernel.com/';
$app     = [
    'baseUrl' => $baseUrl,
    'name'    => 'Dotkernel Light | PSR-15 compliant application',
    'meta'    => [
        'title'       => 'Dotkernel | Headless Platform for modern web application',
        'description' => 'Dotkernel is a Headless Platform for building modern web applications				
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
