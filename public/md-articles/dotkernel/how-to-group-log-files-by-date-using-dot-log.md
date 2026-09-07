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

As described in [this article](https://www.dotkernel.com/dotkernel/logging-with-dot-log-in-zend-expressive-and-dotkernel/), [dot-log](https://github.com/dotkernel/dot-log) is a powerful tool for logging messages in your application. It's power stays in the fact that it can be implemented in a few easy steps and that it's highly customizable.

With a little help from PHP's `date` function, [Version 3.1.1](https://github.com/dotkernel/dot-log/releases/tag/3.1.1) takes this one step further, though. It adds the ability to use datetime formatter strings right in the stream option of your log writer. It also fixes an issue where caching dot-log configs caused logs to be written to the same file, instead of being grouped by date.

## Prerequisites

You will need dot-log installed and configured inside your application. If it's not installed, you can install it by following the steps described [here](https://www.dotkernel.com/dotkernel/logging-with-dot-log-in-zend-expressive-and-dotkernel/). Else, make sure you're using the latest version of dot-log by running `composer update dotkernel/dot-log`.

As always, we strongly suggest you to keep your packages updated. Allthough, if your application logs messages in a single file with a static name (eg: log/dk.log), you can skip the rest of this article - logging will work as before.

## Configuring the logger with Dotkernel

Your application should already have a **config/autoload/error-handling.global.php** file, similar to this:

```
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

Inside that file, locate every instance of your log writers by navigating to: `dot_log->loggers->default_logger->writers`. For each writer, you'll find a `stream` option containing the path to your log file. If your `stream` config looks like this:

`'stream' => sprintf('%s/../../log/error-log-%s.log', __DIR__, date('Y-m-d'))`

replace it with:

`'stream' => __DIR__ . '/../../log/error-log-{Y}-{m}-{d}.log'`

The last step is to clear config cache using the command:

`php bin/clear-config-cache.php`

If the path to your log files contains other date format specifiers, make sure you adapt it accordingly. A complete list of the specifiers can be found [here](https://www.php.net/manual/en/datetime.format.php).

## Configuring the logger without Dotkernel

Locate dot-log configs in your application (probably /config/autoload/log.global.php). They should look similar to this:

```
<?php

return [
    'dot_log' => [
        'loggers' => [
            'my_logger' => [
                'writers' => [
                    'FileWriter' => [
                        'name' => 'FileWriter',
                        'priority' => \Laminas\Log\Logger::ALERT,
                        'options' => [
                            'stream' => __DIR__ . '/../../log/dk.log',
                            'filters' => [
                                'allMessages' => [
                                    'name' => 'priority',
                                    'options' => [
                                        'operator' => '>=',
                                        'priority' => \Laminas\Log\Logger::EMERG,
                                    ]
                                ],
                            ],
                        ],
                    ],
                    // Only warnings
                    'OnlyWarningsWriter' => [
                        'name' => 'stream',
                        'priority' => \Laminas\Log\Logger::ALERT,
                        'options' => [
                            'stream' => __DIR__ . '/../../log/warnings_only.log',
                            'filters' => [
                                'warningOnly' => [
                                    'name' => 'priority',
                                    'options' => [
                                        'operator' => '==',
                                        'priority' => \Laminas\Log\Logger::WARN,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    // Warnings and more important messages
                    'WarningOrHigherWriter' => [
                        'name' => 'stream',
                        'priority' => \Laminas\Log\Logger::ALERT,
                        'options' => [
                            'stream' => __DIR__ . '/../../log/important_messages.log',
                            'filters' => [
                                'importantMessages' => [
                                    'name' => 'priority',
                                    'options' => [
                                        // note, the smaller the priority, the more important is the message
                                        // 0 - emergency, 1 - alert, 2- error, 3 - warn. .etc
                                        'operator' => '<=',
                                        'priority' => \Laminas\Log\Logger::WARN,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
```

Inside that file, locate every instance of your log writers by navigating to: `dot_log->loggers->my_logger->writers`. For each writer, you'll find a `stream` option containing the path to your log file. If your `stream` config looks like this:

`'stream' => sprintf('%s/../../log/dk-%s.log', __DIR__, date('Y-m-d'))`

replace it with:

```
'stream' => __DIR__ . '/../../log/error-log-{Y}-{m}-{d}.log',
```

If the path to your log files contains other date format specifiers, make sure you adapt it accordingly. A complete list of the specifiers can be found [here](https://www.php.net/manual/en/datetime.format.php).

Make sure you clear your application's config before usage.

## FAQ

**Q: What does dot-log Version 3.1.1 add?**
A: Version 3.1.1 adds the ability to use datetime formatter strings directly in the stream option of your log writer, and it fixes an issue where caching dot-log configs caused logs to be written to the same file instead of being grouped by date.

**Q: How do I make sure I'm using the fix in Version 3.1.1?**
A: Make sure you're using the latest version of dot-log by running composer update dotkernel/dot-log.

**Q: Do I need to change anything if my logs are already written to a single static file?**
A: No. If your application logs messages in a single file with a static name (e.g. log/dk.log), you can skip the rest of the article - logging will work as before.

**Q: How do I group log files by date when using Dotkernel?**
A: In config/autoload/error-handling.global.php, locate every log writer by navigating to dot_log->loggers->default_logger->writers and replace a stream value like sprintf('%s/../../log/error-log-%s.log', __DIR__, date('Y-m-d')) with __DIR__ . '/../../log/error-log-{Y}-{m}-{d}.log'.

**Q: How do I group log files by date without Dotkernel?**
A: In your dot-log config (e.g. /config/autoload/log.global.php), locate every writer under dot_log->loggers->my_logger->writers and replace a dynamic stream value such as sprintf('%s/../../log/dk-%s.log', __DIR__, date('Y-m-d')) with a formatter string like __DIR__ . '/../../log/error-log-{Y}-{m}-{d}.log'.

**Q: What must I do after changing the stream configuration?**
A: Clear the config cache. In a Dotkernel application, run php bin/clear-config-cache.php; in a non-Dotkernel setup, make sure you clear your application's config before usage.
