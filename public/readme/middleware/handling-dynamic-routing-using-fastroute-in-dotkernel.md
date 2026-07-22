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

This article, the first in a series about switching from controllers to PSR-15 compliant handlers, explains how Dotkernel replaced its static, hard-coded route declarations with a centralized, dynamic configuration in `local.php`. The change is aimed at static pages only - any method other than GET (post, put, delete) returns a 405 status code.

## The old way of doing things

Routes were declared in a `RoutesDelegator.php` file present in the `src` folder of each module, with entries like:

```php
$app->get('/page', , 'page');
```

This route serves urls like `/page/about` or `/page/who-we-are`. Each entry required:

- A **method**, such as `get`, aimed at static pages.
- A **path** used to direct execution to a handler, such as `/page`, which also uses the optional `action` parameter.
- A **handler** to execute, such as `GetPageViewHandler`, designed to display static pages out of the box but expandable as needed.
- A unique **route name**, such as `page`, referenceable for redirects or authorization.

In a live application the route list can grow to many entries across areas like product list, product details, checkout, contact us, reports, order history, and blog - grouped into modules, meaning a routing error could require digging through multiple RoutesDelegators.

## The new approach

The update centralizes route configuration in `config/autoload/local.php`, under a `routes` array. `RoutesDelegator.php` reads this configuration and generates the routes, so it doesn't need to be touched most of the time:

```php
$routes = $container->get('config') ?? [];
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

Under the `routes` array: the module name (e.g. `page`) is the top-level key, the key (e.g. `about`) builds the page's path, and the value (e.g. `about`) is the template file. This supports `/page/about` and `/page/who-we-are`.

## Advanced configuration

The same versatility as before is retained:

1. **Change the template**: replace `$template = $request->getAttribute(RouteResult::class)->getMatchedRouteName();` with a fixed template, like `$template = 'my-template';`.
2. **Change the URL for SEO** (e.g. `/page/about` to `/about`): remove the `$moduleName` parameter from `sprintf('/%s/%s', $moduleName, $routeUri)`, making it `sprintf('/%s', $routeUri)` - carefully, to avoid breaking other routes.
3. **Change the URL segment**: edit `'about' => 'about',` to `'about-us' => 'about',` in `local.php`, changing the url to `/page/about-us` while still using the `about` template.
4. **Add a dynamic parameter**: edit `'about' => 'about',` to `'about/{id}' => 'about',`, supporting urls like `/page/about/us`, `/page/about/company`, `/page/about/123`, and read the value in the handler with `$request->getAttribute('id')`.

## FAQ

**Q: What is the goal of this dynamic routing update?**
A: To replace static, hard-coded route declarations with a more dynamic, centralized implementation that is easier to set up and review.

**Q: How were routes configured in the old, static approach?**
A: In a per-module `RoutesDelegator.php` file, with hard-coded entries specifying a method, path, handler, and route name.

**Q: How does the new approach centralize route configuration?**
A: Route data moves into a `routes` array in `config/autoload/local.php`, which `RoutesDelegator.php` reads to generate routes automatically.

**Q: What do the entries under the routes array represent?**
A: The module name, the URL path segment (key), and the template file to render (value).

**Q: Can routes still be customized beyond the default setup?**
A: Yes - templates, URL structure, URL segments, and dynamic parameters can all still be adjusted.

**Q: Does this update support methods other than GET?**
A: No - it targets static pages only; other HTTP methods return a 405 status code.

## Resources

- [Dotkernel Light](https://github.com/dotkernel/light)
- [Dotkernel Light Routing How-to](https://docs.dotkernel.org/light-documentation/v1/how-tos/routing/)
- [FastRoute](https://github.com/nikic/FastRoute)
