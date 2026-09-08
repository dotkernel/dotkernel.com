---
title: "Disable Wurfl redirect for mobile browsers"
description: "Shows the application.ini setting introduced in revision 408 to control Dotkernel's automatic Wurfl-based redirect of mobile visitors to the mobile site, and the code that checks it."
author: "Adrian"
date_published: "2011-01-31"
canonical_url: "https://www.dotkernel.com/dotkernel/disable-wurfl-redirect-for-mobile-browsers/"
category: "Dotkernel"
language: "en"
---

# Disable Wurfl redirect for mobile browsers

## TL;DR

Dotkernel's example mobile site normally relies on Wurfl to detect mobile browsers and automatically redirect visitors there on their first homepage view, which isn't always desired.
As of revision 408, this behavior is controlled by a single `resources.useragent.wurflapi.redirect` setting in application.ini.
The article shows that setting along with the matching condition in `IndexController.php` that checks it before registering and redirecting a visit.

Dotkernel has an example mobile site at [http://v1.dotkernel.net/mobile](http://v1.dotkernel.net/mobile) that uses [jQuery Mobile](http://jquerymobile.com/).
Wurfl is also used to detect mobile browsers (as discussed in a [previous blog post](http://www.dotkernel.com/dotkernel/wurfl-zend-framework-integration-into-dotkernel/)) and automatically redirect them to the mobile site the first time they view the homepage.
Sometimes this behavior isn't desired (for example when you don't have a mobile site, or you don't plan on using Wurfl at all).

Starting with revision 408, there's an option in application.ini to disable the automatic redirect (by default the redirect is disabled):

```ini
resources.useragent.wurflapi.redirect = false
```

The following condition is also added to Controllers/frontend/IndexController.php (at line 19) to check the configuration:

```php
//if automatic redirect is enabled in application.ini and the browser is mobile and session->mobileHit is not set, register it and redirect
if($config->resources->useragent->wurflapi->redirect && 'mobile' == Dot_Kernel::getDevice()->getType() && !isset($session->mobileHit))
```

## FAQ

**Q: How do you disable the automatic mobile redirect in Dotkernel?**
A: Starting with revision 408, set resources.useragent.wurflapi.redirect = false in application.ini. Per the article, this is also the default state of the redirect option.

**Q: Where in the code is this configuration option checked?**
A: In Controllers/frontend/IndexController.php (around line 19), a condition checks whether the redirect is enabled in application.ini, whether the visiting browser is mobile, and whether session->mobileHit isn't already set, before registering and redirecting the visit.
