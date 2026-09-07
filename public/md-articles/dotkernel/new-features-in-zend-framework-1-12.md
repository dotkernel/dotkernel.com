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
Per Matthew Weier O'Phinney's announcement, the Zend Framework team made available the first release candidate of the Zend Framework 1.12 series, 1.12.0RC1.
It back ports several ZF2 components to ZF1, removes the WurflApi adapter due to licensing changes, and fixes over 200 reported issues.

According to [Matthew Weier O'Phinney announcement](http://devzone.zend.com/2366/zend-framework-1-12-series-1-12-0rc1-now-available/), Zend Framework team is pleased to announce the immediate availability of the first release candidate of the Zend Framework 1.12 series, 1.12.0RC1

##  New Features

- Zend_Loader changes

> A number of autoloaders and autoloader facilities were back ported from ZF2 to provide performant alternatives to those already available in the 1.X releases.  These include: Zend_Loader_StandardAutoloader, which improves on Zend_Loader_Autoloader by allowing the ability to specify a specific path to associate with a vendor prefix or namespace; Zend_Loader_ClassMapAutoloader, which provides the ability to use lookup tables for autoloading (which are typically the fastest possible way to autoload); and Zend_Loader_AutoloaderFactory, which can both create and update autoloaders for you, as well as register them with spl_autoload_register().
> 
> The Zend_Loader changes were back ported from ZF2 by Matthew Weier O'Phinney

- Zend_EventManager

> Zend_EventManager is a component that allows you to attach and detach listeners to named events, both on a per-instance basis as well as via shared collections; trigger events; and interrupt execution of listeners.
> 
> Zend_EventManager was back ported from ZF2 by Matthew Weier O'Phinney

- Zend_Http_UserAgent_Features_Adapter_Browscap

> This class provides a features adapter that calls get_browser() in order to discover mobile device capabilities to inject into UserAgent device instances.
> 
> Browscap ([http://browsers.garykeith.com/](http://browsers.garykeith.com/)) is an open project dedicated to collecting an disseminating a "database" of browser capabilities. PHP has built-in support for using these files via the get_browser() function. This function requires that your php.ini provides a browscap entry pointing to the PHP-specific php_browscap.ini file which is available at [http://browsers.garykeith.com/stream.asp?PHP_BrowsCapINI](http://browsers.garykeith.com/stream.asp?PHP_BrowsCapINI).
> 
> Zend_Http_UserAgent_Features_Adapter_Browscap was created by Matthew Weier O'Phinney

- Zend_Mobile_Push

> Zend_Mobile_Push is a component for implementing push notifications for the 3 major push notification platforms (Apple (Apns), Google (C2dm) and Microsoft (Mpns).
> 
> Zend_Mobile_Push was contributed by Mike Willbanks.

- Zend_Gdata_Analytics

> Zend_Gdata_Analytics is an extension to Zend_Gdata to allow interaction with Google's Analytics Data Export API. This extension does not encompass any major changes in the overall operation of Zend_Gdata components.
> 
> Zend_Gdata_Analytics was contributed by Daniel Hartmann.

## Removed features

- Zend_Http_UserAgent_Features_Adapter_WurflApi  ( announced few months ago [here](http://www.dotkernel.com/dotkernel/zend-framework-dropped-integration-of-wurfl-adapter/))

> Due to the changes in licensing of WURFL, we have removed the WurflApi adapter. We will be providing the WurflApi adapter to ScientiaMobile so that users of WURFL will still have that option.

## Bug Fixes

> In addition over 200 reported issues in the tracker have been fixed. We'd like to particularly thank Adam Lundrigan, Frank Brückner and Martin Hujer for their efforts in making this happen. Thanks also to the many people who ran the ZF1 unit tests and reported their results!
> 
> For a complete list go here:[http://framework.zend.com/issues/secure/IssueNavigator.jspa?requestId=12877](http://framework.zend.com/issues/secure/IssueNavigator.jspa?requestId=12877)

 

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
