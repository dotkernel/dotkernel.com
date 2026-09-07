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
In Dotkernel, `Zend_Registry` holds a fixed set of request-scoped variables - from timing and configuration to the database adapter and session object - and can be read either as a full instance or one value at a time.

In Dotkernel, Zend_Registry will contain the following variables:

- **startTime** - the result of [microtime()](http://php.net/manual/en/function.microtime.php) at the beginning of the request
- **configuration** - the configuration options loaded from *configs/application.ini*
- **router** - routing settings loaded from *configs/router.xml*
- **database** - the database adapter
- **settings** - the settings loaded from the database
- **requestModule, requestController, requestAction** - the module, controller and action of the current request
- **request** - additional request variables
- **seo** - seo information loaded from *configs/dots/seo.xml* (site name, default description, keywords etc)
- **option** - the options for the current dot loaded from *configs/dots/<moduleName>.xml*
- **session** - the session object

To use the variables in the registry, you must first get an instance of the registry object:

```
$registry = Zend_Registry::getInstance();
//...
echo $registry->startTime;
//...
echo $registry->requestAction;
```

Or if you only need one variable from the registry, you can get it directly using:

```
$action = Zend_Registry::get('requestAction');
```

You can find more information about Zend_Registry, in the [Zend Framework Documentation](http://framework.zend.com/manual/en/zend.registry.using.html).

## FAQ

**Q: What variables does Zend_Registry contain in Dotkernel?**
A: It contains startTime (the result of microtime() at the beginning of the request), configuration (loaded from configs/application.ini), router (loaded from configs/router.xml), database (the database adapter), settings (loaded from the database), requestModule/requestController/requestAction, request (additional request variables), seo (loaded from configs/dots/seo.xml), option (loaded from configs/dots/<moduleName>.xml), and session (the session object).

**Q: How do you get an instance of the registry?**
A: Use $registry = Zend_Registry::getInstance(); and then access variables such as $registry->startTime or $registry->requestAction.

**Q: How do you retrieve just one variable from the registry?**
A: If you only need one variable, you can get it directly using $action = Zend_Registry::get('requestAction');.

**Q: What does the "seo" entry in the registry contain?**
A: It contains seo information loaded from configs/dots/seo.xml, such as site name, default description, and keywords.
