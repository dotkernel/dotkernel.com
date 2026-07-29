---
title: "Adding a CORS implementation to Zend Expressive"
description: "A guide on how to add a CORS implementation to an existing Dotkernel3 project using Tuupola's Cors Middleware package."
author: "Gabi DJ"
date_published: "2019-04-08"
canonical_url: "https://www.dotkernel.com/dotkernel/adding-a-cors-implementation-to-zend-expressive/"
category: "Dotkernel"
language: "en"
---

# Adding a CORS implementation to Zend Expressive

## TL;DR
When a client-side request is blocked with a "No 'Access-Control-Allow-Origin' header" error, it's because the server isn't sending the header that allows a browser to access its data (most common when fetching JSON to process with JavaScript).
This guide adds CORS support to a Zend Expressive / Dotkernel3 project using Tuupola's Cors Middleware package.

## The issue

If you're facing the error:

> "Access to XMLHttpRequest at 'url' has been blocked by cors policy.
> No 'Access-Control-Allow-Origin header is present on the requested resource."

it means the server didn't send the header that lets you access its data through a local client (e.g. a browser).
This issue is most common when trying to get data (usually JSON) that you want to process using JavaScript.

## The solution

A simple implementation uses [Tuupola's Cors Middleware](https://packagist.org/packages/tuupola/cors-middleware) package.
(This article was inspired by [akrabat.com/implementing-tuupola-cors-in-expressive](https://akrabat.com/implementing-tuupola-cors-in-expressive/).)

### 1. Add the package to your project

```shell
composer require tuupola/cors-middleware
```

At the time of writing, the current package version is 0.9.4.

### 2. Create the CORS config file

Create a `cors.global.php` file in the `config/autoload` directory:

```php
return [
    'cors' => [
        "origin" => [],
        "methods" => [],
        "headers.allow" => [],
        "headers.expose" => [],
        "credentials" => false,
        "cache" => 0,
    ],
    'dependencies' => [],
];
```

### 3. Create a factory for the middleware

The factory extracts the config from the `cors` key (or initializes an empty array) and instantiates the Tuupola CORS middleware:

```php
<?php

namespace App\Cors;

use Tuupola\Middleware\CorsMiddleware;

class CorsMiddlewareFactory
{
    public function __invoke($container)
    {
        $corsConfig = $container->get('config')['cors'] ?? [];
        return new CorsMiddleware($corsConfig);
    }
}
```

### 4. Register the CORS middleware

Back in `cors.global.php`, register the middleware so the factory above is used to create it:

```php
<?php

use App\Cors\CorsMiddlewareFactory;
use Tuupola\Middleware\CorsMiddleware;

return [
    'cors' => [
        "origin" => [],
        "methods" => [],
        "headers.allow" => [],
        "headers.expose" => [],
        "credentials" => false,
        "cache" => 0,
    ],
    'dependencies' => [
        'factories' => [
            CorsMiddleware::class => CorsMiddlewareFactory::class,
        ]
    ]
];
```

### 5. Add the CorsMiddleware to the pipeline

In `config/pipelines.php`:

```php
// don't forget the use statement
use Tuupola\Middleware\CorsMiddleware;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    // ...
    $app->pipe(CorsMiddleware::class);
    // ...
};
```

Add the CORS middleware **after** the Error handler and **before** the middleware providing the data you want to access, to make sure everything runs smoothly.
This should get your project working with CORS.

## FAQ

**Q: What causes the "No 'Access-Control-Allow-Origin' header" error?**
A: It means the server didn't send the header that lets a local client, such as a browser, access its data.
This is most common when trying to fetch data (usually JSON) that you want to process using JavaScript.

**Q: What package does the article use to add CORS support?**
A: Tuupola's Cors Middleware package, installed by running `composer require tuupola/cors-middleware` in the project.

**Q: Where does the CORS configuration live?**
A: In a `cors.global.php` file created in the config/autoload directory, containing a "cors" key with settings like origin, methods, headers.allow, headers.expose, credentials, and cache.

**Q: How is the CorsMiddleware wired into the container?**
A: A CorsMiddlewareFactory extracts the "cors" config array (or an empty array if it's not provided) and instantiates Tuupola's CorsMiddleware with it.
That factory is registered under the "dependencies" > "factories" section of cors.global.php.

**Q: Where should the CORS middleware be added in the pipeline?**
A: In config/pipelines.php via `$app->pipe(CorsMiddleware::class)`, placed after the Error handler and before the middleware that provides the data you want to access.

## Resources

- [Tuupola's Cors Middleware package](https://packagist.org/packages/tuupola/cors-middleware)
- [Implementing Tuupola CORS in Expressive (inspiration article)](https://akrabat.com/implementing-tuupola-cors-in-expressive/)
