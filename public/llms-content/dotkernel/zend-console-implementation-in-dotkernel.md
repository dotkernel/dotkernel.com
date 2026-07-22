---
title: "Zend_Console implementation in DotKernel"
description: "How DotKernel's Console bootstrap lets you run PHP scripts from the command line, including its arguments and bundled example actions."
author: "Adrian"
date_published: "2011-10-13"
canonical_url: "https://www.dotkernel.com/dotkernel/zend-console-implementation-in-dotkernel/"
category: "Dotkernel"
language: "en"
---

# Zend_Console implementation in DotKernel

## TL;DR

Starting with version 1.5, DotKernel has a Console bootstrap to easily run PHP scripts from the command line.
The most common use for this is running cron jobs without using wget or going through Apache.

## Where it lives

The bootstrap can be found in `Console/index.php`, and it has one controller in `Controller.php` with two example actions.

## Usage

There are two command line arguments:

| Argument | Meaning |
|---|---|
| `-a` | The name of the action that will be executed |
| `-e` | The environment, as defined in application.ini. Possible values are development, staging, and production. Optional, defaults to production |

Any other arguments set when calling the script will be available in the controller in the `$registry->arguments` array.

The controller bundled with DotKernel has two example actions: `count-users`, which demonstrates how to interact with models, and `send-newsletter`, which reads the newsletter from the command line.

## Examples

```shell
/var/www/vhosts/example.com/httpdocs/Cron/index.php -e staging -a count-users
```

```shell
/var/www/vhosts/example.com/httpdocs/Cron/index.php -e staging -a send-newsletter "test newsletter"
```

## FAQ

**Q: What is the purpose of the Console bootstrap in DotKernel?**
A: Starting with version 1.5, DotKernel has a Console bootstrap to easily run PHP scripts from the command line, most commonly used to run cron jobs without using wget or going through Apache.

**Q: Where is the Console bootstrap located?**
A: The bootstrap is found in Console/index.php, and it has one controller in Controller.php with two example actions.

**Q: What command line arguments does the Console bootstrap support?**
A: There are two arguments: -a, the name of the action to execute, and -e, the environment as defined in application.ini (development, staging, or production), which is optional and defaults to production.

**Q: What happens to extra arguments passed to the script?**
A: Any other arguments set when calling the script will be available in the controller in the registry's arguments array.

**Q: What example actions are bundled with the Console controller?**
A: The bundled controller has two example actions: count-users, which demonstrates how to interact with models, and send-newsletter, which reads the newsletter from the command line.
