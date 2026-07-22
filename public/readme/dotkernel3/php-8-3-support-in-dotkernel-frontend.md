---
title: "PHP 8.3 support in Dotkernel Frontend"
description: "The DotKernel team updated the Frontend application to version 4.2.0 to support PHP 8.3, dropping PHP 8.1 and the PhpFileCache class along the way."
author: "Florin Bidirean"
date_published: "2024-02-14"
canonical_url: "https://www.dotkernel.com/dotkernel3/php-8-3-support-in-dotkernel-frontend/"
category: "Dotkernel 3"
language: "en"
---

# PHP 8.3 support in Dotkernel Frontend

## TL;DR

To take advantage of PHP 8.3 support in the newest packages, the DotKernel team updated the [Frontend](https://github.com/dotkernel/frontend) application to version 4.2.0. As with the earlier Admin update, this required dropping support for PHP 8.1 and for the no-longer-available `PhpFileCache` class, until a replacement is implemented.

## What changed

- **Supports only PHP 8.2 and PHP 8.3**
  - Removed support for PHP 8.1
  - Updated CI workflows to remove PHP 8.1
- **Updated dependencies** in `composer.json` (`require` and `require-dev`), including packages such as `dotkernel/dot-annotated-services`, `dotkernel/dot-controller`, `dotkernel/dot-mail`, `mezzio/mezzio`, `phpunit/phpunit`, `rector/rector`, and `vimeo/psalm`.
- **Updated npm dependencies** in `packagist.json` - recommended to use npm v10.0.4 and Node.js v20.11.0.
- **Removed `PhpFileCache` usage** from configuration files (required due to the class no longer being available):
  - Updated `doctrine.global.php`
  - Removed the `use Doctrine\Common\Cache\PhpFileCache;` statement
  - Removed the parameters used by the `configuration` and `cache` cache settings
- **Fixed Psalm and PHPCS issues** - updated type hints and indentation.

Next on the roadmap: implementing these changes in the DotKernel Frontend live projects and processing the feedback.

## FAQ

**Q: What update adds PHP 8.3 support to Dotkernel Frontend?**
A: The DotKernel team updated the Frontend application to version 4.2.0 so it can take advantage of PHP 8.3 support in the newest packages.

**Q: Which PHP versions does Dotkernel Frontend 4.2.0 support?**
A: Version 4.2.0 supports only PHP 8.2 and PHP 8.3. Support for PHP 8.1 was removed, similar to the earlier Admin update.

**Q: What changed with caching in this update?**
A: The no-longer-available PhpFileCache class is no longer supported until a replacement is implemented. The related parameters were removed from configuration files, including removing the `use Doctrine\Common\Cache\PhpFileCache;` statement and the configuration and cache parameters in `doctrine.global.php`.

**Q: What npm and Node.js versions are recommended for this update?**
A: Alongside updated npm dependencies in packagist.json, it is recommended to use npm v10.0.4 and Node.js v20.11.0.

**Q: Where can I find the full list of file changes for this update?**
A: The full list of file changes is linked in the article, pointing to the corresponding pull request on the Dotkernel Frontend GitHub repository.

## Resources

- [Dotkernel Frontend on GitHub](https://github.com/dotkernel/frontend)
- [Full list of file changes (pull request)](https://github.com/dotkernel/frontend/pull/417)
