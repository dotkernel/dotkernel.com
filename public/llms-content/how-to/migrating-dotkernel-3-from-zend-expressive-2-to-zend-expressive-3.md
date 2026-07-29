---
title: "Migrating Dotkernel 3 from Zend Expressive 2 to Zend Expressive 3"
description: "A walkthrough of migrating a Dotkernel 3 application with controller-based middleware from Zend Expressive 2 to Zend Expressive 3, covering dependency, configuration, and pipeline changes."
author: "Gabi DJ"
date_published: "2018-06-13"
canonical_url: "https://www.dotkernel.com/how-to/migrating-dotkernel-3-from-zend-expressive-2-to-zend-expressive-3/"
category: "How to's"
language: "en"
---

# Migrating Dotkernel 3 from Zend Expressive 2 to Zend Expressive 3

## TL;DR

This guide covers migrating a Dotkernel 3 instance from Zend Expressive 2 to Zend Expressive 3, for projects that only contain controller-based middleware.
Old middleware must first be refactored to the `psr/http-server-middleware` interfaces, since Delegates become RequestHandlers.
The steps then cover updating `composer.json` dependencies, registering new ConfigProviders, wrapping `routes.php` and `pipeline.php` in callables, and replacing the old `pipeRoutingMiddleware()`/`pipeDispatchMiddleware()` calls with their PSR-15 equivalents.

## Packages

In `composer.json` replace the matching repositories with the following:

```json
"dotkernel/dot-authentication-service":"^1.0",
"dotkernel/dot-authentication-service":"^1.0",
"dotkernel/dot-authentication-web":"^1.0.1",
"dotkernel/dot-authentication":"^1.0",
"dotkernel/dot-authorization":"^0.1.2",
"dotkernel/dot-controller":"^1.0",
"dotkernel/dot-controller-plugin-authentication":"^1.0",
"dotkernel/dot-controller-plugin-authorization":"^1.0",
"dotkernel/dot-controller-plugin-forms":"^1.0",
"dotkernel/dot-controller-plugin-flashmessenger":"^1.0",
"dotkernel/dot-controller-plugin-mail":"^1.0",
"dotkernel/dot-controller-plugin-session":"^1.0",
"dotkernel/dot-form":"^1.1.1",
"dotkernel/dot-filter":"^1.1.1",
"dotkernel/dot-flashmessenger":"^1.0",
"dotkernel/dot-helpers":"^1.0",
"dotkernel/dot-inputfilter":"^1.1",
"dotkernel/dot-mail":"^1.0",
"dotkernel/dot-mapper":"^1.0",
"dotkernel/dot-navigation":"^1.0",
"dotkernel/dot-rbac-guard":"^1.0",
"dotkernel/dot-session":"^3.0",
"dotkernel/dot-twigrenderer":"^1.1",
"dotkernel/dot-user":"^1.0",
"dotkernel/dot-rbac":"^0.2.1",
"dotkernel/dot-validator":"^1.1",

"zendframework/zend-escaper":"^2.6",
"zendframework/zend-expressive-helpers":"^5.0",
"zendframework/zend-expressive-twigrenderer":"^2.0",
"zendframework/zend-expressive-template":"^2.0",
"zendframework/zend-expressive":"^3.0",
"zendframework/zend-expressive-fastroute":"^3.0",
"zendframework/zend-expressive-tooling":"^1.0",
"zendframework/zend-expressive-router":"^3.0",
"zendframework/zend-stratigility":"^3.0",
"zendframework/zend-component-installer":"^2.0
```

Also update require-dev dependencies:

```json
"zendframework/zend-expressive-tooling:": "^1.0",
"zendframework/zend-component-installer": "^2.0",
```

Remove packages:

- `http-interop/http-middleware`
- `webimpress/http-middleware-compatibility`

## Configurations

### Main Configuration

In `config/config.php` add the following config providers:

```php
// zend expressive & middleware factory
\Zend\Expressive\ConfigProvider::class,

// router config
\Zend\Expressive\Router\ConfigProvider::class,
\Zend\Expressive\Router\FastRouteRouter\ConfigProvider::class,

\Zend\Expressive\Twig\ConfigProvider::class,
\Zend\Expressive\Helper\ConfigProvider::class,

// handler runner
\Zend\HttpHandlerRunner\ConfigProvider::class,
```

Make sure they are the first ConfigProviders or before cached config (`ArrayProvider`).

### Routing

Wrap routing from `config/routes.php` in a callable with the following format:

```php
return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    /** @var \Zend\Expressive\Application $app */
    $app->route('/', , , 'home');
};
```

Add the following use statements and make sure the names are not duplicate:

```php
use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;
```

### Pipeline

Wrap routing from `config/pipeline.php` in a callable with the following format:

```php
return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    /** @var \Zend\Expressive\Application $app */
    $app->route('/', , , 'home');
};
```

Add the following use statements and make sure the names are not duplicate:

```php
use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;
```

#### Routing Middleware Migration

Add the following use statements:

```php
use Zend\Expressive\Router\Middleware\RouteMiddleware;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;
```

Replace the following lines to reflect the changes:

- `$app->pipeRoutingMiddleware();` becomes `$app->pipe(RouteMiddleware::class);`
- `$app->pipeDispatchMiddleware();` becomes `$app->pipe(DispatchMiddleware::class);`

You can check the complete guides and example files from the following links:

- [Migration guide for Dotkernel Frontend](https://github.com/dotkernel/frontend/tree/master/docs)
- [Migration guide for Dotkernel Admin](https://github.com/dotkernel/admin/tree/master/docs)

## FAQ

**Q: What should you read before starting the migration?**
A: For a better understanding of the migration and how it affects support, it's recommended to read Zend Expressive's article on migration regarding HTTP interop.

**Q: What happens to old middleware during the migration?**
A: If a project contains old middleware, it must be refactored to reflect the interfaces provided in the psr/http-server-middleware package.
As part of this, Delegates become RequestHandlers, using interfaces from the psr/http-server-handler package.

**Q: Which packages should be removed during migration?**
A: `http-interop/http-middleware` and `webimpress/http-middleware-compatibility` should be removed from `composer.json`.

**Q: What needs to be added to the main configuration file?**
A: In `config/config.php`, several ConfigProviders need to be added - for Zend Expressive, the router, FastRoute router, Twig, helpers, and the handler runner - and they must be the first ConfigProviders or placed before the cached config (`ArrayProvider`).

**Q: How do the routes.php and pipeline.php files change?**
A: Routing from `config/routes.php` and the pipeline from `config/pipeline.php` both need to be wrapped in a callable of the form `function (Application $app, MiddlewareFactory $factory, ContainerInterface $container)`, with the corresponding use statements added.

**Q: What replaces pipeRoutingMiddleware() and pipeDispatchMiddleware()?**
A: `$app->pipeRoutingMiddleware();` becomes `$app->pipe(RouteMiddleware::class);`, and `$app->pipeDispatchMiddleware();` becomes `$app->pipe(DispatchMiddleware::class);`.
