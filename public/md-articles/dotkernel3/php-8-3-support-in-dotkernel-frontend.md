---
title: "PHP 8.3 support in Dotkernel Frontend"
description: "The Dotkernel team updated the Frontend application to version 4.2.0 to support PHP 8.3, dropping PHP 8.1 and the PhpFileCache class along the way."
author: "Florin Bidirean"
date_published: "2024-02-14"
canonical_url: "https://www.dotkernel.com/dotkernel3/php-8-3-support-in-dotkernel-frontend/"
category: "Dotkernel 3"
language: "en"
---

# PHP 8.3 support in Dotkernel Frontend

## TL;DR
To take advantage of PHP 8.3 support in the newest packages, the Dotkernel team updated the [Frontend](https://github.com/dotkernel/frontend) application to version 4.2.0.
As with the earlier Admin update, this required dropping support for PHP 8.1 and for the no-longer-available `PhpFileCache` class, until a replacement is implemented.

To be able to take advantage of the support for **PHP 8.3** in the newest packages, the Dotkernel team has updated the [Frontend](https://github.com/dotkernel/frontend) Application to version **4.2.0**. Very much like for the Admin update before it, the Frontend must also remove support for PHP 8.1 and the no-longer-available PhpFileCache class until a replacement is implemented.

The full list of file changes is [here](https://github.com/dotkernel/frontend/pull/417).

- **Supports only PHP 8.2 and PHP 8.3**
  - Removed support for PHP 8.1
  - Updated workflows - removed PHP 8.1
  - Commit [#1](https://github.com/dotkernel/frontend/commit/e6c3e6495d50e181cfe4ff6a4edec7af12120abf)
- **Updated** dependencies
  - Commit [#1](https://github.com/dotkernel/frontend/commit/e6c3e6495d50e181cfe4ff6a4edec7af12120abf)
  - **require**
    - "dotkernel/dot-annotated-services": "^4.1.6",
    - "dotkernel/dot-authorization": "^3.4.1",
    - "dotkernel/dot-controller": "^3.4.3",
    - "dotkernel/dot-data-fixtures": "^1.1.3",
    - "dotkernel/dot-debugbar": "^1.1.5",
    - "dotkernel/dot-errorhandler": "^3.3.2",
    - "dotkernel/dot-flashmessenger": "^3.4.2",
    - "dotkernel/dot-mail": "~3.4 || ^4.1.1",
    - "dotkernel/dot-navigation": "^3.4.2",
    - "dotkernel/dot-rbac-guard": "^3.4.3",
    - "dotkernel/dot-response-header": "^3.2.3",
    - "dotkernel/dot-session": "^5.4.2",
    - "dotkernel/dot-twigrenderer": "^3.4.3",
    - "friendsofphp/proxy-manager-lts": "^1.0.16",
    - "laminas/laminas-component-installer": "^3.4.0",
    - "laminas/laminas-config-aggregator": "^1.14.0",
    - "laminas/laminas-form": "^3.19.1",
    - "laminas/laminas-i18n": "^2.26.0",
    - "mezzio/mezzio": "^3.18.0",
    - "mezzio/mezzio-authorization-rbac": "^1.7.0",
    - "mezzio/mezzio-cors": "^1.11.1",
    - "mezzio/mezzio-fastroute": "^3.11.0",
    - "ramsey/uuid-doctrine": "^2.0.0",
    - "roave/psr-container-doctrine": "^4.1.0"
  - **require-dev**
    - "filp/whoops": "^2.15.4",
    - "laminas/laminas-development-mode": "^3.12.0",
    - "mezzio/mezzio-tooling": "^2.9.0",
    - "phpunit/phpunit": "^10.5",
    - "rector/rector": "^1.0.0",
    - "vimeo/psalm": "^5.21.1"
- **Updated** npm dependencies in *packagist.json*
  - Recommended to use npm v10.0.4 and Node.js v20.11.0
  - Commit [#1](https://github.com/dotkernel/frontend/commit/d4d0309c95ba7a572afc82965fdf0e0904a4e0c9) and [#2](https://github.com/dotkernel/frontend/commit/99d8a1fcbebf5d124c9f96cd948b9e28732cfec8)
- **Removed** parameter from configuration files and uses for class *PhpFileCache* (required by cache)
  - Updated *doctrine.global.php*
    - Removed *use Doctrine\Common\Cache\PhpFileCache;*
    - Removed the parameters used by cache *configuration* and *cache*
  - Commit [#1](https://github.com/dotkernel/frontend/commit/e6c3e6495d50e181cfe4ff6a4edec7af12120abf)
  - Make sure to keep the lines from the more recent commit [#2](https://github.com/dotkernel/frontend/commit/f693750602b8c0e5d6582826604ef65a4f0f6b02)
- **Fixed** psalm and phpcs issues
  - Updated type hints and indentations
  - Commits [#1](https://github.com/dotkernel/frontend/commit/2a3e831208aa342220077ee2fd497b3ac4cc823f) and [#2](https://github.com/dotkernel/frontend/commit/a24a3426725417713109f8318bd9b0d53c250945)

Next on the menu is to implement these changes in the Dotkernel Frontend live projects and process the feedback.

## FAQ

**Q: What update adds PHP 8.3 support to Dotkernel Frontend?**
A: The Dotkernel team updated the Frontend application to version 4.2.0 so it can take advantage of PHP 8.3 support in the newest packages.

**Q: Which PHP versions does Dotkernel Frontend 4.2.0 support?**
A: Version 4.2.0 supports only PHP 8.2 and PHP 8.3. Support for PHP 8.1 was removed, similar to the earlier Admin update.

**Q: What changed with caching in this update?**
A: The no-longer-available PhpFileCache class is no longer supported until a replacement is implemented. The related parameters were removed from configuration files, including removing the use Doctrine\Common\Cache\PhpFileCache; statement and the configuration and cache parameters in doctrine.global.php.

**Q: What npm and Node.js versions are recommended for this update?**
A: Alongside updated npm dependencies in packagist.json, it is recommended to use npm v10.0.4 and Node.js v20.11.0.

**Q: Where can I find the full list of file changes for this update?**
A: The full list of file changes is linked in the article, pointing to the corresponding pull request on the Dotkernel Frontend GitHub repository.
