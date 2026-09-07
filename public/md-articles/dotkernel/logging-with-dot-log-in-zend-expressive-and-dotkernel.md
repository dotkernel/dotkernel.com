---
title: "Logging with dot-log in Zend Expressive and Dotkernel"
description: "How to wire up, configure and use the dot-log component (compatible with zend-log) within Dotkernel, Zend Expressive, or any project using Zend Service Manager."
author: "Gabi DJ"
date_published: "2018-11-13"
canonical_url: "https://www.dotkernel.com/dotkernel/logging-with-dot-log-in-zend-expressive-and-dotkernel/"
category: "Dotkernel"
language: "en"
---

# Logging with dot-log in Zend Expressive and Dotkernel

## TL;DR
This article explains how to use the [dot-log](https://github.com/dotkernel/dot-log) component within Dotkernel, Zend Expressive, or any project that uses Zend Service Manager.
Since dot-log extends `zendframework/zend-log`, the tutorial is mostly compatible with zend-log as well.
See the [zend-log documentation](https://zendframework.github.io/zend-log/) for more detail.

This article will explain the usage of the **[dot-log](https://github.com/dotkernel/dot-log)** component within Dotkernel, Zend Expressive or in a project that uses Zend Service Manager.

Since dot-log extends zendframework/zend-log this tutorial mostly compatible with zend-log as well.

For a more detailed documentation about the zend-log visit the [zend-log documentation](https://zendframework.github.io/zend-log/).

## Adding The Config Provider

- Enter **config/config.php**
- If there is no entry for the config provider below, add it: \Dot\Log\ConfigProvider::class
- Make sure it is added before with the Application-Specific components, eg.: `\Frontend\App\ConfigProvider.php`,  `\Admin\App\ConfigProvider::class`,  `MyProject\ConfigProvider::class` , etc.
- Open the `Dot\Log\ConfigProvider`
  - In the dependencies section you will see an **absctract factory **(`LoggerAbstractServiceFactory::class`)
  - This class responds to "selectors" instead of class names
  - Instead of requesting the `Zend\Log\Logger::class` from the container, `dot-log.my_logger` should be requested (or just `my_logger` if using zend-log)
- Next, create a **log.global.php **file within **/config/autoload**
  - return an empty array for start

## Configuring the logger

For compatibility between components and for the better understanding of this tutorial the name for the created logger will be **my_logger**.

The logger name is the developer's choice and should reflect its purpose (eg.: db_error_logger - a logger that only writes the error messages in **db**).

Create a key-value pair, the key should be **dot-log** (or **log** if using zend-log) and the value should be an empty array.

In the newly created value add a key loggers, and the value should be an array with the key **my_logger** and an empty array as the value.

 

At this point your log.global.php should look like this:

```
return
        ],
    ],
];
```

 

For this logger to actually log somewhere a writer is required, otherwise the log command will be received, but the logger will have no place to write the message in.

The next step will show you how to configure writing to a specific file.

## Configuring the writer(s)

Loggers must have at least one writer.

A ***writer*** is an object that inherits from `Zend\Log\Writer\AbstractWriter`. A writer's responsibility is to record log data to a storage backend. (from [zend-log's writer documentation](https://zendframework.github.io/zend-log/writers/))

 

### Writing to a file (stream)

It is possible separate logs into multiple files using writers and filters. For example **warnings.log, errors.log**, all_messages.log.

In this example all the log messages will be written in one file.

- In the my_logger key insert an empty array on key **writers**
  - The **writers** will all be used when writing logs
- The following is the simplest example to write anything to **/data/logs/dk.log**

```
return ,
                    ],
                ],
            ]
        ],
    ],
];
```

The **FileWriter** key is optional, otherwise the writers array would be enumerative instead of associative.

The writer **name** key is a developer-provided name for that writer, the writer name key is **mandatory**.

The writer priority key is not affecting the errors that are written, it is a way to organize writers, for example:

- 1 - FILE
- 2 - SQL
- 3 - E-mail

It is the most important to write in the file, the sql or e-mail are more probably fail because the servers can be external and offline, the file is on the same server.

The writer priority key is optional.

To write into a file the key **stream** must be present in the **writer options** array. This is required only if writing into streams/files.

 

For more examples see the zend-log streams:

- [Writing to Streams](https://zendframework.github.io/zend-log/writers/#writing-to-streams)
- [Writing to Databases](https://zendframework.github.io/zend-log/writers/#writing-to-databases)
- [Writing to FirePHP](https://zendframework.github.io/zend-log/writers/#writing-to-firephp)
- [Writing to ChromePHP](https://zendframework.github.io/zend-log/writers/#writing-to-chromephp)
- [Writing to Mail](https://zendframework.github.io/zend-log/writers/#writing-to-mail)
- [Writing to MongoDB](https://zendframework.github.io/zend-log/writers/#writing-to-mongodb)
- [Writing to Syslog](https://zendframework.github.io/zend-log/writers/#writing-to-syslog)
- [Writing to Zend Monitor](https://zendframework.github.io/zend-log/writers/#writing-to-zend-monitor)

## (Optional) Configuring the Filters

A *filter* prevents a message from being written to the log. (from [zend-log filters documentation](https://zendframework.github.io/zend-log/filters/))

As per [PSR-3 document](https://www.php-fig.org/psr/psr-3/#5-psrlogloglevel).

The log levels are: **emergency (0)**, **alert (1)**, **critical (2)**, **error (3)**, **warn (4)**, **notice (5)**, **info (6)**, **debug (7)** (in order of priority/importance)

Although the plain Logger in Zend Log is not fully compatible with PSR-3, it provides a way to log all of these message types.

 

Starting from the basic writer configuration, several configurations will be added for extra functionality.

 

```
<?php

return
                                ],
                            ],
                        ],
                    ],
                    // Only warnings
                    'OnlyWarningsWriter' => ,
                                ],
                            ],
                        ],
                    ],
                    // Warnings and more important messages
                    'WarningOrHigherWriter' => ,
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

As in the writer configuration, the developer can optionally use keys for associating the filters with a name.

**IMPORTANT NOTE:** the operator for more important messages is **<=,** this is because the number representation is smaller for a more important message type.

The filter added on the first writer is equal to not setting a filter, but it was been added to illustrate how to explicitly allow all messages.

It was added opposite to the others just to demonstrate the other operator is also an option.

 

More examples on filters: [https://zendframework.github.io/zend-log/filters/](https://zendframework.github.io/zend-log/filters/)

## (Optional) Configuring the Formatter

When using dot-log or zend-log, the logged value is not limited to a string. Arrays can be logged as well.

For a better readability, these arrays can be serialized.

Zend Log provides String formatting, XML, JSON and FirePHP formatting.

 

The formatter accepts following parameters:

- name - the formatter class (it must implement **Zend\Log\Formatter\FormatterInterface**)
- options - options to pass to the formatter constructor if required

 

The following formats the message as JSON data:

```
'formatter' => [
    'name' => \Zend\Log\Formatter\Json::class,
],
```

 

- [Simple Formatting](https://zendframework.github.io/zend-log/formatters/#simple-formatting)
- [Formatting to JSON](https://zendframework.github.io/zend-log/formatters/#formatting-to-json)
- [Formatting to XML](https://zendframework.github.io/zend-log/formatters/#formatting-to-xml)
- [Formatting to FirePhp](https://zendframework.github.io/zend-log/formatters/#formatting-to-firephp)

 

## Full example

Below an example which:

- The log is used through **dot-log**
- The logger name is **my_logger**
- Writes to file: **data/logs/dk.log**
- Explicitly allows **all the messages** to be written
- Formats the message as **JSON**

The key elements are **bold**.

```
<?php

return ,
                                ],
                            ],
                            'formatter' => ,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
```

 

## Usage

Basic usage of the logger is illustraded below.

The messages are written to see which logs are written and which are not written.

```
use Zend\Log\Logger;
```

...

```
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

## Sources:

- https://zendframework.github.io/zend-log/
- https://zendframework.github.io/zend-log/writers/
- https://zendframework.github.io/zend-log/filters/

This article will be continued in a different article that treats the error handling in middleware applications.

 

## FAQ

**Q: How do I register dot-log's config provider?**
A: In config/config.php, add \Dot\Log\ConfigProvider::class if it's not already there, making sure it is added before application-specific components such as Frontend\App\ConfigProvider or Admin\App\ConfigProvider.

**Q: How is a logger retrieved from the container instead of using the plain class name?**
A: Dot\Log\ConfigProvider registers an abstract factory, LoggerAbstractServiceFactory, that responds to "selectors" instead of class names. Instead of requesting Zend\Log\Logger::class from the container, you request dot-log.my_logger (or just my_logger if using zend-log).

**Q: What is a writer, and how many does a logger need?**
A: A writer is an object that inherits from Zend\Log\Writer\AbstractWriter and is responsible for recording log data to a storage backend. Loggers must have at least one writer, and the writer's "name" key is mandatory while its "priority" key is optional and only used to organize writers, not to affect which errors get written.

**Q: What does a filter do, and how are log levels ordered?**
A: A filter prevents a message from being written to the log. Per PSR-3, the log levels in order of priority/importance are emergency (0), alert (1), critical (2), error (3), warn (4), notice (5), info (6), and debug (7) - the operator for "more important" messages is <= because a smaller number represents a more important message.

**Q: What does the formatter configuration control?**
A: The formatter accepts a "name" (a class implementing Zend\Log\Formatter\FormatterInterface) and "options" to pass to that formatter's constructor. Zend Log provides String, XML, JSON and FirePHP formatting, and arrays can be serialized this way for better readability.

**Q: How do you actually write log messages once the logger is configured?**
A: Fetch the logger from the container, e.g. $logger = $container->get('dot-log.my_logger');, then call methods such as emerg(), alert(), crit(), err(), warn(), notice(), info(), debug(), or the generic log(Logger::NOTICE, 'message').
