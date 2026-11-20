---
title: "Fixing the Doctrine ORM Second Level Cache Error"
description: "Doctrine ORM 3.8 requires an extra package before the second level cache can be enabled. Why Dotkernel API and Admin fail with a ServiceNotCreatedException after updating, and how to disable the cache or opt back in."
author: "Florin Bidirean"
date_published: "2026-11-16"
canonical_url: "https://www.dotkernel.com/how-to/fixing-the-doctrine-orm-second-level-cache-error/"
category: "How to's"
language: "en"
---

# Fixing the Doctrine ORM Second Level Cache Error

## TL;DR

In the 3.8.0 release of Doctrine ORM, the second level cache requires the installation of the extra package `doctrine/orm-plugin-second-level-cache`.
Dotkernel API and Admin enable the second level cache by default, and ORM 3.8 is pulled in automatically as a transitive dependency, so a fresh install or a `composer update` fails as soon as the entity manager is created.
Neither application actually caches anything through the second level cache, so the fix is to disable it with a new `second_level_cache.enabled` override in `config/autoload/local.php`.
To keep using it, install the plugin and set `enabled` to `true`.

## The Error

After installing or updating a Dotkernel API or Dotkernel Admin application, the first request ends with:

```text
Laminas\ServiceManager\Exception\ServiceNotCreatedException: Service with name "doctrine.entity_manager.orm_default" could not be created.
Reason: Using the second level cache requires installing an additional package. Install with composer require doctrine/orm-plugin-second-level-cache
```

Neither the application code nor its configuration has changed.
The cause is a dependency update.

## What Changed in Doctrine ORM 3.8

Doctrine ORM 3.8.0 was released on November 14, 2026.
Its release notes list a backward-compatibility break, [PR #12752](https://github.com/doctrine/orm/pull/12752): the second level cache (SLC) now requires the [`doctrine/orm-plugin-second-level-cache`](https://github.com/doctrine/orm-plugin-second-level-cache) package.

The second level cache was not removed.
All of its code still ships with the ORM.
What changed is a single check in `Doctrine\ORM\Configuration::setSecondLevelCacheEnabled()`: when the flag is `true` and the plugin is not installed, the method throws a `LogicException`.
The [3.8 upgrade notes](https://github.com/doctrine/orm/blob/3.8.x/UPGRADE.md) describe exactly this behavior.

The plugin adds no functionality of its own.
According to its README, it exists to give the Doctrine team telemetry on how many projects use the second level cache, and the SLC code may move into it later.
The feature has been around since ORM 2.5 (2015), and the [Doctrine documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/stable/reference/second-level-cache.html) still marks it as experimental, so counting installs of a required package is a straightforward way to measure whether it is worth maintaining.

The ORM lists the plugin only under `suggest` (and under `require-dev`, for its own test suite).
Composer does not install it, and nothing fails at install time - the problem surfaces only at runtime, when the entity manager is built.

## Why Dotkernel Applications Are Affected

### The Second Level Cache Was Enabled by Default

Dotkernel builds the entity manager with the [`roave/psr-container-doctrine`](https://github.com/Roave/psr-container-doctrine) factories, which keep the second level cache disabled by default.
Dotkernel's Core module overrides that default in `src/Core/src/App/src/ConfigProvider.php` ([API](https://github.com/dotkernel/api/blob/7.0/src/Core/src/App/src/ConfigProvider.php#L162), [Admin](https://github.com/dotkernel/admin/blob/7.0/src/Core/src/App/src/ConfigProvider.php#L161)):

```php
'second_level_cache' => [
    'enabled'                    => true,
    'default_lifetime'           => 3600,
    'default_lock_lifetime'      => 60,
    'file_lock_region_directory' => '',
    'regions'                    => [],
],
```

When `enabled` is `true` and a result cache is configured (Dotkernel uses the `filesystem` cache), the factory calls `setSecondLevelCacheEnabled(true)`.
On ORM 3.8 without the plugin, that call throws.

### ORM 3.8 Arrives as a Transitive Dependency

Neither application lists `doctrine/orm` in its `composer.json`.
Running `composer why doctrine/orm` on a fresh Dotkernel API install shows where it comes from:

```text
doctrine/data-fixtures             2.3.0       conflicts doctrine/orm (<3.7 || >=4)
doctrine/migrations                3.9.8       conflicts doctrine/orm (<2.12 || >=4)
dotkernel/dot-data-fixtures        1.5.0       requires  doctrine/orm (^3.0)
dotkernel/dot-dependency-injection 1.4.1       requires  doctrine/orm (^2.9 || ^3.0)
phpstan/phpstan-doctrine           2.1.2       conflicts doctrine/orm (<2.5)
roave/psr-container-doctrine       6.3.0       requires  doctrine/orm (^3.6.2)
roave/security-advisories          9999999-dev conflicts doctrine/orm (>=1,<1.2.4|>=2,<2.4.8|>=2.5,<2.5.1|>=2.8.3,<2.8.4)
```

Every constraint accepts any 3.x release from 3.7 upward, so Composer resolves the newest one.
`composer show doctrine/orm` confirms it:

```text
name     : doctrine/orm
versions : * 3.8.0
released : 2026-11-14, this week
...
suggests
doctrine/orm-plugin-second-level-cache Second level cache functionality needs extra package.
```

A minor release of a package that is never required directly, combined with a default that enables the now-gated feature, is enough to break the entity manager.

## Does the Application Need the Second Level Cache?

For the Dotkernel skeletons, no.
The second level cache only stores data for entities, associations, and queries that opt in - with `#[ORM\Cache]` on an entity or `setCacheable(true)` on a query.
Neither Dotkernel API nor Dotkernel Admin does either, so the cache was enabled but had nothing to store.

The repositories do call `$queryBuilder->getQuery()->useQueryCache(true)`, but that is the query cache, which stores the DQL-to-SQL translation.
It is a separate mechanism, it does not depend on the plugin, and it is not affected by this change.
The same applies to the metadata, result, and hydration caches.

Disabling the second level cache therefore has no functional impact on a stock Dotkernel application.

## Step 1: Configure the Second Level Cache in `local.php`

Both skeletons have been updated so the second level cache is controlled from `local.php`:

- Dotkernel API: [PR #584](https://github.com/dotkernel/api/pull/584)
- Dotkernel Admin: [PR #482](https://github.com/dotkernel/admin/pull/482)

The change adds a `doctrine.configuration.orm_default.second_level_cache` block to `config/autoload/local.php.dist` that sets `enabled` to `false` and overrides the Core setting:

```php
<?php

declare(strict_types=1);

$baseUrl = 'http://localhost:8080';

$databases = [
    // ...
];

return [
    // ...
    'doctrine' => [
        'connection'    => [
            'orm_default' => [
                'params' => $databases['mariadb'],
            ],
        ],
        'configuration' => [
            'orm_default' => [
                /**
                 * Starting with doctrine/orm 3.8, the second-level cache requires
                 * the doctrine/orm-plugin-second-level-cache package.
                 * To enable it, run `composer require doctrine/orm-plugin-second-level-cache`
                 * and set 'enabled' to true.
                 */
                'second_level_cache' => [
                    'enabled' => false,
                ],
            ],
        ],
    ],
    // ...
];
```

Overriding a single key is enough because of how `config/config.php` builds the configuration.
`Laminas\ConfigAggregator` loads all module `ConfigProvider` classes, including `Core\App\ConfigProvider`, first, and the files in `config/autoload/`, such as `local.php`, afterward.
Arrays are merged recursively, so `local.php` replaces only `enabled`; the other second level cache settings from Core stay in place, ready for when the cache is turned back on.

## Step 2: Update Existing Installations

`config/autoload/local.php` is ignored by Version Control - each installation creates it by copying `local.php.dist`.
Pulling the updated `.dist` file does not fix an existing installation, so add the `doctrine.configuration` block shown above to the existing `local.php` by hand.

If configuration caching is enabled, clear the cached file so the new value is picked up:

```bash
rm -f data/cache/config-cache.php
```

## Opting Back In to the Second Level Cache

Projects that rely on the second level cache, or plan to, need two changes.
Install the plugin:

```bash
composer require doctrine/orm-plugin-second-level-cache
```

Then enable the cache in `config/autoload/local.php`:

```php
'second_level_cache' => [
    'enabled' => true,
],
```

Enabling the cache is only the first step.
To benefit from it, entities, associations, or queries must be marked as cacheable, each with a caching mode (`READ_ONLY`, `NONSTRICT_READ_WRITE`, or `READ_WRITE`), and regions and lifetimes must be tuned to the data.
The [Doctrine second level cache reference](https://www.doctrine-project.org/projects/doctrine-orm/en/stable/reference/second-level-cache.html) covers the details - keeping in mind that Doctrine still considers the feature experimental.

More details are available in the Dotkernel documentation:

- [Dotkernel API: Doctrine ORM](https://docs.dotkernel.org/api-documentation/v7/installation/doctrine-orm/)
- [Dotkernel Admin: Doctrine ORM](https://docs.dotkernel.org/admin-documentation/v7/installation/doctrine-orm/)

## Resources

- [Doctrine ORM 3.8.0 release](https://github.com/doctrine/orm/releases/tag/3.8.0) - the release notes listing the BC break
- [Doctrine ORM PR #12752](https://github.com/doctrine/orm/pull/12752) - the change that added the plugin requirement
- [Doctrine ORM UPGRADE.md](https://github.com/doctrine/orm/blob/3.8.x/UPGRADE.md) - the official upgrade notes for 3.8
- [doctrine/orm-plugin-second-level-cache](https://github.com/doctrine/orm-plugin-second-level-cache) - the plugin repository ([Packagist](https://packagist.org/packages/doctrine/orm-plugin-second-level-cache))
- [Doctrine ORM: The Second Level Cache](https://www.doctrine-project.org/projects/doctrine-orm/en/stable/reference/second-level-cache.html) - the reference documentation for configuring the cache
- [roave/psr-container-doctrine](https://github.com/Roave/psr-container-doctrine) - the factories Dotkernel uses to build the entity manager

## FAQ

**Q: Did Doctrine ORM 3.8 remove the second level cache?**
A: No.
The code still ships with the ORM; enabling it now requires the `doctrine/orm-plugin-second-level-cache` package, and `Configuration::setSecondLevelCacheEnabled(true)` throws a `LogicException` without it.

**Q: Why does the error appear when doctrine/orm is not in composer.json?**
A: The ORM is a transitive dependency of packages such as `roave/psr-container-doctrine` and `dotkernel/dot-data-fixtures`, whose constraints allow any 3.x release, so `composer update` or a fresh install resolves 3.8.0.

**Q: Does disabling the second level cache affect a Dotkernel application?**
A: Not for the stock API and Admin skeletons.
No entity or query is marked as cacheable, so the cache had nothing to store.
The query, metadata, result, and hydration caches keep working.

**Q: Is useQueryCache(true) affected by this change?**
A: No.
The query cache stores the DQL-to-SQL translation and is independent of the second level cache and of the plugin.

**Q: Why doesn't updating local.php.dist fix an existing project?**
A: `local.php` is a Git-ignored copy created at install time.
The new `second_level_cache` block must be added to the existing `local.php` by hand, and the config cache cleared if it is enabled.

**Q: How do I keep using the second level cache?**
A: Run `composer require doctrine/orm-plugin-second-level-cache` and set `second_level_cache.enabled` to `true` in `config/autoload/local.php`.
