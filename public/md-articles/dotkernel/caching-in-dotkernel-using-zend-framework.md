---
title: "Caching in Dotkernel using Zend Framework"
description: "How Dotkernel's upcoming 1.8 cache layer stores router, ACL, menu and other data between requests, using APC/APCU or file storage."
author: "Gabi DJ"
date_published: "2015-01-29"
canonical_url: "https://www.dotkernel.com/dotkernel/caching-in-dotkernel-using-zend-framework/"
category: "Dotkernel"
language: "en"
---

# Caching in Dotkernel using Zend Framework

## TL;DR
Loading configuration and settings from XML files on every request is expensive, both due to hard-drive latency and XML parsing overhead.
Dotkernel 1.8 implements a cache layer for router, acl_role, menu, options (including seo_xml), browser_xml, os_xml and test data, with a choice of APC/APCU or file-based storage.

## 1. Configuring the cache

The configuration is set from `/configs/application.ini`: whether caching is enabled, how long the cache stays valid, the cache namespace, and the storage provider (File or APC).
The article recommends disabling the cache in development mode.
See [Configuring the Cache in Dotkernel](http://www.dotkernel.com/dotkernel/configuring-the-cache-in-dotkernel/) for more details.

## 2. Using the cache

The cache is automatically loaded during initialization and stored in the Registry — loading it manually is not needed because it's already loaded on kernel initialization (see `Dot_Kernel::initialize($startTime)`).
If you want to use caching outside of that normal initialization, load it with:

```php
Dot_Cache::loadCache();
```

Note: the cache key must match a specific RegEx pattern.

Example of object caching:

```php
$id = 'MyCachedKey';
$obj = new stdClass();
$obj->text = 'I am a cached text';

// saving an object
Dot_Cache::save(obj, $id);

// loading the object
$value = Dot_Cache::load($id);

// checking if we have the object in cache
if ($value !== false) {
     // assuming we only need the text value from the object
     echo $value->text;
} else {
     echo 'no value cached for '. $id ;
}
```

## FAQ

**Q: What data does Dotkernel's cache layer store?**
A: Router, acl_role, menu, options (including seo_xml), browser_xml, os_xml, and test data between requests.

**Q: What storage providers are available for the cache?**
A: Two cache factories to choose from: APC (or APCU for newer PHP installations) and File.

**Q: Where is the cache configured?**
A: In /configs/application.ini, where you can enable or disable caching, set how long the cache stays valid, choose the cache namespace, and pick the storage provider (File or APC).
The article recommends disabling the cache in development mode.

**Q: Do you need to manually load the cache engine?**
A: No, it's automatically loaded during kernel initialization (Dot_Kernel::initialize()).
Manually calling Dot_Cache::loadCache() is only needed if you want to use caching outside of that normal initialization.

**Q: Can you cache PHP objects, not just simple values?**
A: Yes, the article shows an example of saving and loading a stdClass object using Dot_Cache::save() and Dot_Cache::load().

## Resources

- [Dotkernel Reserved Variable Names for Caching](http://www.dotkernel.com/dotkernel/dotkernel-reserved-variable-names-for-caching)
- [Configuring the Cache in Dotkernel](http://www.dotkernel.com/dotkernel/configuring-the-cache-in-dotkernel/)
