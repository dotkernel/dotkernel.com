---
title: "Static Analysis - Replacing Psalm with PHPStan"
description: "Why Dotkernel replaced Psalm with PHPStan for static analysis, and a walkthrough of updating composer.json, CI, and configuration files to run PHPStan checks."
author: "Florin Bidirean"
date_published: "2024-11-22"
canonical_url: "https://www.dotkernel.com/php-development/static-analysis-replacing-psalm-with-phpstan/"
category: "PHP Development"
language: "en"
---

# Static Analysis - Replacing Psalm with PHPStan

## TL;DR

Dotkernel is replacing Psalm with PHPStan for static analysis, following a broader PHP community shift (including projects like Doctrine and Composer) toward PHPStan's faster-growing ecosystem, full-time maintainer, PHPStorm-based stubs, and stronger detection.
This article explains what static analysis is, why the switch makes sense, and walks through updating composer.json, the CI workflow, and the phpstan.neon configuration to run PHPStan checks in place of Psalm.

## What Is Static Analysis

Static analysis (static code analysis or source code analysis) applies a set of coding rules to debug source code before a program is run.
Applied in the early phase of code development, the goals of static analysis are:

- Catch and fix errors like type-related errors which can occur especially in dynamically-typed programming languages like PHP.
- Confirm coding standards to ensure readability and maintainability for large projects that need a consistent coding style.
- Identify code that needs refactoring and recommend improvements to improve complex or 'smelly' code.
- Enhance security by detecting potential code injection in PHP, cross-site scripting (XSS) and open redirect vulnerabilities.

The top static analysis tools are:

- PHPStan
- Psalm
- Snyk
- Sonarqube
- Scrutinizer
- PHPCheckstyle

## Why Switch from Psalm to PHPStan?

Dotkernel has been using [psalm](https://psalm.dev/) for a while now and the results have always been positive.
A large part of the PHP community, especially developers in widely-used projects like Doctrine and Composer, have opted for [PHPStan](https://phpstan.org/) instead.
For most use cases, psalm and PHPStan have identical findings, so it isn't really justified to use both.

PHPStan has some advantages to psalm:

- A faster growing ecosystem.
- Better social media presence.
- A fulltime contributor (author [@ondrejmirtes](https://github.com/ondrejmirtes)).
- Uses PHP stubs from PHPStorm.
- Better quality and depth of detection.

Thus, the better choice becomes PHPStan.

## Updating Your Project to Use PHPStan

First, remove the references to psalm:

- update the `require` in your `composer.json` file
  - remove `vimeo/psalm`
  - add these packages
    - "phpstan/phpstan": "^2.0"
    - "phpstan/phpstan-doctrine": "^2.0"
    - "phpstan/phpstan-phpunit": "^2.0",
- update the `scripts` in your `composer.json` file
  - replace `"static-analysis": "psalm --shepherd --stats",` with `"static-analysis": "phpstan analyse --memory-limit 1G",`
- run `composer update` to install the new packages
- delete `psalm-baseline.xml` and `psalm.xml`
- create file `.github/workflows/static-analysis.yml` with the content below to configure the environment and the steps for PHPStan

```
on:
  - push

name: Run PHPStan checks

jobs:
  mutation:
    name: PHPStan ${{ matrix.php }}-${{ matrix.os }}

    runs-on: ${{ matrix.os }}

    strategy:
      matrix:
        os:
          - ubuntu-latest

        php:
          - "8.2"
          - "8.3"

    steps:
      - name: Checkout
        uses: actions/checkout@v4

      - name: Install PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: "${{ matrix.php }}"
          coverage: pcov
          ini-values: assert.exception=1, zend.assertions=1, error_reporting=-1, log_errors_max_len=0, display_errors=On
          tools: composer:v2, cs2pr

      - name: Determine composer cache directory
        run: echo "COMPOSER_CACHE_DIR=$(composer config cache-dir)" >> $GITHUB_ENV

      - name: Cache dependencies installed with composer
        uses: actions/cache@v4
        with:
          path: ${{ env.COMPOSER_CACHE_DIR }}
          key: php${{ matrix.php }}-composer-${{ hashFiles('**/composer.json') }}
          restore-keys: |
            php${{ matrix.php }}-composer-
      - name: Install dependencies with composer
        run: composer install --prefer-dist --no-interaction --no-progress --optimize-autoloader --ansi

      - name: Setup project
        run: |
          mv config/autoload/local.php.dist config/autoload/local.php
          mv config/autoload/mail.local.php.dist config/autoload/mail.local.php
          mv config/autoload/local.test.php.dist config/autoload/local.test.php
      - name: Run static analysis with PHPStan
        run:  vendor/bin/phpstan analyse
```

- create the file `phpstan.neon` with the content below to configure the extensions, the rule level and the ignore rules

```
includes:
    - vendor/phpstan/phpstan-doctrine/extension.neon
    - vendor/phpstan/phpstan-phpunit/extension.neon
parameters:
    level: 5
    paths:
        - src
        - test
    treatPhpDocTypesAsCertain: false
    ignoreErrors:
        -
            message: '#Call to an undefined method.*setAllowOverride#'
            path: test/Functional/AbstractFunctionalTest.php
```

## Running the PHPStan Checks

To run the checks, use this command:

```bash
composer static-analysis
```

`composer.json` is currently set up to run this command which sets up the memory limit to a higher amount than that from the `php.ini` file in PHP (128M).

```bash
vendor/bin/phpstan analyse --memory-limit 1G
```

If you still get the error below, try increasing the memory limit further, e.g. 2G or 4G.

```
Child process error: PHPStan process crashed because it reached configured PHP memory limit: 128M
```

## Summary

In this article we revisited the theoretical meaning of static analysis and focused on the change from psalm to PHPStan.
It's highly recommended to use a static analysis tool in your project and, while both psalm and PHPStan perform similar functions, the latter has recently stepped ahead of the former.

## FAQ

**Q: What is static analysis and why is it used?**
A: Static analysis (or source code analysis) applies coding rules to debug source code before a program is run. It's used to catch and fix type-related errors, confirm coding standards, identify code that needs refactoring, and enhance security by detecting issues like code injection, XSS, and open redirect vulnerabilities.

**Q: Why did Dotkernel switch from Psalm to PHPStan?**
A: Dotkernel had used Psalm with positive results, but much of the PHP community, including projects like Doctrine and Composer, moved to PHPStan instead. Since the two tools have largely identical findings, using both wasn't justified, and PHPStan has a faster growing ecosystem, better social media presence, a fulltime contributor (@ondrejmirtes), PHP stubs from PHPStorm, and better quality and depth of detection.

**Q: What are the main steps to migrate a project from Psalm to PHPStan?**
A: Remove vimeo/psalm from composer.json and add phpstan/phpstan, phpstan/phpstan-doctrine and phpstan/phpstan-phpunit, replace the "static-analysis" composer script with "phpstan analyse --memory-limit 1G", run composer update, delete psalm-baseline.xml and psalm.xml, then add a .github/workflows/static-analysis.yml workflow and a phpstan.neon configuration file.

**Q: What does the phpstan.neon configuration file do in this setup?**
A: It includes the phpstan-doctrine and phpstan-phpunit extensions, sets the analysis rule level (level 5 in the example), specifies the paths to scan (src and test), sets treatPhpDocTypesAsCertain to false, and can list ignoreErrors patterns to suppress specific known findings.

**Q: How do you run the PHPStan checks, and what if you hit a memory error?**
A: Run composer static-analysis, which is configured to call vendor/bin/phpstan analyse --memory-limit 1G (higher than PHP's default 128M). If you still see "PHPStan process crashed because it reached configured PHP memory limit," try increasing the memory limit further, e.g. to 2G or 4G.
