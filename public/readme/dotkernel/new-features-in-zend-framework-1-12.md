---
title: "New Features in Zend Framework 1.12"
description: "Overview of Zend Framework 1.12.0RC1, covering new components back ported from ZF2, the removal of the WurflApi adapter, and over 200 bug fixes."
author: "admin"
date_published: "2012-06-18"
canonical_url: "https://www.dotkernel.com/dotkernel/new-features-in-zend-framework-1-12/"
category: "Dotkernel"
language: "en"
---

# New Features in Zend Framework 1.12

## TL;DR

Per Matthew Weier O'Phinney's announcement, the Zend Framework team made available the first release candidate of the Zend Framework 1.12 series, 1.12.0RC1. It back ports several ZF2 components to ZF1, removes the WurflApi adapter due to licensing changes, and fixes over 200 reported issues.

## New Features

| Feature | Description |
|---|---|
| Zend_Loader changes | A number of autoloaders and autoloader facilities were back ported from ZF2, including `Zend_Loader_StandardAutoloader` (improves on `Zend_Loader_Autoloader` by allowing a specific path to be associated with a vendor prefix or namespace), `Zend_Loader_ClassMapAutoloader` (lookup-table based autoloading, typically the fastest method), and `Zend_Loader_AutoloaderFactory` (can create and update autoloaders and register them with `spl_autoload_register()`). Back ported by Matthew Weier O'Phinney. |
| Zend_EventManager | A component that lets you attach and detach listeners to named events, per-instance or via shared collections, trigger events, and interrupt execution of listeners. Back ported by Matthew Weier O'Phinney. |
| Zend_Http_UserAgent_Features_Adapter_Browscap | A features adapter that calls `get_browser()` to discover mobile device capabilities for injection into UserAgent device instances, relying on the Browscap project's `php_browscap.ini` file. Created by Matthew Weier O'Phinney. |
| Zend_Mobile_Push | A component for implementing push notifications across the three major platforms: Apple (APNs), Google (C2DM), and Microsoft (MPNS). Contributed by Mike Willbanks. |
| Zend_Gdata_Analytics | An extension to Zend_Gdata for interacting with Google's Analytics Data Export API; does not change the overall operation of Zend_Gdata components. Contributed by Daniel Hartmann. |

## Removed features

- `Zend_Http_UserAgent_Features_Adapter_WurflApi` was removed due to changes in WURFL's licensing (announced previously). The team planned to provide the WurflApi adapter directly to ScientiaMobile so WURFL users would still have that option.

## Bug Fixes

Over 200 reported issues in the tracker were fixed, with particular thanks to Adam Lundrigan, Frank Brückner and Martin Hujer, as well as everyone who ran the ZF1 unit tests and reported results.

## FAQ

**Q: What was announced for Zend Framework 1.12?**
A: Matthew Weier O'Phinney announced the immediate availability of the first release candidate of the Zend Framework 1.12 series, 1.12.0RC1.

**Q: What changed in Zend_Loader?**
A: A number of autoloaders and autoloader facilities were back ported from ZF2, including Zend_Loader_StandardAutoloader, Zend_Loader_ClassMapAutoloader, and Zend_Loader_AutoloaderFactory, providing performant alternatives to the autoloading facilities already in the 1.X releases.

**Q: What is Zend_EventManager?**
A: Zend_EventManager is a component, also back ported from ZF2, that lets you attach and detach listeners to named events (per-instance or via shared collections), trigger events, and interrupt execution of listeners.

**Q: Why was the WurflApi adapter removed?**
A: Zend_Http_UserAgent_Features_Adapter_WurflApi was removed due to changes in the licensing of WURFL. The team planned to provide the WurflApi adapter to ScientiaMobile so WURFL users would still have that option.

**Q: How many bugs were fixed in this release?**
A: Over 200 reported issues in the tracker were fixed, with particular thanks credited to Adam Lundrigan, Frank Brückner and Martin Hujer, as well as everyone who ran the ZF1 unit tests and reported results.

## Resources

- [Matthew Weier O'Phinney's announcement](http://devzone.zend.com/2366/zend-framework-1-12-series-1-12-0rc1-now-available/)
- [Browscap project](http://browsers.garykeith.com/)
- [php_browscap.ini download](http://browsers.garykeith.com/stream.asp?PHP_BrowsCapINI)
- [Prior WurflApi removal announcement](http://www.dotkernel.com/dotkernel/zend-framework-dropped-integration-of-wurfl-adapter/)
- [Complete issue tracker list](http://framework.zend.com/issues/secure/IssueNavigator.jspa?requestId=12877)
