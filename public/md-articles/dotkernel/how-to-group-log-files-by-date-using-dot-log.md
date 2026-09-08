---
title: "How to group log files by date using dot-log"
description: "How dot-log Version 3.1.1 lets you use datetime formatter strings in the stream option of a log writer, so log files get grouped by date."
author: "Alex Karajos"
date_published: "2021-04-26"
canonical_url: "https://www.dotkernel.com/dotkernel/how-to-group-log-files-by-date-using-dot-log/"
category: "Dotkernel"
language: "en"
---

# How to group log files by date using dot-log

## TL;DR
[dot-log](https://github.com/dotkernel/dot-log) is a powerful, easily customizable logging tool.
[Version 3.1.1](https://github.com/dotkernel/dot-log/releases/tag/3.1.1) adds the ability to use datetime formatter strings right in the stream option of a log writer, and fixes an issue where caching dot-log configs caused logs to be written to a single file instead of being grouped by date.

## Prerequisites

- dot-log installed and configured inside your application.
If it's not installed, follow the steps in [Logging with dot-log in Zend Expressive and Dotkernel](https://www.dotkernel.com/dotkernel/logging-with-dot-log-in-zend-expressive-and-dotkernel/).
- Otherwise, make sure you're using the latest version by running:

```shell
composer update dotkernel/dot-log
```

If your application logs messages to a single file with a static name (e.g. `log/dk.log`), you can skip the rest of this guide - logging will work as before.

## Configuring the logger with Dotkernel

Your application should already have a `config/autoload/error-handling.global.php` file similar to:

```php
<?php

return [
    'dot-errorhandler' => [
        'loggerEnabled' => true,
        'logger' => 'dot-log.default_logger'
    ],
    'dot_log' => [
        'loggers' => [
            'default_logger' => [
                'writers' => [
                    'FileWriter' => [
                        'name' => 'stream',
                        'priority' => \Laminas\Log\Logger::ALERT,
                        'options' => [
                            'stream' => sprintf('%s/../../log/error-log-%s.log', __DIR__, date('Y-m-d')),
                            // explicitly log all messages
                            'filters' => [
                                'allMessages' => [
                                    'name' => 'priority',
                                    'options' => [
                                        'operator' => '>=',
                                        'priority' => \Laminas\Log\Logger::EMERG,
                                    ],
                                ],
                            ],
                            'formatter' => [
                                'name' => \Laminas\Log\Formatter\Json::class,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
```

1. Locate every log writer by navigating to `dot_log -> loggers -> default_logger -> writers`.
2. For each writer, find the `stream` option containing the path to the log file.
If it looks like:

   ```php
   'stream' => sprintf('%s/../../log/error-log-%s.log', __DIR__, date('Y-m-d'))
   ```

3. Replace it with:

   ```php
   'stream' => __DIR__ . '/../../log/error-log-{Y}-{m}-{d}.log'
   ```

4. Clear the config cache:

   ```shell
   php bin/clear-config-cache.php
   ```

If the log path uses other date format specifiers, adapt them accordingly - the full list is in the [PHP date() manual](https://www.php.net/manual/en/datetime.format.php).

## Configuring the logger without Dotkernel

Locate the dot-log config (probably `/config/autoload/log.global.php`), which should look similar to the Dotkernel example but keyed under a custom logger name (e.g. `my_logger`) and may define multiple writers (e.g. a general file writer, a warnings-only writer, and a warnings-or-higher writer).

1. Locate every writer by navigating to `dot_log -> loggers -> my_logger -> writers`.
2. For each writer, find the `stream` option.
If it looks like:

   ```php
   'stream' => sprintf('%s/../../log/dk-%s.log', __DIR__, date('Y-m-d'))
   ```

3. Replace it with:

   ```php
   'stream' => __DIR__ . '/../../log/error-log-{Y}-{m}-{d}.log',
   ```

4. Make sure you clear your application's config before usage.

As before, adapt any other date format specifiers as needed - see the [PHP date() manual](https://www.php.net/manual/en/datetime.format.php).

## FAQ

**Q: What does dot-log Version 3.1.1 add?**
A: Version 3.1.1 adds the ability to use datetime formatter strings directly in the stream option of your log writer, and it fixes an issue where caching dot-log configs caused logs to be written to the same file instead of being grouped by date.

**Q: How do I make sure I'm using the fix in Version 3.1.1?**
A: Make sure you're using the latest version of dot-log by running `composer update dotkernel/dot-log`.

**Q: Do I need to change anything if my logs are already written to a single static file?**
A: No. If your application logs messages in a single file with a static name (e.g. log/dk.log), you can skip the rest of the article - logging will work as before.

**Q: How do I group log files by date when using Dotkernel?**
A: In `config/autoload/error-handling.global.php`, locate every log writer by navigating to `dot_log->loggers->default_logger->writers` and replace a stream value like `sprintf('%s/../../log/error-log-%s.log', __DIR__, date('Y-m-d'))` with `__DIR__ . '/../../log/error-log-{Y}-{m}-{d}.log'`.

**Q: How do I group log files by date without Dotkernel?**
A: In your dot-log config (e.g. `/config/autoload/log.global.php`), locate every writer under `dot_log->loggers->my_logger->writers` and replace a dynamic stream value with a formatter string using `{Y}-{m}-{d}` placeholders.

**Q: What must I do after changing the stream configuration?**
A: Clear the config cache.
In a Dotkernel application, run `php bin/clear-config-cache.php`; in a non-Dotkernel setup, make sure you clear your application's config before usage.

## Resources

- [dot-log on GitHub](https://github.com/dotkernel/dot-log)
- [dot-log Version 3.1.1 release notes](https://github.com/dotkernel/dot-log/releases/tag/3.1.1)
- [Logging with dot-log in Zend Expressive and Dotkernel](https://www.dotkernel.com/dotkernel/logging-with-dot-log-in-zend-expressive-and-dotkernel/)
- [PHP date() format specifiers](https://www.php.net/manual/en/datetime.format.php)
