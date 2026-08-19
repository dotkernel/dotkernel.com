---
title: "Where is the intl PHP extension? Problem solved!"
description: "Common causes of 'missing intl PHP extension' errors and step-by-step fixes for installing or enabling the Intl extension on Linux and Windows PHP setups."
author: "Gabi DJ"
date_published: "2016-12-14"
canonical_url: "https://www.dotkernel.com/php-troubleshooting/where-is-the-intl-php-extension-problem-solved/"
category: "PHP Troubleshooting"
language: "en"
---

# Where is the intl PHP extension? Problem solved!

## TL;DR

Errors like "requires intl PHP extension" or "extension intl is missing" happen because the PHP Intl extension isn't installed or enabled.
This article explains what Intl is used for, why it might be missing depending on whether you have a bundled or unbundled PHP install, and gives step-by-step fixes for both Linux and Windows servers.

## Problem

PHP packages, frameworks, libraries, and scripts might require different PHP extensions.
In this case, the Intl extension is needed to work with Internationalization Functions.
See [What is Internationalization?](https://www.w3.org/International/questions/qa-i18n) for background.
Have you seen any of these error messages?

- Zend InputFilter requires intl PHP extension
- The requested PHP extension intl is missing from your system

This happened because the PHP Intl extension isn't installed or enabled.
Parts of this tutorial can also serve as a guide for installing or enabling other extensions.

## What Is PHP Intl?

> Internationalization extension (further referred to as Intl) is a wrapper for the ICU library, enabling PHP programmers to perform various locale-aware operations including but not limited to formatting, transliteration, encoding conversion, and calendar operations.

Source: [PHP Documentation](https://www.php.net/manual/en/book.intl.php)

> This extension may be installed using the bundled version as of PHP 5.3.0, or as a PECL extension as of PHP 5.2.0. In other words, there are two methods to install the intl extension.

Source: [PHP Documentation](http://php.net/manual/en/intl.installation.php)

## Cause

If you have installed the unbundled PHP version, the extension is not installed on the system, unless you've installed it separately.
If you have the bundled PHP version, the extension might exist but not be enabled.

## Solutions

### For Linux-Based Server (Assuming You Have Root Access)

- Make sure the php_intl.so file exists within your PHP extensions directory.
Find the extensions directory by using `phpinfo()`, or by running: `php -r "echo ini_get('extension_dir');"` — both options get the extension_dir right from the PHP runtime configuration.
- If the file exists:
  - Search for the config file (php.ini, usually /etc/php.ini) and open it.
  - Make sure the line "extension=php_intl.so" exists and is not commented out.
  - Restart the web server (usually `sudo service httpd restart`).
  - Check if the extension is enabled using `phpinfo()`.
- If the file doesn't exist:
  - Check your PHP version by running the `php -v` command.
  - For PHP 5, install the php-intl package using your [package manager](https://en.wikipedia.org/wiki/Package_manager#Front-ends_for_locally_compiled_packages) — most commonly `apt-get install php-intl` (Ubuntu-based) or `yum install php-intl` (CentOS).
  - For PHP 7, install the php7.x-intl package (depending on your PHP version).
  - Repeat the steps for the case in which the file exists.

For projects hosted on a shared hosting platform, you must ask your hosting provider to install/enable the PHP Intl extension.

### For Windows-Based Server

- Make sure the php_intl.dll file exists within your PHP extensions directory:
  - For a separately installed PHP: C:\path\to\php\ext\
  - For XAMPP: C:\path\to\xampp\php\ext
  - Note: your drive letter might be different.
- If the file exists:
  - Search for the config file (php.ini, usually in the same folder as the PHP executable) and open it.
  - Make sure the line "extension=php_intl.dll" exists and is not commented out.
  - Restart the web server (usually Apache).
  - Check if the extension is enabled using `phpinfo()`.
- If the file doesn't exist:
  - Check your PHP version by running the `php -v` command.
  - Download the PHP version that corresponds to yours from the [PHP Downloads Page](http://windows.php.net/download/) (TS/NTS, x86/x64).
To find thread safety for your PHP, run `php -i | findstr "Thread"`.
  - Search for the php_intl.dll file in the ext folder of that downloaded version and copy it into your php\ext folder.
  - Repeat the steps for the case in which the file exists.

Edit: changed php7.0 occurrences with php7.x, as the version may vary.

## FAQ

**Q: What errors indicate the PHP Intl extension is missing?**
A: Typical errors include "Zend InputFilter requires intl PHP extension" and "The requested PHP extension intl is missing from your system." These happen because the PHP Intl extension isn't installed or enabled.

**Q: What is the PHP Intl extension used for?**
A: Intl (Internationalization extension) is a wrapper for the ICU library that lets PHP programmers perform locale-aware operations, including formatting, transliteration, encoding conversion, and calendar operations. It can be installed bundled since PHP 5.3.0, or as a separate PECL extension since PHP 5.2.0.

**Q: How do you find your PHP extensions directory?**
A: You can find the extension_dir either by calling phpinfo() or by running the command php -r "echo ini_get('extension_dir');" — both read the value straight from the PHP runtime configuration.

**Q: How do you enable the Intl extension on a Linux server if the file already exists?**
A: Confirm php_intl.so exists in your extensions directory, open the php.ini config file (usually /etc/php.ini), make sure the line "extension=php_intl.so" exists and isn't commented out, restart the web server (e.g. sudo service httpd restart), and verify with phpinfo().

**Q: What if the php_intl.so or php_intl.dll file doesn't exist at all?**
A: On Linux, check your PHP version with php -v, then install the php-intl package (PHP 5) or php7.x-intl package (PHP 7) via your package manager, e.g. apt-get install php-intl on Ubuntu-based systems or yum install php-intl on CentOS; on shared hosting, ask your provider to install/enable it. On Windows, download the matching PHP version (checking TS/NTS and x86/x64, with thread safety found via php -i | findstr "Thread") from the PHP Downloads page and copy php_intl.dll from its ext folder into your php\ext folder.
