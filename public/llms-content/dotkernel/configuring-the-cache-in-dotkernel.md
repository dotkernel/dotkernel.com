---
title: "Configuring the Cache in Dotkernel"
description: "A configuration guide for Dotkernel's Zend Framework Cache-based caching layer, covering the main frontend settings and the optional per-backend settings."
author: "Gabi DJ"
date_published: "2015-01-29"
canonical_url: "https://www.dotkernel.com/dotkernel/configuring-the-cache-in-dotkernel/"
category: "Dotkernel"
language: "en"
---

# Configuring the Cache in Dotkernel

## TL;DR

Dotkernel's caching layer is built on Zend Framework Cache and is configured through `cache.*` settings in `application.ini`.
The main frontend settings control whether caching is enabled, which cache service to use, the namespace prefix, and how long entries live.
Optional backend-specific settings (like the file cache directory) are recommended so that separate projects don't accidentally share the same cache.

This article contains the Dotkernel cache layer configuration guide.
The Dotkernel Caching Layer is based on Zend Framework Cache; more configuration options can be found at the following links:

- [Zend Framework Cache Frontends](http://framework.zend.com/manual/1.12/en/zend.cache.frontends.html)
- [Zend Framework Cache Backends](http://framework.zend.com/manual/1.12/en/zend.cache.backends.html)

## Main Cache Settings (Cache Frontend)

The main cache settings within the application.ini file should look like this:

```ini
cache.enable = true
cache.factory = "apc"
cache.lifetime = "86400"
cache.namespace = "dotkernel"
```

The cache.enable option can be used to disable caching, mostly used in the development stage.
The cache.factory value will be the cache service we want to use: file or apc.
The cache.namespace will be the cache variables prefix, and the cache.lifetime value will define how long the cached variables will be usable before they need to be re-cached.

## Individual Cache Settings (Cache Backend)

The individual cache settings are optional, but it's highly recommended that you have these values set, otherwise other projects might use the same cache.

```ini
; file caching settings
cache.file.cache_dir = APPLICATION_PATH "/cache"
cache.file.cache_file_perm = 0600
```

For more settings and caching alternatives, see the Zend Framework Cache links at the beginning of the article.
The setting pattern and sample are below:

```ini
cache.BACKEND_NAME.SETTING = "VALUE"
; example:
cache.file.file_name_prefix = "Dotkernel"
```

## FAQ

**Q: What is Dotkernel's caching layer based on?**
A: It's based on Zend Framework Cache, configured through settings in application.ini, with more configuration options available at the Zend Framework Cache Frontends and Backends documentation links given in the article.

**Q: What does the cache.enable setting do?**
A: It can be used to disable caching, which is mostly useful during the development stage.

**Q: What values can cache.factory take?**
A: The cache.factory value is the cache service to use, and the article lists two options: file or apc.

**Q: Why bother setting the individual/backend cache settings like cache.file.cache_dir?**
A: These settings are optional, but the article highly recommends setting them, otherwise other projects might end up using the same cache.
