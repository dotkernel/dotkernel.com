---
title: "Handling and Logging errors with dot-errorhandler and dot-log"
description: "A guide to using dotkernel/dot-errorhandler alongside dot-log (or zend-log) to log errors in Zend Expressive applications, covering its two handler types, configuration, and how it was tested."
author: "Gabi DJ"
date_published: "2018-11-27"
canonical_url: "https://www.dotkernel.com/dotkernel/handling-and-logging-errors-with-dot-errorhandler-and-dot-log/"
category: "Dotkernel"
language: "en"
---

# Handling and Logging errors with dot-errorhandler and dot-log

## TL;DR

This article is a follow-up to "Logging with dot-log in Zend Expressive and Dotkernel" and explains how to use `dotkernel/dot-errorhandler` together with `dotkernel/dot-log` or `zendframework/zend-log` to log errors in Zend Expressive applications.
It covers how dot-errorhandler was built, how to configure it, and how it was tested.

## The two error handlers

- **The plain `ErrorHandler`** — a copy of Zend Expressive's `Zend\Stratigility\Middleware\ErrorHandler` (copied because that class is `final`).
- **The logging `LogErrorHandler`** — the same as above, but with added logging support via the container.

Both error handlers have factories registered for easier use with the Container.

Install it with:

```shell
composer require dotkernel/dot-errorhandler
```

## The Config Provider

When the dot-errorhandler config provider is invoked, both error handlers' factories are registered, and an alias is added to switch between them.
As a fallback, the plain error handler is selected by default, and this can be overwritten through the config file.

## Configuration

Important notes:

- This assumes the project already has a configured logger, as described in the "Logging with dot-log in Zend Expressive and Dotkernel" article, with the logger named `default_logger` (as used in the package's config example).
- Although the config key is `dot_log`, the dot-log abstract factory responds to the `dot-log` selector — the container key to ask for is `dot-log.default_logger`.
- dot-errorhandler is meant to be a silent logger for staging and production environments.
To test it, development mode should be disabled, otherwise whoops will catch the errors and show them to the developer instead.
Any custom error handler can be used as long as it implements the provided `ErrorHandlerInterface`.

Steps to configure:

1. Add `Dot\ErrorHandler\ConfigProvider` to the project's `config/config.php` file.
2. Write the error handler config.

To use the logging error handler, this config is needed, in `config/autoload/dot-errorhandler.global.php`:

```php
use Dot\ErrorHandler\ErrorHandlerInterface;
use Dot\ErrorHandler\LogErrorHandler;
```

The `logger` key in the dot-errorhandler config should reflect your logger configuration in `config/autoload/log.global.php`.
An out-of-the-box config was provided within the error handler's config directory for using the default logger.

## Usage / Triggering errors

Errors were triggered in the following ways to test the handlers:

- Throwing Exceptions — the most common case.
- Raising errors, such as triggering warning/error messages, for example:
  - dividing numbers by zero (e.g. `16/0`)
  - casting arrays to strings (e.g. `$string = 'hello' . $array`)

## FAQ

**Q: What two kinds of error handlers does dot-errorhandler provide?**
A: A plain ErrorHandler, which is a copy of Zend Expressive's Zend\Stratigility\Middleware\ErrorHandler (copied because that class is final), and a logging LogErrorHandler, which is the same but with added logging support via the container.
Both have factories registered for easier use with the Container.

**Q: How do you install dot-errorhandler?**
A: Run `composer require dotkernel/dot-errorhandler`.

**Q: Which error handler is used by default?**
A: The plain error handler is selected by default as a fallback, and this can be overwritten through the config file, which also registers factories for both handlers plus an alias to switch between them.

**Q: What must already be in place before configuring the logging error handler?**
A: A configured logger as described in the "Logging with dot-log in Zend Expressive and Dotkernel" article, with the logger named default_logger as used in the package's config example.

**Q: Why might whoops interfere when testing dot-errorhandler?**
A: dot-errorhandler is meant to be a silent logger for staging and production environments.
To actually test it, development mode should be disabled, otherwise whoops will catch the errors and show them to the developer instead.

**Q: How were errors triggered to test the error handlers?**
A: By throwing Exceptions, the most common case, and by raising warnings/errors such as dividing a number by zero (e.g. 16/0) or casting an array to a string.

## Resources

- [Logging with dot-log in Zend Expressive and Dotkernel](https://www.dotkernel.com/dotkernel/logging-with-dot-log-in-zend-expressive-and-dotkernel/)
