---
title: "Using the URLGenerator work in FastRoute"
description: "A how-to on avoiding FastRoute's slash-suffix quirk when declaring routes in DotKernel 3, and on referencing routes by name via the URLGenerator in controllers and Twig views."
author: "Jesper"
date_published: "2017-09-04"
canonical_url: "https://www.dotkernel.com/how-to/using-the-urlgenerator-work-in-fastroute/"
category: "How to's"
language: "en"
---

# Using the URLGenerator work in FastRoute

## TL;DR

DotKernel 3 uses FastRoute under the hood, which is fast but has a quirk around slash-suffixes that a wrong route setup can trigger, leading to hard-to-diagnose errors.
Optional slashes must be kept inside the optional block of a route definition, or the URLGenerator ends up producing the wrong route.
Every named route can then be referenced instead of hard-coded, using `$this->url()` in controllers or the `path()` function in Twig views.

## Declaring Routes

When defining routes in the `routes.php` file, they need to follow a specific pattern, a usual route would look like this:

```php
$app->route('/page', , , 'page');
```

> Notice the '/page' part, as this is crucial. FastRoute cannot handle a slash-suffix, which means that /page will give you the indexAction, whereas /page/ will give you a 404.

To prevent the URLGenerator from adding a slash-suffix, we need to make sure that the optional slash is included in the optional block.
The above example is correct; if we instead wrote it like this, it'd be woefully bad:

```php
$app->route('/page/', , , 'page');
```

> Notice that the slash has changed places and is now outside the optional block. This would generate a /page/ route for the index action, which is bad, so always include any optional parts, even slashes, in the optional block.

## Referencing Routes

Developers are used to hand-write and hard-code URLs into anchor tags, but this is a bad idea, as a URL may change and then you'd have to go through the entire app to find every single reference for it.
Instead, every route we define has a name that can be used to reference it in the URLGenerator.

```php
$app->route('/page', , , 'page');
```

> That very last part there, that's the route name, which can be referenced in any view or controller to generate a route to a specific route in the app.

To use it in a controller, you have access to `$this->url();`, which takes the route name, in this case, 'page', as the first parameter, and then any optional blocks as the second parameter.
A controller redirect may then look like this:

```php
return new RedirectResponse($this->url('contact', ));
```

Using the URLGenerator in views is approximately the same, since you have access to the global `path()` method.
The path method works the same way as the `url()` method, and takes the route name as the first parameter, and any optional blocks as its second parameter.
This is of course done in Twig, so we need to put curly brackets around it since it's dynamic and not static HTML.
It could look like this:

```
{{ path(name: 'page', parameters: {action: 'thank-you'}, relative: false) }}
```

## FAQ

**Q: What is a common quirk to watch out for when declaring routes in FastRoute?**
A: FastRoute cannot handle a slash-suffix: for a route defined as `/page`, requesting /page works, but /page/ gives a 404.
Any optional part, including slashes, needs to be included inside the optional block, not appended outside it - otherwise the URLGenerator ends up producing the wrong route for the index action.

**Q: Why should route names be used instead of hard-coded URLs?**
A: Hand-writing and hard-coding URLs into anchor tags is a bad idea because a URL may change and then every reference throughout the app would need to be updated.
Instead, every defined route has a name that can be referenced through the URLGenerator.

**Q: How do you generate a URL from a route name inside a controller?**
A: Use `$this->url()`, passing the route name as the first parameter and any optional blocks as the second parameter, for example in a redirect: `return new RedirectResponse($this->url('contact'));`.

**Q: How do you generate a URL from a route name inside a Twig view?**
A: Use the global `path()` function, which works the same way as `url()`, taking the route name as the first parameter and any optional blocks as the second parameter, wrapped in curly brackets since it's dynamic Twig code.
