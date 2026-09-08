---
title: "PHP 8.3 support in Dotkernel API"
description: "Dotkernel API (now v4.2.1) was updated to support PHP 8.2 and PHP 8.3, dropping PHP 8.1, updating dependencies, and replacing the removed PhpFileCache with the new dot-cache package."
author: "Florin Bidirean"
date_published: "2024-04-03"
canonical_url: "https://www.dotkernel.com/dotkernel3/php-8-3-support-in-dotkernel-api/"
category: "Dotkernel 3"
language: "en"
---

# PHP 8.3 support in Dotkernel API

## TL;DR

Dotkernel API, now at v4.2.1, is the last remaining Dotkernel application updated to support PHP 8.3, following the same approach used for the Frontend update.
The update drops PHP 8.1 support, updates a large set of dependencies, removes the `PhpFileCache`-based configuration in favor of the new `dot-cache` package, and requires a small query change (`useQueryCache()` to `setCacheable()`).

## Supported PHP versions

Dotkernel API now supports only PHP 8.2 and PHP 8.3.
Support for PHP 8.1 was removed, and the project's CI workflows were updated accordingly.

## Updated dependencies

### require

- `dotkernel/dot-annotated-services`: ^4.1.7
- `dotkernel/dot-cli`: ^3.5.0
- `dotkernel/dot-data-fixtures`: ^1.1.3
- `dotkernel/dot-doctrine-metadata`: ^3.2.2
- `dotkernel/dot-errorhandler`: ^3.3.2
- `dotkernel/dot-mail`: ^4.1.1
- `dotkernel/dot-response-header`: ^3.2.3
- `laminas/laminas-component-installer`: ^3.4.0
- `laminas/laminas-config`: ^3.9.0
- `laminas/laminas-config-aggregator`: ^1.14.0
- `laminas/laminas-http`: ^2.19.0
- `laminas/laminas-hydrator`: ^4.15.0
- `laminas/laminas-inputfilter`: ^2.29.0
- `laminas/laminas-paginator`: ^2.18.0
- `laminas/laminas-stdlib`: ^3.19.0
- `laminas/laminas-text`: ^2.11.0
- `mezzio/mezzio`: ^3.19.0
- `mezzio/mezzio-authentication-oauth2`: ^2.8.0
- `mezzio/mezzio-authorization-acl`: ^1.10.0
- `mezzio/mezzio-authorization-rbac`: ^1.7.0
- `mezzio/mezzio-cors`: ^1.11.1
- `mezzio/mezzio-fastroute`: ^3.11.0
- `mezzio/mezzio-problem-details`: ^1.13.1
- `mezzio/mezzio-twigrenderer`: ^2.15.0
- `ramsey/uuid-doctrine`: ^2.0.0
- `roave/psr-container-doctrine`: ^4.1.0
- `symfony/filesystem`: ^7.0.3

### require-dev

- `laminas/laminas-coding-standard`: ^2.5
- `laminas/laminas-development-mode`: ^3.12.0
- `mezzio/mezzio-tooling`: ^2.9.0
- `phpunit/phpunit`: ^10.5.10
- `roave/security-advisories`: dev-latest
- `vimeo/psalm`: ^5.22.0

## Caching changes

The `PhpFileCache` class (previously required for cache configuration) and its related parameters were removed from `config/autoload/doctrine.global.php`, including the `use Doctrine\Common\Cache\PhpFileCache` import and the cache-related configuration parameters.

In its place, the `dot-cache` package was added, with corresponding updates made to `composer.json`, `config/autoload/doctrine.global.php`, and `config/config.php`.

## Upgrade notes

- When upgrading from an older version of Dotkernel API (before PHP 8.3 support), you may need to run `composer update` and/or install Sodium, for example via `sudo dnf install php-sodium.x86_64`.
- Update your queries: replace `useQueryCache()` with `setCacheable()` wherever it is used.

## FAQ

**Q: What PHP versions does Dotkernel API support after this update?**
A: Dotkernel API, now at v4.2.1, supports only PHP 8.2 and PHP 8.3.
Support for PHP 8.1 was removed, including from the project's workflows.

**Q: What changed with caching in Dotkernel API?**
A: The PhpFileCache class and its related parameters were removed from config/autoload/doctrine.global.php, which previously required it for cache configuration.
The dot-cache package was added instead, with corresponding updates to composer.json, config/autoload/doctrine.global.php, and config/config.php.

**Q: Do I need to update my Doctrine queries after this update?**
A: Yes.
Wherever useQueryCache() is used in your queries, it should be replaced with setCacheable().

**Q: What should I do when upgrading an older Dotkernel API installation to this version?**
A: When upgrading from a version of Dotkernel API older than the PHP 8.3 support, you may need to run composer update and/or install Sodium, for example by running sudo dnf install php-sodium.x86_64.

**Q: Where can I find the full list of changes for this update?**
A: The full list of file changes is available in the linked pull request on GitHub for the dotkernel/api repository, and the steps taken mirror those used for the PHP 8.3 update of the Dotkernel Frontend.

## Resources

- [Dotkernel API on GitHub](https://github.com/dotkernel/api)
- [Dotkernel Frontend on GitHub](https://github.com/dotkernel/frontend)
- [Full list of file changes (PR #222)](https://github.com/dotkernel/api/pull/222)
