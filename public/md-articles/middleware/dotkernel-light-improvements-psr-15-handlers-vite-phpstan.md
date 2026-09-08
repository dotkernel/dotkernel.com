---
title: "Dotkernel Light improvements: PSR-15 Handlers, Vite, PHPStan"
description: "An overview of recent improvements to Dotkernel Light: migrating to PSR-15 handlers, adopting Vite, switching to PHPStan, and other smaller updates."
author: "Florin Bidirean"
date_published: "2025-05-02"
canonical_url: "https://www.dotkernel.com/middleware/dotkernel-light-improvements-psr-15-handlers-vite-phpstan/"
category: "Middleware"
language: "en"
---

# Dotkernel Light improvements: PSR-15 Handlers, Vite, PHPStan

## TL;DR

[Dotkernel Light](https://github.com/dotkernel/light) is a PSR-15 compliant application built on Mezzio and Laminas, aimed at simple websites like presentation sites.
Since its last update, it has moved from controllers to PSR-15 handlers, adopted Vite as its bundler, replaced Psalm with PHPStan, and picked up several smaller improvements.

[Dotkernel Light](https://github.com/dotkernel/light) is the smallest complete Mezzio application - a PSR-15 pipeline, routing and templating, with nothing to strip out. A good starting point for a simple site, like a presentation site, that can be expanded as needed.

Dotkernel Light has come a long way since our [last update](https://www.dotkernel.com/dotkernel/dotkernel-light-the-best-choice-for-your-presentation-site/). It's just as useful for a presentation site, but our perfectionist devs thought we could do better. Let's see what we have improved to make your use of Light that much more convenient.

## Migrating from controllers to handlers

[dotkernel/dot-controller](https://github.com/dotkernel/dot-controller) isn't going away, but we have switched to [PSR-15](https://www.php-fig.org/psr/psr-15/) handlers instead.
[PSR-7 HTTP message interfaces](https://www.php-fig.org/psr/psr-7/) defines the request handler as an individual component that processes a request and produces a response.
Given that it's used in a middleware architecture, the request can be wildly different (e.g. filtered, augmented) by the time it reaches your custom code.
Handlers split the code into more manageable chunks and cleaner file structure.
An added benefit is it allows us to figure out an application's functionality easier.
We will expand on this aspect further down in the article, in the 'Naming pattern' chapter.

## Implementing PSR-15 compatible handlers

We are striving to implement every feature with the most modern standards and design patterns in mind.
The [PHP Framework Interop Group](https://www.php-fig.org/) defines PSR-15 as common interfaces for HTTP server request handlers and HTTP middleware that use HTTP messages as described by PSR-7.
The MVC design pattern is considered obsolete by respected members of the PHP community, so we are replacing it with Middleware and HTTP message design pattern.

## Adopting a naming pattern for PSR-15 handlers

Now the file names reflect the functionality at a glance.
We devised a naming pattern for our PSR-15 handlers that highlights the method, resource and action for each file to make navigation and onboarding that much easier.
We like to keep things tidy, as well as informative.

## Implementing Vite

[Vite](https://vite.dev/) replaces [webpack](https://webpack.js.org) as our static modules bundler.
It concatenates and compresses `.css` and `.js` files to enable faster downloads.
It also preprocesses `.scss` files into `.css`.
As far as devs using Light are concerned, Vite was configured to work similarly to webpack.
We decided the technical advantages like easier dependency management and execution speed justified the migration to Vite.
It's also highly recommended by the PHP community.

## Replacing Psalm with PHPStan

Both [Psalm](https://psalm.dev/) and [PHPStan](https://phpstan.org/) are respected and widely-used static analysis tools.
Some of the big names in the PHP ecosystem have opted for the same switch, so we decided to follow suit.
Ultimately, the functionality is similar to our previous tool, but the growing interest and improved detection quality make PHPStan a great choice for our applications.
PHPStan is configured to run at rule level 8 to help prevent bugs and write better code.

## Other updates

Some of the smaller updates involve:

- Adding support for PHP 8.4.
- Enabling [PHPStan](https://phpstan.org/) and [Qodana](https://www.jetbrains.com/qodana/) to run for PHP 8.4 as well.
- Cleaning up and adapting the error configuration file to make use of the latest features.
- Updating [laminas/laminas-coding-standard](https://github.com/laminas/laminas-coding-standard) to the latest major version.
- Updating composer.json to make sure it's up-to-date with the latest releases for each dependency, as well as to remove any now-obsolete items.
- Implementing a more reliable and efficient post install script to help get you up and running that much faster.
- Removing [dotkernel/dot-twigrenderer](https://github.com/dotkernel/dot-twigrenderer) in favor of a direct implementation of [mezzio/mezzio-twigrenderer](https://github.com/mezzio/mezzio-twigrenderer).

## FAQ

**Q: Why did Dotkernel Light switch from controllers to handlers?**
A: Dotkernel Light switched to PSR-15 handlers instead of dotkernel/dot-controller (which isn't going away, but is no longer used here). Since the request can be filtered or augmented by the time it reaches custom code in a middleware architecture, handlers split the code into more manageable chunks and a cleaner file structure, and make an application's functionality easier to figure out.

**Q: What naming pattern is used for the new PSR-15 handlers?**
A: A naming pattern was devised that highlights the method, resource, and action for each handler file, so file names reflect their functionality at a glance, making navigation and onboarding easier.

**Q: Why was Vite adopted instead of webpack?**
A: Vite replaces webpack as the static modules bundler, concatenating and compressing .css and .js files and preprocessing .scss files into .css. It was configured to work similarly to webpack for developers using Light, and was chosen for its easier dependency management, execution speed, and its recommendation by the PHP community.

**Q: Why was Psalm replaced with PHPStan?**
A: Both Psalm and PHPStan are respected, widely-used static analysis tools with similar functionality, but growing interest and improved detection quality led Dotkernel to follow other big names in the PHP ecosystem and switch to PHPStan, which is configured to run at rule level 8 to help prevent bugs and write better code.

**Q: What other smaller updates were made to Dotkernel Light?**
A: Other updates include adding support for PHP 8.4, enabling PHPStan and Qodana to run for PHP 8.4, cleaning up the error configuration file, updating laminas/laminas-coding-standard to its latest major version, updating composer.json dependencies and removing obsolete items, implementing a more reliable and efficient post-install script, and removing dotkernel/dot-twigrenderer in favor of a direct implementation of mezzio/mezzio-twigrenderer.

## Resources

- [Dotkernel Light Git](https://github.com/dotkernel/light)
- [Dotkernel Light demo](https://light.dotkernel.net/)
- [Dotkernel Light documentation](https://docs.dotkernel.org/light-documentation/)
- [PHP Framework Interop Group](https://www.php-fig.org/)
- [PSR-15: HTTP Server Request Handlers](https://www.php-fig.org/psr/psr-15/)
- [Static Analysis – Replacing Psalm with PHPStan](https://www.dotkernel.com/php-development/static-analysis-replacing-psalm-with-phpstan/)
