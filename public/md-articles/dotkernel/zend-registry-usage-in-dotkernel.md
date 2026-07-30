---
title: "Zend Registry usage in Dotkernel"
description: "The variables stored in Zend_Registry in Dotkernel and how to read them, either as a full instance or one value at a time."
author: "Adrian"
date_published: "2011-06-01"
canonical_url: "https://www.dotkernel.com/dotkernel/zend-registry-usage-in-dotkernel/"
category: "Dotkernel"
language: "en"
---

# Zend Registry usage in Dotkernel

## TL;DR

In Dotkernel, `Zend_Registry` holds a fixed set of request-scoped variables — from timing and configuration to the database adapter and session object — and can be read either as a full instance or one value at a time.

## Variables stored in Zend_Registry

| Variable | Contents |
|---|---|
| `startTime` | The result of [microtime()](http://php.net/manual/en/function.microtime.php) at the beginning of the request |
| `configuration` | The configuration options loaded from `configs/application.ini` |
| `router` | Routing settings loaded from `configs/router.xml` |
| `database` | The database adapter |
| `settings` | The settings loaded from the database |
| `requestModule, requestController, requestAction` | The module, controller, and action of the current request |
| `request` | Additional request variables |
| `seo` | SEO information loaded from `configs/dots/seo.xml` (site name, default description, keywords, etc.) |
| `option` | The options for the current dot loaded from `configs/dots/<moduleName>.xml` |
| `session` | The session object |

## Reading values from the registry

To use the variables in the registry, first get an instance of the registry object:

```php
$registry = Zend_Registry::getInstance();
//...
echo $registry->startTime;
//...
echo $registry->requestAction;
```

Or, if you only need one variable, get it directly:

```php
$action = Zend_Registry::get('requestAction');
```

## FAQ

**Q: What variables does Zend_Registry contain in Dotkernel?**
A: It contains startTime (the result of microtime() at the beginning of the request), configuration (loaded from configs/application.ini), router (loaded from configs/router.xml), database (the database adapter), settings (loaded from the database), requestModule/requestController/requestAction, request (additional request variables), seo (loaded from configs/dots/seo.xml), option (loaded from configs/dots/<moduleName>.xml), and session (the session object).

**Q: How do you get an instance of the registry?**
A: Use `$registry = Zend_Registry::getInstance();` and then access variables such as `$registry->startTime` or `$registry->requestAction`.

**Q: How do you retrieve just one variable from the registry?**
A: If you only need one variable, you can get it directly using `$action = Zend_Registry::get('requestAction');`.

**Q: What does the "seo" entry in the registry contain?**
A: It contains seo information loaded from configs/dots/seo.xml, such as site name, default description, and keywords.

## Resources

- microtime() PHP manual: http://php.net/manual/en/function.microtime.php
- Zend Framework Documentation on Zend_Registry: http://framework.zend.com/manual/en/zend.registry.using.html
