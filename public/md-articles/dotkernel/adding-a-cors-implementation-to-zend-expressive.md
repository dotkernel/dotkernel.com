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

This article is a guide on how to add a CORS implementation on an existing Dotkernel3 project.

## The issue

If you're facing this message:

"Access to XMLHttpRequest at 'url' has been blocked by cors policy. No 'Access-Control-Allow-Origin header is present on the requested resource."

It means the server didn't sent the header that lets you access its data through a local client (eg.: browser).

This issue is most common when trying to get some data (usually json) that you want to process using JavaScript.

The error looks similar to the image below:

![](/uploads/article/019f8a80-cc4d-71e9-9af2-595b3eb4c793/Screenshot-2019-04-06-at-15.03.21-1024x165-1-1024x165.png)

## The solution

A simple implementation would be using [Tuupola's Cors Middleware](https://packagist.org/packages/tuupola/cors-middleware) package.

This article was inspired by: [akrabat.com/implementing-tuupola-cors-in-expressive](https://akrabat.com/implementing-tuupola-cors-in-expressive/)

### Adding the package to your project

Run the following command in your project:

```
composer require tuupola/cors-middleware
```

At the time writing this article the current package version is: 0.9.4.

Follow the next steps to get your Zend Expressive or Dotkernel3 project **CORS friendly**.

### Create the CORS config file

Create a **cors.global.php** file in the config/autoload directory.

```
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

We'll come back at this file to register the CORS middleware.

### Creating a factory for the middleware

The factory should look like the one below.

The code below extracts de config from the **cors** key if provided or initializes an empty array and instantiates the **Tuupola CORS middleware**.

```
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

### Registering the CORS middleware

Back at **cors.global.php** we will register the cors middleware so our custom implemented factory will be used to create the middleware. (basic config example below)

```
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

### Final step: Adding (registering) the CorsMiddleware in the pipeline

In this last step we only need to add the CorsMiddleware in config/pipelines.php

```
// don't forget the use statement
use Tuupola\Middleware\CorsMiddleware;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    // ...
    $app->pipe(CorsMiddleware::class);
    // ...
};
```

Add the cors middleware **after** the Error handler and **before** the middleware providing the data you want to access to make sure everything runs smoothly.

This should get your project working with CORS.

## FAQ

**Q: What causes the "No 'Access-Control-Allow-Origin' header" error?**
A: It means the server didn't send the header that lets a local client, such as a browser, access its data. This is most common when trying to fetch data (usually JSON) that you want to process using JavaScript.

**Q: What package does the article use to add CORS support?**
A: Tuupola's Cors Middleware package, installed by running composer require tuupola/cors-middleware in the project.

**Q: Where does the CORS configuration live?**
A: In a cors.global.php file created in the config/autoload directory, containing a "cors" key with settings like origin, methods, headers.allow, headers.expose, credentials, and cache.

**Q: How is the CorsMiddleware wired into the container?**
A: A CorsMiddlewareFactory extracts the "cors" config array (or an empty array if it's not provided) and instantiates Tuupola's CorsMiddleware with it. That factory is then registered under the "dependencies" > "factories" section of cors.global.php.

**Q: Where should the CORS middleware be added in the pipeline?**
A: In config/pipelines.php via $app->pipe(CorsMiddleware::class), placed after the Error handler and before the middleware that provides the data you want to access.
