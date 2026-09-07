---
title: "Handling dynamic routing using FastRoute in Dotkernel"
description: "How Dotkernel moved from static, hard-coded route declarations to a dynamic, centralized routing configuration."
author: "Florin Bidirean"
date_published: "2025-02-26"
canonical_url: "https://www.dotkernel.com/middleware/handling-dynamic-routing-using-fastroute-in-dotkernel/"
category: "Middleware"
language: "en"
---

# Handling dynamic routing using FastRoute in Dotkernel

## TL;DR
This article, the first in a series about switching from controllers to PSR-15 compliant handlers, explains how Dotkernel replaced its static, hard-coded route declarations with a centralized, dynamic configuration in `local.php`.
The change is aimed at static pages only - any method other than GET (post, put, delete) returns a 405 status code.

The goal of this update is to replace the static way of creating routes with a more dynamic implementation. The result is a cleaner approach that is easier to set up and review at a glance.

`RoutesDelegator.php` is used to configure the routes in Dotkernel applications. `Routing` allows web applications to respond to user requests by executing the correct code based on URL paths. The dynamic aspect discussed in this article moves the relevant items for each route into the `local.php` file. The RoutesDelegator then reads the route configuration and generates the routes.

> This is the first in a series of articles for switching from controllers to handlers that are PSR-15 compliant. It's aimed at static pages alone, so any other method like `post`, `put` or `delete` will return a `405 status code`.

## The old way of doing things

In the past, declaring routes had a more static, hard-coded approach. All our modules have a `RoutesDelegator.php` file in the `src` folder for each module. Each route would have an entry like the one below:

```
$app->get('/page[/{action}]', [GetPageViewHandler::class], 'page');
```

This route would be used for urls like `/page/about` or `/page/who-we-are` that we will reference later.

These are the required items:

- A **method**, in this case `get` which is aimed at static pages.
- A **path** used to direct the execution to a handler, in this case `/page[/{action}]` which also uses the optional parameter `action`.
- A **handler** to be executed, like `GetPageViewHandler` which is designed to display static pages out of the box, but can be expanded as needed.
- A unique **route name**, like `page` which can be referenced to generate redirects or set up authorization.

In a live application the route list can quickly grow to many more entries, each with its own logic:

- Product list
- Product details
- Checkout
- Contact us
- Reports
- Order History
- Blog

These items can be grouped into modules, meaning you have to dig into multiple RoutesDelegators when a routing error occurs.

## The new approach

The update centralizes the route configuration in the `config/autoload/local.php` file. Here is how the routing looks out of the box:

```
'routes'      => [
    'page' => [
        'about'      => 'about',
        'who-we-are' => 'who-we-are',
    ],
],
```

This supports the urls `/page/about` and `/page/who-we-are`.

Let's list the components under the `routes` array:

- `page` is the module name.
- The key `about` is used to build the page's path.
- The value `about` is the template file.

In this setup, the `RoutesDelegator` file doesn't need to be touched most of the time. This is how it generates the routes for each page:

```
$routes = $container->get('config')['routes'] ?? [];
foreach ($routes as $prefix => $moduleRoutes) {
    foreach ($moduleRoutes as $routeUri => $templateName) {
    $app->get(
        sprintf('/%s/%s', $prefix, $routeUri),
        GetPageViewHandler::class,
        sprintf('%s::%s', $prefix, $templateName)
    );
    }
}
```

Each item under `routes` array for each module will have its own entry. The result is you have a `get` for the `about` page and another for the `who-we-are` page.

## Advanced configuration

You have the same versatility as before for route configuration. Below we explore some scenarios that showcase the control you still have over the routing.

The template file is taken from the path, but you can still change that in the `handler` based on your requirements. Replace `$template = $request->getAttribute(RouteResult::class)->getMatchedRouteName();` with a template of your choosing, like `$template = 'my-template';`

Say you want to change the url from `/page/about`, to `/about` for SEO purposes. All you have to do is remove the `$moduleName` parameter from `sprintf('/%s/%s', $moduleName, $routeUri),` making it `sprintf('/%s', $routeUri),`. Take care not to break other routes, though!

If you need to change the url parameter, you can do that in the `local.php` file. Edit `'about' => 'about',` into `'about-us' => 'about',`. This changes the original url to `/page/about-us`, but uses the same `about` template as before.

Do you need a dynamic parameter? Edit the route entry from `'about' => 'about',` to `'about/{id}' => 'about',`. This expands the matched url to support something like `/page/about/us`, `/page/about/company`, `/page/about/123` which will allow you to customize each url with different content in the handler. All you need is to use `$request->getAttribute('id')` to tell what page you are on.

## Additional resources

[Dotkernel Light](https://github.com/dotkernel/light)

[Dotkernel Light Routing How to](https://docs.dotkernel.org/light-documentation/v1/how-tos/routing/)

[FastRoute](https://github.com/nikic/FastRoute)

## FAQ

**Q: What is the goal of this dynamic routing update?**
A: The goal is to replace the static way of creating routes with a more dynamic implementation, resulting in a cleaner approach that is easier to set up and review at a glance.

**Q: How were routes configured in the old, static approach?**
A: Each module had its own RoutesDelegator.php file in the src folder, with hard-coded route entries such as $app->get('/page', , 'page');. Each route needed a method (e.g. get), a path used to direct execution to a handler, a handler like GetPageViewHandler, and a unique route name. In a live application the route list can grow to many entries grouped across modules, meaning multiple RoutesDelegators had to be checked when a routing error occurred.

**Q: How does the new approach centralize route configuration?**
A: The update centralizes the route configuration in the config/autoload/local.php file, moving the relevant items for each route into a routes array. RoutesDelegator.php then reads this configuration and generates the routes automatically, so it doesn't need to be touched most of the time.

**Q: What do the entries under the routes array represent?**
A: For a module like page, the module name is the top-level key, the array key (e.g. about) is used to build the page's path, and its value (e.g. about) is the template file. This supports urls like /page/about and /page/who-we-are.

**Q: Can routes still be customized beyond the default setup?**
A: Yes. You can change the template used by editing the handler's template attribute, change a URL for SEO purposes by removing the module name parameter from the generated path, change the URL segment by editing the key in local.php (e.g. turning 'about' into 'about-us' while keeping the same template), or add a dynamic parameter (e.g. 'about/{id}') and read it in the handler via $request->getAttribute('id').

**Q: Does this update support methods other than GET?**
A: No - this is the first in a series of articles for switching from controllers to PSR-15 compliant handlers, and it is aimed at static pages alone, so any other method like post, put or delete will return a 405 status code.
