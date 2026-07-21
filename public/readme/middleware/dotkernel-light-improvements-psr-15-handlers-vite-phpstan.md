---
title: "Dotkernel Light improvements: PSR-15 Handlers, Vite, PHPStan"
description: "An overview of recent improvements to Dotkernel Light: migrating to PSR-15 handlers, adopting Vite, switching to PHPStan, and other smaller updates."
author: "Florin Bidirean"
date_published: "2025-05-02"
canonical_url: "https://new.dotkernel.com/middleware/dotkernel-light-improvements-psr-15-handlers-vite-phpstan/"
category: "Middleware"
language: "en"
---

# Dotkernel Light improvements: PSR-15 Handlers, Vite, PHPStan

## TL;DR

[Dotkernel Light](https://github.com/dotkernel/light) is a PSR-15 compliant application built on Mezzio and Laminas, aimed at simple websites like presentation sites. Since its last update, it has moved from controllers to PSR-15 handlers, adopted Vite as its bundler, replaced Psalm with PHPStan, and picked up several smaller improvements.

## Migrating from controllers to handlers

dotkernel/dot-controller isn't going away, but Dotkernel Light has switched to PSR-15 handlers instead. PSR-7 defines the request handler as an individual component that processes a request and produces a response; since the request can be filtered or augmented in a middleware architecture by the time it reaches custom code, handlers split code into more manageable chunks and a cleaner file structure, and make it easier to understand an application's functionality.

## Implementing PSR-15 compatible handlers

PSR-15, defined by the PHP Framework Interop Group, specifies common interfaces for HTTP server request handlers and middleware that use HTTP messages as described by PSR-7. The MVC design pattern is considered obsolete by respected members of the PHP community, so Dotkernel Light is replacing it with the middleware and HTTP message design pattern.

## Adopting a naming pattern for PSR-15 handlers

A naming pattern was devised for PSR-15 handlers that highlights the method, resource, and action for each file, so navigation and onboarding are easier.

## Implementing Vite

[Vite](https://vite.dev/) replaces [webpack](https://webpack.js.org) as the static modules bundler. It concatenates and compresses `.css` and `.js` files for faster downloads, and preprocesses `.scss` files into `.css`. Vite was configured to work similarly to webpack for existing Light developers; the migration was justified by easier dependency management and execution speed, and it's highly recommended by the PHP community.

## Replacing Psalm with PHPStan

Both Psalm and PHPStan are respected, widely-used static analysis tools. Some big names in the PHP ecosystem made the same switch, so Dotkernel followed suit. Functionality is similar to the previous tool, but growing interest and improved detection quality made PHPStan the better choice. PHPStan is configured to run at rule level 8 to help prevent bugs and write better code.

## Other updates

- Adding support for PHP 8.4.
- Enabling PHPStan and Qodana to run for PHP 8.4 as well.
- Cleaning up and adapting the error configuration file to make use of the latest features.
- Updating laminas/laminas-coding-standard to the latest major version.
- Updating composer.json to be up-to-date with the latest releases for each dependency, and removing obsolete items.
- Implementing a more reliable and efficient post-install script.
- Removing dotkernel/dot-twigrenderer in favor of a direct implementation of mezzio/mezzio-twigrenderer.

## FAQ

**Q: Why did Dotkernel Light switch from controllers to handlers?**
A: Handlers split code into more manageable chunks and a cleaner file structure, and make it easier to understand an application's functionality, especially given that requests can be filtered or augmented by middleware before reaching custom code.

**Q: What naming pattern is used for the new PSR-15 handlers?**
A: One that highlights the method, resource, and action for each handler file, so file names reflect functionality at a glance.

**Q: Why was Vite adopted instead of webpack?**
A: For easier dependency management and execution speed, and because it's highly recommended by the PHP community; it was configured to work similarly to webpack for existing developers.

**Q: Why was Psalm replaced with PHPStan?**
A: Growing interest and improved detection quality, following the lead of other big names in the PHP ecosystem; PHPStan runs at rule level 8.

**Q: What other smaller updates were made to Dotkernel Light?**
A: PHP 8.4 support, PHPStan/Qodana running on PHP 8.4, error configuration cleanup, an updated coding standard, refreshed composer.json dependencies, a more reliable post-install script, and removal of dot-twigrenderer in favor of mezzio-twigrenderer directly.

## Resources

- [Dotkernel Light Git](https://github.com/dotkernel/light)
- [Dotkernel Light demo](https://light.dotkernel.net/)
- [Dotkernel Light documentation](https://docs.dotkernel.org/light-documentation/)
- [PHP Framework Interop Group](https://www.php-fig.org/)
- [PSR-15: HTTP Server Request Handlers](https://www.php-fig.org/psr/psr-15/)
- [Static Analysis – Replacing Psalm with PHPStan](https://www.dotkernel.com/php-development/static-analysis-replacing-psalm-with-phpstan/)
