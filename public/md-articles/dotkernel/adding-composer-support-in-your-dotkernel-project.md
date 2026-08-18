---
title: "Adding Composer support in your Dotkernel project"
description: "The steps needed to add Composer support to a Dotkernel 1.x project, or 'composify' it."
author: "Gabi DJ"
date_published: "2016-04-04"
canonical_url: "https://www.dotkernel.com/dotkernel/adding-composer-support-in-your-dotkernel-project/"
category: "Dotkernel"
language: "en"
---

# Adding Composer support in your Dotkernel project

## TL;DR
Composer is an application-level package manager that auto-loads dependencies (and custom classes) on demand.
This article covers the steps needed to add a composer.json file to a Dotkernel project, run `composer update`, and safely require the generated autoloader so the project works whether or not Composer is present.

## First things first

The Dotkernel project must have a **composer.json** file so that Composer can work.
It should look like this:

```json
{
  "require" : {
    "zendframework/zendframework1" : "1.12.*",
    "mobiledetect/mobiledetectlib" : "2.8.*"
  },
  "require-dev" : {
    "php" : ">=5.4.0"
  }
}
```

Note: `zendframework/zendframework1` may not be necessary if you already have ZendServer running or the Zend Framework folder within `/usr/share/`.

This file makes sure:

- Zend Framework is present and at version > 1.12.*
- MobileDetect is present and at version > 2.8
- The PHP executable is at least at version > 5.4.0 (only for development, because it is not present in the main `require`)

The dependencies provided in the `require` section are also loaded for development purposes if not provided in `require-dev`.

In order to have these components installed, run the following command in your Dotkernel root path:

```shell
composer update
```

If the `vendor` folder is present, Composer will check for updates and update the packages as needed. If the `vendor` folder does not exist, Composer will create it, containing all the requested packages, along with an autoload file used to load the dependencies/packages.

## Adding Composer Support to Dotkernel

The autoload file created by Composer is used to load the packages:

```php
$composerAutoLoaderPath = realpath(APPLICATION_PATH.'/vendor/autoload.php');
require_once($composerAutoLoaderPath);
```

But what if the file does not exist, or Composer is not present?
First make sure the Composer autoload path exists, and only load the dependencies if the autoload file was found:

```php
$composerAutoLoaderPath = realpath('./vendor/autoload.php');

$composerEnabled = file_exists($composerAutoLoaderPath);

if ($composerEnabled == true) {
    require_once($composerAutoLoaderPath);
} else {
    // handle the error gracefully
    // or load fallbacks - if exist
}
```

The variable `$composerEnabled` will be true only if the Composer path exists, so the application behavior can be controlled if Composer is not present.

Later on, the packages can be used like this:

```php
use VendorName\PackageName\ClassName as MyDependency;

$myDependency = new MyDependency($neededArguments);
$myDependency->doSomething();
```

This article works for any **Dotkernel 1.x** version if your server is running **PHP >5.4.0**.

## FAQ

**Q: What does Composer do?**
A: Composer is an application-level package manager.
It auto-loads dependencies on demand and can also auto-load custom classes.

**Q: What must a Dotkernel project have before Composer can be used?**
A: A composer.json file, for example requiring zendframework/zendframework1 at 1.12.* and mobiledetect/mobiledetectlib at 2.8.*, plus PHP >=5.4.0 listed under require-dev.

**Q: What happens when you run composer update?**
A: If the vendor folder already exists, Composer checks for and applies updates to the packages.
If it doesn't exist, Composer creates the vendor folder containing all requested packages, along with an autoload file.

**Q: How do you safely load the Composer autoloader in case Composer isn't present?**
A: Check whether vendor/autoload.php exists using file_exists() before calling require_once() on it, and handle the case gracefully (for example by loading fallbacks) if the path is missing.

**Q: Which Dotkernel versions does this apply to?**
A: The article states it works for any Dotkernel 1.x version, as long as the server is running PHP greater than 5.4.0.

## Resources

- [Composer install / PHP dependency manager tutorial](https://www.codementor.io/php/tutorial/composer-install-php-dependency-manager)
- Using Dotkernel with Composer dependencies
