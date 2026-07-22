---
title: "DotKernel Reserved Variable Names for Caching"
description: "A reference of the variables DotKernel caches — router, ACL role, menu, options, and browser/OS data — and the cache keys they use."
author: "Gabi DJ"
date_published: "2015-01-29"
canonical_url: "https://www.dotkernel.com/dotkernel/dotkernel-reserved-variable-names-for-caching/"
category: "Dotkernel"
language: "en"
---

# DotKernel Reserved Variable Names for Caching

## TL;DR

This article is a follow-up to "Caching in DotKernel Using Zend Framework Cache" and lists the variables DotKernel caches, along with the exact cache key each one uses.

## Cached variables

| Item | Source | Cache key(s) |
|---|---|---|
| Router | Loads routes (modules, controllers, actions) settings from `router.xml` | `router` |
| Auth / ACL Role | `Dot_Auth`'s `/configs/acl/role.xml`, defining user rights | `acl_role` |
| Menu | `menu.xml` from the current module | `admin_menu`, `frontend_menu` |
| Options | Files found in `/configs/dots/` | `option_MODULE_CONTROLLER`, e.g. `option_admin_Admin`, `option_frontend_Page`, `option_admin_seo` (seo is lowercase) |
| Browser & OS | `browser.xml` and `os.xml` in `/configs/useragent/`, identifying browser/OS name, icon, and type | `browser_xml`, `os_xml` |

## Important note

Be careful when changing the XML files or values that get cached, as they remain cached. If a change appears to have no effect, you must clear the cache or rewrite that value in the cache.

## FAQ

**Q: What cache key is used for the router?**
A: The router, which loads routes settings from router.xml, is cached as `router`.

**Q: What cache key stores the ACL role definitions?**
A: The role.xml file (used by Dot_Auth, from /configs/acl/role.xml, to define user rights) is cached as `acl_role`.

**Q: How is the menu cached?**
A: The menu.xml from the current module is cached as `admin_menu` and `frontend_menu`.

**Q: What naming pattern do cached options use?**
A: `option_MODULE_CONTROLLER`, where MODULE is the current module and CONTROLLER is the current controller or "seo" — for example `option_admin_Admin`, `option_frontend_Page`, and `option_admin_seo` (seo is lowercase).

**Q: What happens if you change browser.xml, os.xml, or other cached config files?**
A: The change won't take effect on its own, since values like browser_xml and os_xml remain cached — you must clear the cache or rewrite that value in cache to see the change take effect.

## Resources

- [Caching in DotKernel Using Zend Framework Cache](http://www.dotkernel.com/dotkernel/caching-in-dotkernel-using-zend-framework)
- [router.xml documentation](http://www.dotkernel.com/docs/router-xml/)
- [Module Structure](http://www.dotkernel.com/docs/module-structure/)
- [menu.xml documentation](http://www.dotkernel.com/docs/menu-xml/)
