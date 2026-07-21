---
title: "DotKernel 1.3.0 release"
description: "DotKernel 1.3.0 adds an admin skin switcher, a way to protect member-only links, and reorganizes resource.xml into route.xml and dots.xml, at the cost of backward compatibility."
author: "Teo"
date_published: "2010-10-15"
canonical_url: "https://new.dotkernel.com/dotkernel/dotkernel-1-3-0-release/"
category: "Dotkernel"
language: "en"
---

# DotKernel 1.3.0 release

## TL;DR

DotKernel 1.3.0 brings a switchable admin skin, a way to protect member-only pages, a rename of Dot_Sessions, and a reorganization of resource.xml into route.xml and dots.xml. Because of that XML reorganization, 1.3.0 is not backward compatible with earlier versions.

## Highlights

### Admin skin switcher

The admin skin can now be customized. Several ready-made skins are available: blue, brown, gray, and green. Set the skin by changing the `settings.admin.skin` value (e.g. `settings.admin.skin = green`).

### Protecting member-only links

To protect a link so only logged-in members can access it, add this line in the controller file above the code that should require login:

```php
Dot_Auth::checkIdentity();
```

### XML reorganization

Some XML files from the configs folder were changed. `resource.xml` was deleted and its content was split between two new files, `route.xml` and `dots.xml`.

### Other closed issues

The release also closed a number of other tracked issues, covering: the Dot_Sessions rename, menu issues in Admin and frontend, URL casing consistency, an XSS issue in the forgot-password flow, several security scan results, admin listing/UI fixes, a GeoIP extension listing feature, and formatting cleanup (blank lines, brace placement) across the frontend files.

## Compatibility note

Because of the XML file reorganization, this release is **not compatible** with previous versions. Further details on what changed are available on the DotKernel Tracker or DotKernel WebSVN.

## FAQ

**Q: What is new in the admin interface in DotKernel 1.3.0?**
A: 1.3.0 adds a skin switcher for the admin, with several ready-made skins (blue, brown, gray, green) that can be set via the settings.admin.skin configuration value.

**Q: How do I protect a page so only logged-in members can access it?**
A: Add the line Dot_Auth::checkIdentity(); in the controller file above the code you want to protect - everything below that line requires the visitor to be logged in.

**Q: What happened to resource.xml in this release?**
A: resource.xml was deleted and its content split between two new files, route.xml and dots.xml.

**Q: Is DotKernel 1.3.0 backward compatible with earlier versions?**
A: No. Because of the XML file reorganization (bug 69), 1.3.0 is not compatible with previous versions.

## Resources

- DotKernel 1.3.0 download (linked in the original post as `../download/?did=23`)
- ChangeLog 1.3.0 (linked in the original post as `../changelog/1-3-0/`)
- route.xml documentation (linked in the original post as `../docs/router-xml/`)
- dots.xml documentation (linked in the original post as `../docs/dots-xml/`)
- DotKernel Tracker: http://www.dotkernel.net/
- DotKernel WebSVN: http://websvn.dotkernel.net/listing.php?repname=DotKernel
