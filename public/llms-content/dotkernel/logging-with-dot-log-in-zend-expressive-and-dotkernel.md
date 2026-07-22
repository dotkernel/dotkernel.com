---
title: "Logging with dot-log in Zend Expressive and DotKernel"
description: "How to wire up, configure and use the dot-log component (compatible with zend-log) within DotKernel, Zend Expressive, or any project using Zend Service Manager."
author: "Gabi DJ"
date_published: "2018-11-13"
canonical_url: "https://www.dotkernel.com/dotkernel/logging-with-dot-log-in-zend-expressive-and-dotkernel/"
category: "Dotkernel"
language: "en"
---

# Logging with dot-log in Zend Expressive and DotKernel

## TL;DR
This article explains how to use the [dot-log](https://github.com/dotkernel/dot-log) component within DotKernel, Zend Expressive, or any project that uses Zend Service Manager.
Since dot-log extends `zendframework/zend-log`, the tutorial is mostly compatible with zend-log as well.
See the [zend-log documentation](https://zendframework.github.io/zend-log/) for more detail.

## Adding the config provider

- Open `config/config.php`.
- If there's no entry for the dot-log config provider, add `\Dot\Log\ConfigProvider::class`.
- Make sure it's added before application-specific components, e.g. `Frontend\App\ConfigProvider::class`, `Admin\App\ConfigProvider::class`, `MyProject\ConfigProvider::class`.
- Inside `Dot\Log\ConfigProvider`, the dependencies section registers an abstract factory, `LoggerAbstractServiceFactory::class`.
This class responds to "selectors" instead of class names — instead of requesting `Zend\Log\Logger::class` from the container, you request `dot-log.my_logger` (or just `my_logger` if using zend-log).
- Create a `log.global.php` file within `/config/autoload`, returning an empty array to start.

## Configuring the logger

For this tutorial, the created logger is named **my_logger** (the name is the developer's choice and should reflect its purpose, e.g. `db_error_logger`).

In `log.global.php`:

1. Add a top-level key `dot-log` (or `log` if using zend-log) with an array value.
2. Inside it, add a `loggers` key.
3. Inside that, add the logger name key (`my_logger`) with an empty array.

For the logger to actually log somewhere, a writer is required — without one, log calls are received but there's nowhere to write the message.

## Configuring the writer(s)

Loggers must have at least one writer.
A **writer** is an object that inherits from `Zend\Log\Writer\AbstractWriter`, responsible for recording log data to a storage backend (see the [zend-log writer documentation](https://zendframework.github.io/zend-log/writers/)).

It's possible to separate logs into multiple files using writers and filters (e.g. `warnings.log`, `errors.log`, `all_messages.log`).
In the simplest example, all log messages are written to one file, e.g. `/data/logs/dk.log`, under a `writers` key inside `my_logger`.

Notes on writer configuration:

- The writer key name (e.g. `FileWriter`) is optional — otherwise the writers array would be enumerative instead of associative.
- The writer's `name` key is a developer-provided name for that writer and is **mandatory**.
- The writer's `priority` key doesn't affect which errors get written — it's only a way to organize writers (e.g. 1 - FILE, 2 - SQL, 3 - E-mail), reflecting that writing to a file is the most reliable since SQL or e-mail servers can be external and offline.
The priority key is optional.
- To write to a file, the `stream` key must be present in the writer's `options` array (required only when writing to streams/files).

More writer examples: [Streams](https://zendframework.github.io/zend-log/writers/#writing-to-streams), [Databases](https://zendframework.github.io/zend-log/writers/#writing-to-databases), [FirePHP](https://zendframework.github.io/zend-log/writers/#writing-to-firephp), [ChromePHP](https://zendframework.github.io/zend-log/writers/#writing-to-chromephp), [Mail](https://zendframework.github.io/zend-log/writers/#writing-to-mail), [MongoDB](https://zendframework.github.io/zend-log/writers/#writing-to-mongodb), [Syslog](https://zendframework.github.io/zend-log/writers/#writing-to-syslog), [Zend Monitor](https://zendframework.github.io/zend-log/writers/#writing-to-zend-monitor).

## (Optional) Configuring the filters

A **filter** prevents a message from being written to the log (see the [zend-log filters documentation](https://zendframework.github.io/zend-log/filters/)).

Per [PSR-3](https://www.php-fig.org/psr/psr-3/#5-psrlogloglevel), the log levels, in order of priority/importance, are:

| Level | Priority number |
|---|---|
| emergency | 0 |
| alert | 1 |
| critical | 2 |
| error | 3 |
| warn | 4 |
| notice | 5 |
| info | 6 |
| debug | 7 |

Although the plain Logger in Zend Log is not fully PSR-3 compatible, it provides a way to log all of these message types.
The developer can optionally use keys to name filters.
**Important:** the operator for "more important" messages is `<=`, because a smaller number represents a more important message.

More on filters: [zend-log filters documentation](https://zendframework.github.io/zend-log/filters/).

## (Optional) Configuring the formatter

The logged value isn't limited to a string — arrays can be logged too, and for readability they can be serialized. Zend Log provides String, XML, JSON and FirePHP formatting.

The formatter config accepts:

- `name` — the formatter class (must implement `Zend\Log\Formatter\FormatterInterface`)
- `options` — options passed to the formatter constructor, if required

More on formatters: [Simple](https://zendframework.github.io/zend-log/formatters/#simple-formatting), [JSON](https://zendframework.github.io/zend-log/formatters/#formatting-to-json), [XML](https://zendframework.github.io/zend-log/formatters/#formatting-to-xml), [FirePHP](https://zendframework.github.io/zend-log/formatters/#formatting-to-firephp).

## Full example (described)

A complete configuration, as described in the article, does the following:

- Uses the log through **dot-log**
- Names the logger **my_logger**
- Writes to file: **data/logs/dk.log**
- Explicitly allows **all messages** to be written
- Formats the message as **JSON**

## Usage

Basic usage of the logger:

```php
use Zend\Log\Logger;
```

```php
$logger = $container->get('dot-log.my_logger');

/** @var Logger $logger */
$logger->emerg('0 EMERG');
$logger->alert('1 ALERT');
$logger->crit('2 CRITICAL');
$logger->err('3 ERR');
$logger->warn('4 WARN');
$logger->notice('5 NOTICE');
$logger->info('6 INF');
$logger->debug('7 debug');
$logger->log(Logger::NOTICE, 'NOTICE from log()');
```

## FAQ

**Q: How do I register dot-log's config provider?**
A: In config/config.php, add `\Dot\Log\ConfigProvider::class` if it's not already there, making sure it is added before application-specific components such as Frontend\App\ConfigProvider or Admin\App\ConfigProvider.

**Q: How is a logger retrieved from the container instead of using the plain class name?**
A: Dot\Log\ConfigProvider registers an abstract factory, LoggerAbstractServiceFactory, that responds to "selectors" instead of class names.
Instead of requesting Zend\Log\Logger::class from the container, you request dot-log.my_logger (or just my_logger if using zend-log).

**Q: What is a writer, and how many does a logger need?**
A: A writer is an object that inherits from Zend\Log\Writer\AbstractWriter and is responsible for recording log data to a storage backend.
Loggers must have at least one writer, and the writer's "name" key is mandatory while its "priority" key is optional and only used to organize writers, not to affect which errors get written.

**Q: What does a filter do, and how are log levels ordered?**
A: A filter prevents a message from being written to the log.
Per PSR-3, the log levels in order of priority/importance are emergency (0), alert (1), critical (2), error (3), warn (4), notice (5), info (6), and debug (7) — the operator for "more important" messages is `<=` because a smaller number represents a more important message.

**Q: What does the formatter configuration control?**
A: The formatter accepts a "name" (a class implementing Zend\Log\Formatter\FormatterInterface) and "options" to pass to that formatter's constructor.
Zend Log provides String, XML, JSON and FirePHP formatting, and arrays can be serialized this way for better readability.

**Q: How do you actually write log messages once the logger is configured?**
A: Fetch the logger from the container, e.g. `$logger = $container->get('dot-log.my_logger');`, then call methods such as emerg(), alert(), crit(), err(), warn(), notice(), info(), debug(), or the generic log(Logger::NOTICE, 'message').

## Resources

- [zend-log documentation](https://zendframework.github.io/zend-log/)
- [zend-log writers documentation](https://zendframework.github.io/zend-log/writers/)
- [zend-log filters documentation](https://zendframework.github.io/zend-log/filters/)
- [dot-log on GitHub](https://github.com/dotkernel/dot-log)
