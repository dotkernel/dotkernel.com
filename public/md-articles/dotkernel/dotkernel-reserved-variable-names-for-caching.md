---
title: "Dotkernel Reserved Variable Names for Caching"
description: "A reference of the variables Dotkernel caches - router, ACL role, menu, options, and browser/OS data - and the cache keys they use."
author: "Gabi DJ"
date_published: "2015-01-29"
canonical_url: "https://www.dotkernel.com/dotkernel/dotkernel-reserved-variable-names-for-caching/"
category: "Dotkernel"
language: "en"
---

# Dotkernel Reserved Variable Names for Caching

## TL;DR
This article is a follow-up to "Caching in Dotkernel Using Zend Framework Cache" and lists the variables Dotkernel caches, along with the exact cache key each one uses.

This article is related to: [Caching in Dotkernel with Zend Framework Cache](http://www.dotkernel.com/dotkernel/caching-in-dotkernel-using-zend-framework)

The variables that Dotkernel cache are below:

## Router

Router is the object that load routes (modules, controllers, actions) settings from router.xml file. More info about router: [http://www.dotkernel.com/docs/router-xml/](http://www.dotkernel.com/docs/router-xml/)

The router is cached as ***router.***

## Auth / ACL Role

The object which authorizes user methods (aka Dot_Auth), used in all Dotkernel Applications, Dot_Auth uses */configs/acl/role.xml* to define the users rights.

The *role.xml* file is cached as ***acl_role**.*

## Menu

The menu.xml from current ***module**([what is a module?](http://www.dotkernel.com/docs/module-structure/))* More about menu.xml : *[http://www.dotkernel.com/docs/menu-xml/](http://www.dotkernel.com/docs/menu-xml/)*

The menu is cached as ***admin_menu***, ***frontend_menu*****.**

## Options

The options are the ones found in /configs/dots/ and have the following naming format: **option_MODULE_CONTROLLER**.

MODULE is the current module and CONTROLLER is the current controller or "seo", so the options cache entries will look like:

- option_admin_Admin
- option_frontend_Page
- option_admin_seo (yes, seo is lowercase)

## Browser & OS

The browser.xml and os.xml are used to identify the Browser and OS name, icon, and type. Theese XML files are located in /configs/useragent/.

Browser & OS are cached as ***browser_xml** and **os_xml.***

**Note: Be careful when changing the xml files / values to be cached as they remain cached. If there is no effect that means you must clear the cache or rewrite that value in cache.**

## FAQ

**Q: What cache key is used for the router?**
A: The router, which loads routes settings from router.xml, is cached as router.

**Q: What cache key stores the ACL role definitions?**
A: The role.xml file (used by Dot_Auth, from /configs/acl/role.xml, to define user rights) is cached as acl_role.

**Q: How is the menu cached?**
A: The menu.xml from the current module is cached as admin_menu and frontend_menu.

**Q: What naming pattern do cached options use?**
A: option_MODULE_CONTROLLER, where MODULE is the current module and CONTROLLER is the current controller or "seo" — for example option_admin_Admin, option_frontend_Page, and option_admin_seo (seo is lowercase).

**Q: What happens if you change browser.xml, os.xml, or other cached config files?**
A: The change won't take effect on its own, since values like browser_xml and os_xml remain cached — you must clear the cache or rewrite that value in cache to see the change take effect.
