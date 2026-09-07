---
title: "Dotkernel 1.8.0 LTS Released"
description: "Dotkernel 1.8.0 (LTS) introduces a plugin architecture, a redesigned mobile-friendly admin and frontend, APC/File caching for speed, a new Dot_Request class, and several security and alerting improvements."
author: "Gabi DJ"
date_published: "2015-06-08"
canonical_url: "https://www.dotkernel.com/dotkernel/dotkernel-1-8-0-lts-released/"
category: "Dotkernel"
language: "en"
---

# Dotkernel 1.8.0 LTS Released

## TL;DR
Dotkernel 1.8.0 (LTS) was released with a new Plugin Architecture, a redesigned and mobile-friendly frontend, APC/File caching for faster response times, a new Dot_Request class, and multiple security and alerting improvements.
Some features (WURFL integration, multiple SMTP transporters) were removed from core and made available as plugins instead.

Dotkernel 1.8.0 (LTS) was just released.

## **What is LTS?**

Long-term support (LTS) is a type of special versions or editions of software designed to be supported for a longer than normal period. It is particularly applicable to open-source software projects. It contains many bug fixes, some refactoring and a few minor features. Find more details read [this article](http://www.dotkernel.com/long-term-support).

 

Here are a few of the many changes to Dotkernel in the latest release:

## Highlights of 1.8.0 (LTS)

 

### Plugin Architecture

Starting with 1.8.0 we will start using Plugins to make the Dotkernel extending easier. We'll keep you up to date about how you create and use a plugin.

### New design

We've redesigned the admin module and the frontend module is now mobile-friendly, but you can still use the mobile module.

### Loads Faster

The Dotkernel framework just got a big boost because it supports APC & File Caching within the framework, all the XML's and config files are cached in order to maximize response speed for more information about caching and how to cache your data see [this article](http://www.dotkernel.com/dotkernel/caching-in-dotkernel-using-zend-framework/).

### Easier Request Handling

We've added a new class, Dot_Request, which lets you have control over the request data before you use it, for example the variables $_SERVER, $_GET and $_POST are only used within controllers.

### Features added

- API with Rate Limit - we've added a simple API with a single key authentification and a simple implementation of a rate limit (see /configs/application.ini - section params.api)
- Cache System - Built on the Zend_Cache backends, provides caching within Dotkernel, but also in library, more details about this and how it works can be found [here](http://www.dotkernel.com/dotkernel/dotkernel-reserved-variable-names-for-caching/).

### Other changes

- Removed WURFL integration, Dotkernel can detect wether you use a mobile device separately now, the WURFL Library can be added as a Plugin from now on
- Removed multiple SMTP Transporter, it can be added as a plugin
- Security scan in Admin Dashboard - you can now see which are the recommended settings (especially security related) for Dotkernel to work at it's best
- Admin fail logins are no longer sent to the first admin, they are sent to all developers found in *devEmails* within *settings* table in the database
- Alert System - Alerts can be sent to all the developers to notify them if something goes wrong, for more information about alerts read [this article](http://www.dotkernel.com/dotkernel/how-to-use-alerts-in-dotkernel/).

### Bug Fixes

- [0000289](http://dotkernel.net/view.php?id=289): **[Bugs]** seo.xml will cause error on same varname for two modules instead of overwriting
- [0000249](http://dotkernel.net/view.php?id=249): **[Bugs]** email sent twice
- [0000275](http://dotkernel.net/view.php?id=275): **[Bugs]** wrong unwritable warning on nginx

 

 

There have been a lot of commits in our SVN repository since the latest release, so we can't cover all changes in this blog post. Please [download Dotkernel 1.8.0 (LTS)](http://www.dotkernel.com/download/?did=41) try it out yourself and tell us what you think.

 

## FAQ

**Q: What does LTS mean for Dotkernel 1.8.0?**
A: LTS stands for Long-term support, a type of special version designed to be supported for longer than normal, which is particularly common for open-source software projects.

**Q: What is the Plugin Architecture introduced in 1.8.0?**
A: Starting with 1.8.0, Dotkernel uses Plugins to make extending the framework easier.

**Q: How does 1.8.0 load faster than previous versions?**
A: It supports APC and File Caching within the framework, so XML files and config files are cached to maximize response speed.

**Q: What is Dot_Request?**
A: Dot_Request is a new class that gives you control over the request data before you use it, so that the variables $_SERVER, $_GET and $_POST are only accessed within controllers.

**Q: What was removed from Dotkernel in 1.8.0?**
A: WURFL integration was removed (mobile device detection is now handled separately, and WURFL can be added as a plugin), and support for multiple SMTP transporters was removed (it can also be added as a plugin).

**Q: What security-related additions does 1.8.0 include?**
A: A security scan in the Admin Dashboard shows recommended settings, admin failed-login notifications are sent to all developers listed in devEmails (not just the first admin), and a new Alert System can notify developers if something goes wrong.
