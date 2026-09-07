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

This article is a follow-up for: **[Logging with dot-log in Zend Expressive and Dotkernel](https://www.dotkernel.com/dotkernel/logging-with-dot-log-in-zend-expressive-and-dotkernel/)**, the mentioned article is a guide to using dot-log.

 

This article explains the usage of **dotkernel/dot-errorhandler** with **dotkernel/dot-log** or **zendframework/zend-log** to **log errors** in Zend Expressive applications.

This can be considered as a guide on how the **dot-errorhandler** was made and how it's meant to be used.

 

As a first note Dot Error Handler provides two kinds of error handlers:

- the plain `ErrorHandler` - this class is a copy of Zend Expressive's error handler `Zend\Stratigility\Middleware\ErrorHandler` (as it is final)
- the logging `LogErrorHandler` - this class is like the above one, but with added Logging support (via container)

Both error handlers have factories for an easier usage with the **Container**.

 

To use dot-error handler in your project run the following command: `composer require dotkernel/dot-errorhandler`.

 

## The Config Provider

When the dot-errorhandler config provider is invoked the following configuration is returned.

```
,
        'factories' =>
    ],
];
```

Both the error handlers have the factories registered, and an alias to switch between them is added.

As a fallback case the plain error handler is selected by default and can be overwritten through the config file.

 

## Configuration

**IMPORTANT NOTES:**

- Assuming the project in hand has a configured logger as per **[this article](https://www.dotkernel.com/dotkernel/logging-with-dot-log-in-zend-expressive-and-dotkernel/)** and the logger name is **default_logger** (as provided in the package's config example).
- Although the key is `dot_log`, when selecting a logger the dot log abstract factory responds to the `dot-log` selector
  - to select the container key asked for is `dot-log.default_logger`
- The dot-errorhandler was meant as a silent logger for staging and production environments doesn't block whoops
  - to test it the development mode should be **disabled**, otherwise whoops will catch the errors and show them to the developer
  - you can use any custom implemented error handler as long as it implements the provided `ErrorHandlerInterface`

The steps to configuring are the following:

- add the `Dot\ErrorHandler\ConfigProvider` in the project's `config/config.php` file
- write the error handler config

 

To use the logging error handler the following config must be used.

 

`config/autoload/dot-errorhandler.global.php`

```
use Dot\ErrorHandler\ErrorHandlerInterface;
use Dot\ErrorHandler\LogErrorHandler;

return
    ],
    'dot-errorhandler' =>
];
```

The **logger** key in dot-error handler should reflect your logger configuration in `config/autoload/log.global.php`

To use the default logger an out-of-the-box config was provided within the error handler's config directory.

## Usage / Triggering errors

The tests we have made were the following:

- throwing Exceptions - the most common
- raising errors such as triggering warning/error messages:
  - by dividing numbers to zero (eg.: **16/0**)
  - by casting arrays to strings (**$string = 'hello' . ['world']['dot_log']['world']**)

 

## FAQ

**Q: What two kinds of error handlers does dot-errorhandler provide?**
A: A plain ErrorHandler, which is a copy of Zend Expressive's Zend\Stratigility\Middleware\ErrorHandler (copied because that class is final), and a logging LogErrorHandler, which is the same but with added logging support via the container. Both have factories registered for easier use with the Container.

**Q: How do you install dot-errorhandler?**
A: Run composer require dotkernel/dot-errorhandler.

**Q: Which error handler is used by default?**
A: The plain error handler is selected by default as a fallback, and this can be overwritten through the config file, which also registers factories for both handlers plus an alias to switch between them.

**Q: What must already be in place before configuring the logging error handler?**
A: A configured logger as described in the "Logging with dot-log in Zend Expressive and Dotkernel" article, with the logger named default_logger as used in the package's config example.

**Q: Why might whoops interfere when testing dot-errorhandler?**
A: dot-errorhandler is meant to be a silent logger for staging and production environments. To actually test it, development mode should be disabled, otherwise whoops will catch the errors and show them to the developer instead.

**Q: How were errors triggered to test the error handlers?**
A: By throwing Exceptions, the most common case, and by raising warnings/errors such as dividing a number by zero (e.g. 16/0) or casting an array to a string.
