---
title: "PHP 8.3 support in Dotkernel Admin"
description: "A rundown of the backward incompatibilities and dependency updates involved in adding PHP 8.3 support to Dotkernel Admin, released in version 4.3.1."
author: "Florin Bidirean"
date_published: "2024-02-06"
canonical_url: "https://www.dotkernel.com/dotkernel3/php-8-3-support-in-dotkernel-admin/"
category: "Dotkernel 3"
language: "en"
---

# PHP 8.3 support in Dotkernel Admin

## TL;DR

Dotkernel Admin added PHP 8.3 support in release 4.3.1, dropping PHP 8.1 and now supporting only PHP 8.2 and PHP 8.3.
The update brought numerous dependency bumps across dotkernel/*, laminas/*, and mezzio/* packages, removed PhpFileCache-related cache configuration because doctrine/cache dropped its implementation classes, and removed doctrine/doctrine-module due to a conflict, which may affect packages that depended on it.
The `AdminService::logAdminVisit` method was also updated to no longer return `AddressNotFoundException`.

With the release of PHP 8.3, the DotKernel team has been working on updating the dependencies in our packages.
Eventually, this allowed us to update our applications as well, starting with the [Admin](https://github.com/dotkernel/admin), in release 4.3.1.
There were some issues and backward incompatibilities that we will list below.
The full list of file changes is [here](https://github.com/dotkernel/admin/pull/218/files).

- Supports only PHP 8.2 and PHP 8.3
  - Removed support for PHP 8.1
  - Updated workflows - removed PHP 8.1
  - Commits [#1](https://github.com/dotkernel/admin/commit/12d46e671ea430d93e4f4a38ba10e74d375ca944) and [#2](https://github.com/dotkernel/admin/commit/5233c9dabe5264553956df29a66ddfd5808a283b)
- Updated dependencies
  - require
    - `"dotkernel/dot-cli": "^3.4.2"`
    - `"dotkernel/dot-controller": "^3.4.3"`
    - `"dotkernel/dot-data-fixtures": "^1.1.3"`
    - `"dotkernel/dot-errorhandler": "^3.3.2"`
    - `"dotkernel/dot-flashmessenger": "^3.4.2"`
    - `"dotkernel/dot-geoip": "^3.5.3"`
    - `"dotkernel/dot-helpers": "^3.4.2"`
    - `"dotkernel/dot-mail": "^4.1.1"`
    - `"dotkernel/dot-navigation": "^3.4.2"`
    - `"dotkernel/dot-rbac-guard": "^3.4.2"`
    - `"dotkernel/dot-session": "^5.4.2"`
    - `"dotkernel/dot-twigrenderer": "3.4.3"`
    - `"dotkernel/dot-user-agent-sniffer": "^3.3.3"`
    - `"friendsofphp/proxy-manager-lts": "^1.0.16"`
    - `"laminas/laminas-component-installer": "^3.4.0"`
    - `"laminas/laminas-config-aggregator": "^1.14.0"`
    - `"laminas/laminas-i18n": "^2.26.0"`
    - `"laminas/laminas-math": "^3.7.0"`
    - `"mezzio/mezzio": "^3.18.0"`
    - `"mezzio/mezzio-authorization-rbac": "^1.7.0"`
    - `"mezzio/mezzio-cors": "^1.11.1"`
    - `"mezzio/mezzio-fastroute": "^3.11.0"`
    - `"ramsey/uuid-doctrine": "^2.0.0"`
    - `"roave/psr-container-doctrine": "^4.1.0"`
  - require-dev
    - `"filp/whoops": "^2.15.4"`
    - `"laminas/laminas-development-mode": "^3.12.0"`
    - `"laminas/laminas-http": "^2.19.0"`
    - `"mezzio/mezzio-tooling": "^2.9.0"`
    - `"phpunit/phpunit": "^10.5.9"`
    - `"vimeo/psalm": "^5.20.0"`
  - Commit [#1](https://github.com/dotkernel/admin/commit/a4cfb4f8873e18e100ed089a7ccfed0b3e8603fc)
- Added `"laminas/laminas-http": "^2.19.0"` to composer.json require-dev (required for tests)
  - Commit [#1](https://github.com/dotkernel/admin/commit/b47119e7cbd1c96648cac2d1781fb227f3c12cc9)
- Updated `src/Admin/src/Service/AdminService.php`, function `logAdminVisit`
  - Note: No longer returns `AddressNotFoundException`
  - Commit [#1](https://github.com/dotkernel/admin/commit/b47119e7cbd1c96648cac2d1781fb227f3c12cc9)
- Removed parameter from configuration files and uses for class `PhpFileCache` (required by cache)
  - Removed `use Doctrine\Common\Cache\PhpFileCache;` from local.php.dist (local.php) and doctrine.global.php
  - Removed the parameters used by cache configuration from local.php.dist (local.php) and cache from doctrine.global.php
  - Commits [#1](https://github.com/dotkernel/admin/commit/73c79dd707e8575adec013ae1cd85f87c01787f1) and [#2](https://github.com/dotkernel/admin/commit/6585a39c0077763e55161b8749f19ac2a846f5df)
- Updated `dotkernel/dot-twigrenderer` dependencies
  - Removed `doctrine/doctrine-module` because of conflict
  - Other packages that don't get installed because of removing `doctrine/doctrine-module` - make sure to check if their functionality is used in your project
    - `doctrine/doctrine-laminas-hydrator`
    - `laminas/laminas-cache-storage-adapter-filesystem`
    - `laminas/laminas-cache-storage-adapter-memory`
    - `laminas/laminas-mvc`
    - `laminas/laminas-paginator`

Updating to PHP 8.3 has not been as streamlined as in previous updates, but the advantages of doing so outweigh the extra work on the custom code to remove potential incompatibilities.
The DotKernel team is focused on the cache that has been removed in the current version because `doctrine/cache` deleted their cache implementation classes.
We are also updating our existing projects to PHP 8.3 and testing the changes on live environments to iron out any other issues.

## FAQ

**Q: Which Dotkernel Admin release added PHP 8.3 support?**
A: Release 4.3.1.

**Q: Which PHP versions does this release support?**
A: Only PHP 8.2 and PHP 8.3 - support for PHP 8.1 was removed, including from the workflows.

**Q: Did the logAdminVisit function change?**
A: Yes. src/Admin/src/Service/AdminService.php's logAdminVisit function was updated and no longer returns AddressNotFoundException.

**Q: Why were cache-related parameters removed from the configuration files?**
A: The PhpFileCache class and its related parameters were removed from local.php.dist (local.php) and doctrine.global.php because doctrine/cache deleted their cache implementation classes.

**Q: Are there side effects from updating the dot-twigrenderer dependencies?**
A: Yes. doctrine/doctrine-module was removed because of a conflict, which means other packages that depended on it - such as doctrine/doctrine-laminas-hydrator, laminas-cache-storage-adapter-filesystem, laminas-cache-storage-adapter-memory, laminas-mvc and laminas-paginator - may no longer get installed, so you should check whether your project actually relies on them.
