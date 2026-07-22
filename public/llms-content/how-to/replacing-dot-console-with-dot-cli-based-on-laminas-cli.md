---
title: "Replacing dot-console with dot-cli based on laminas-cli"
description: "How to install and configure dot-cli, Dotkernel's laminas-cli based replacement for the abandoned dot-console package, including its built-in FileLocker locking system."
author: "Alex Karajos"
date_published: "2021-06-09"
canonical_url: "https://www.dotkernel.com/how-to/replacing-dot-console-with-dot-cli-based-on-laminas-cli/"
category: "How to's"
language: "en"
---

# Replacing dot-console with dot-cli based on laminas-cli

## TL;DR

DotKernel's dot-cli package replaces dot-console, which was abandoned after Laminas dropped the laminas-console package it was based on.
Setting it up involves requiring the package via Composer, registering its ConfigProvider, and copying its bootstrap and config files into the application.
It also ships with FileLocker, a built-in, enabled-by-default locking system that prevents overlapping calls to the same command.

## Implementing dot-cli in Your Application

DotKernel's [dot-cli](https://github.com/dotkernel/dot-cli) package comes as a replacement for [dot-console](https://github.com/dotkernel/dot-console), which was abandoned after Laminas abandoned their [laminas-console](https://github.com/laminas/laminas-console) package, that dot-console was based on.

## Setup

### Install Package

Run the following command in your application's root directory:

```bash
composer require dotkernel/dot-cli
```

### Register ConfigProvider

Open your application's `config/config.php` file and add `Dot\Cli\ConfigProvider::class,` under the DK packages comment.

### Create Bootstrap File

Locate the provided `vendor/dotkernel/dot-cli/bin/cli.php` and copy it into your application's `bin` directory.
This is the file you will execute your commands through.

### Create Config File

Locate the provided `vendor/dotkernel/dot-cli/config/autoload/cli.global.php` and copy it into your application's `config/autoload` directory.
This file already contains a sample command (`demo:command`), that serves as an example on creating and registering new commands.
For more information on configuring/chaining commands, see the [laminas-cli documentation](https://docs.laminas.dev/laminas-cli/).

## Usage

Run the following command in your application's root directory:

```bash
php ./bin/cli.php
```

The output should look similar to this, containing information on how to start using dot-cli:

```
DotKernel CLI 1.0.0

Usage:
  command

Options:
  -h, --help            Display help for the given command. When no command is given display help for the list command
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  help          Display help for a command
  list          List commands
 demo
  demo:command  Demo command description.
```

## Set Up Command as Cronjob

Open your crontab and add the following line:

```bash
* * * * {PATH_TO_PHP_EXECUTABLE} {PATH_TO_APPLICATION}/bin/cli.php demo:command
```

Where {PATH_TO_PHP_EXECUTABLE} needs to be replaced with the full path to the PHP executable and {PATH_TO_APPLICATION} with the full path to your application's root directory.

## FileLocker

dot-cli has a built-in locking system, called FileLocker, enabled by default.
This feature prevents multiple calls to the same command overlapping each other by making sure the latter calls won't run until the former one is finished.
You can toggle this by modifying the previously created `config/autoload/cli.global.php` under `FileLockerInterface::class -> enabled`.

## FAQ

**Q: Why was dot-console replaced by dot-cli?**
A: dot-console was abandoned after Laminas abandoned its laminas-console package, which dot-console was based on, so dot-cli was created as its replacement.

**Q: How do you install dot-cli in an application?**
A: Run `composer require dotkernel/dot-cli` in the application's root directory, then register `Dot\Cli\ConfigProvider::class,` in `config/config.php`, and copy the provided `bin/cli.php` bootstrap file and `config/autoload/cli.global.php` config file into the application.

**Q: What does the provided cli.global.php config file contain?**
A: It already contains a sample command (`demo:command`) that serves as an example on how to create and register new commands.

**Q: How do you run a dot-cli command as a cronjob?**
A: Add a line to your crontab such as `* * * * {PATH_TO_PHP_EXECUTABLE} {PATH_TO_APPLICATION}/bin/cli.php demo:command`, replacing the placeholders with the full path to the PHP executable and the full path to the application's root directory.

**Q: What is FileLocker?**
A: FileLocker is a built-in locking system in dot-cli, enabled by default, that prevents multiple calls to the same command from overlapping by making sure later calls won't run until the earlier one finishes.
It can be toggled in `config/autoload/cli.global.php` under `FileLockerInterface::class -> enabled`.
