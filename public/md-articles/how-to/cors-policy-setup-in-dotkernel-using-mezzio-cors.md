---
title: "CORS policy setup in Dotkernel using mezzio-cors"
description: "A walkthrough of fixing the classic 'No Access-Control-Allow-Origin header' CORS error in a Dotkernel application by installing and configuring the mezzio-cors package."
author: "Alex Karajos"
date_published: "2021-04-28"
canonical_url: "https://www.dotkernel.com/how-to/cors-policy-setup-in-dotkernel-using-mezzio-cors/"
category: "How to's"
language: "en"
---

# CORS policy setup in Dotkernel using mezzio-cors

## TL;DR

This article explains how to fix the common "No 'Access-Control-Allow-Origin' header is present" browser error by installing and configuring the mezzio-cors package.
It covers registering the package's ConfigProvider and middleware, then creating a CORS configuration file.
The configuration supports a permissive mode, where any origin is allowed, and a restrictive mode, where only specific listed origins are allowed.
It also shows how to verify each mode is working correctly.

## Error Message

> Access to fetch at RESOURCE_URL from origin ORIGIN_URL has been blocked by CORS policy: No 'Access-Control-Allow-Origin' header is present on the requested resource.

Most developers have encountered this error when interacting with APIs.
In this article we will run through the steps required to fix it.
Cross-Origin Resource Sharing ([CORS](https://developer.mozilla.org/en-US/docs/Glossary/CORS)) is an [HTTP](https://developer.mozilla.org/en-US/docs/Glossary/HTTP)-header based mechanism that allows a server to indicate any other [origins](https://developer.mozilla.org/en-US/docs/Glossary/Origin) (domain, scheme, or port) than its own from which a browser should permit loading of resources.
The library we are going to use is [Mezzio CORS](https://docs.mezzio.dev/mezzio-cors/).

## Setup

### Install Package Mezzio CORS

Run the following command in your application's root directory:

```bash
composer require mezzio/mezzio-cors
```

### Register ConfigProvider

Open your application's `config/config.php` file and add the following lines to the `$aggregator` variable:

`Laminas\Diactoros\ConfigProvider::class,`

`Mezzio\Cors\ConfigProvider::class,`

### Register Middleware

Open your application's `config/pipeline.php` file and add the following line (preferably between `ErrorHandlerInterface::class` and the handler/middleware that will return your response):

`$app->pipe(CorsMiddleware::class);`

Don't forget to add the corresponding use at the top of the file:

`use Mezzio\Cors\Middleware\CorsMiddleware;`

### Create Config File

Create and open the file `config/autoload/cors.local.php` and add the following code inside it:

```php
<?php

declare(strict_types=1);

use Mezzio\Cors\Configuration\ConfigurationInterface;

return [
    ConfigurationInterface::CONFIGURATION_IDENTIFIER => [
        'allowed_origins' => [
            ConfigurationInterface::ANY_ORIGIN
        ],
        'allowed_headers' => ['Accept', 'Content-Type', 'Authorization'],
        'allowed_max_age' => '600',
        'credentials_allowed' => true,
        'exposed_headers' => [],
    ],
];
```

Note the value `ConfigurationInterface::ANY_ORIGIN` stored under `allowed_origins`.
Leaving this value as is makes your application accessible by any origin (permissive mode).
To restrict access, replace it with a list of origins that should have access to your application (restrictive mode), for example:

```php
'allowed_origins' => ['https://example.com'],
```

Don't forget to make a distributable version of your `config/autoload/cors.local.php` and add that to your repository.

## Testing

Make sure your application sends the Origin header and it is set to the correct value, for example `example.com`.
In your `config/autoload/cors.local.php`:

- when in permissive mode, you should see the expected response from the resource
- when in restrictive mode, if the request origin is not listed under `allowed_origins`, you should see a 403 Not authorized response

## FAQ

**Q: What causes the CORS error described in this article?**
A: It appears when a browser blocks access to a resource on another origin because the server's response doesn't include an 'Access-Control-Allow-Origin' header, per the browser's CORS (Cross-Origin Resource Sharing) policy.

**Q: What package solves CORS handling in Dotkernel?**
A: Mezzio CORS, installed by running `composer require mezzio/mezzio-cors` in the application's root directory.

**Q: What are the setup steps needed to enable CORS?**
A: Register both `Laminas\Diactoros\ConfigProvider::class` and `Mezzio\Cors\ConfigProvider::class` in `config/config.php`, add `$app->pipe(CorsMiddleware::class);` to `config/pipeline.php` (preferably between the error handler and the handler/middleware returning your response), and create a `config/autoload/cors.local.php` file with your CORS configuration.

**Q: What's the difference between permissive and restrictive mode?**
A: In permissive mode, `allowed_origins` is set to `ConfigurationInterface::ANY_ORIGIN`, making the application accessible from any origin.
In restrictive mode, you replace that value with a list of the specific origins that should have access.

**Q: How do you test whether CORS is configured correctly?**
A: Make sure your application sends the Origin header set to the correct value.
In permissive mode you should see the expected response from the resource; in restrictive mode, if the request origin isn't listed under `allowed_origins`, you should see a 403 Not authorized response.
