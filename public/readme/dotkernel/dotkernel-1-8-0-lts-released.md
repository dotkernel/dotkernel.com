---
title: "DotKernel 1.8.0 LTS Released"
description: "DotKernel 1.8.0 (LTS) introduces a plugin architecture, a redesigned mobile-friendly admin and frontend, APC/File caching for speed, a new Dot_Request class, and several security and alerting improvements."
author: "Gabi DJ"
date_published: "2015-06-08"
canonical_url: "https://new.dotkernel.com/dotkernel/dotkernel-1-8-0-lts-released/"
category: "Dotkernel"
language: "en"
---

# DotKernel 1.8.0 LTS Released

## TL;DR

DotKernel 1.8.0 (LTS) was released with a new Plugin Architecture, a redesigned and mobile-friendly frontend, APC/File caching for faster response times, a new Dot_Request class, and multiple security and alerting improvements. Some features (WURFL integration, multiple SMTP transporters) were removed from core and made available as plugins instead.

## What is LTS?

Long-term support (LTS) is a type of special version or edition of software designed to be supported for a longer than normal period. It's particularly applicable to open-source software projects. The 1.8.0 LTS release itself contains many bug fixes, some refactoring, and a few minor features.

## Highlights of 1.8.0 (LTS)

### Plugin Architecture

Starting with 1.8.0, DotKernel uses Plugins to make extending the framework easier.

### New design

The admin module was redesigned and the frontend module is now mobile-friendly, while the separate mobile module remains available.

### Loads faster

The framework now supports APC and File Caching, with all XML and config files cached in order to maximize response speed.

### Easier request handling

A new class, `Dot_Request`, gives control over the request data before use — for example, so that `$_SERVER`, `$_GET`, and `$_POST` are only accessed from within controllers.

### Features added

- API with Rate Limit — a simple API with single-key authentication and a basic rate limit implementation (configurable in `/configs/application.ini`, section `params.api`)
- Cache System — built on Zend_Cache backends, providing caching within DotKernel and in library code

### Other changes

- Removed WURFL integration — mobile device detection is now handled separately; WURFL can be added as a plugin
- Removed support for multiple SMTP transporters — it can be added as a plugin
- Security scan in the Admin Dashboard, showing recommended (especially security-related) settings
- Admin failed-login notifications are now sent to all developers listed in `devEmails` (within the `settings` table), not just the first admin
- Alert System — alerts can be sent to all developers to notify them if something goes wrong

### Bug fixes

- `seo.xml` caused an error when two modules used the same variable name instead of overwriting it
- Emails were sent twice
- A wrong "unwritable" warning appeared on nginx

## Scale of the release

There were a lot of commits in the SVN repository since the previous release, so the blog post only covers the highlights.

## FAQ

**Q: What does LTS mean for DotKernel 1.8.0?**
A: LTS stands for Long-term support, a type of special version designed to be supported for longer than normal, which is particularly common for open-source software projects.

**Q: What is the Plugin Architecture introduced in 1.8.0?**
A: Starting with 1.8.0, DotKernel uses Plugins to make extending the framework easier.

**Q: How does 1.8.0 load faster than previous versions?**
A: It supports APC and File Caching within the framework, so XML files and config files are cached to maximize response speed.

**Q: What is Dot_Request?**
A: Dot_Request is a new class that gives you control over the request data before you use it, so that the variables $_SERVER, $_GET and $_POST are only accessed within controllers.

**Q: What was removed from DotKernel in 1.8.0?**
A: WURFL integration was removed (mobile device detection is now handled separately, and WURFL can be added as a plugin), and support for multiple SMTP transporters was removed (it can also be added as a plugin).

**Q: What security-related additions does 1.8.0 include?**
A: A security scan in the Admin Dashboard shows recommended settings, admin failed-login notifications are sent to all developers listed in devEmails (not just the first admin), and a new Alert System can notify developers if something goes wrong.

## Resources

- What is LTS: http://www.dotkernel.com/long-term-support
- Caching in DotKernel using Zend Framework: http://www.dotkernel.com/dotkernel/caching-in-dotkernel-using-zend-framework/
- DotKernel reserved variable names for caching: http://www.dotkernel.com/dotkernel/dotkernel-reserved-variable-names-for-caching/
- How to use alerts in DotKernel: http://www.dotkernel.com/dotkernel/how-to-use-alerts-in-dotkernel/
- DotKernel 1.8.0 (LTS) download: http://www.dotkernel.com/download/?did=41
