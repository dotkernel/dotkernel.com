---
title: "Dotkernel 1.3.0 release"
description: "Dotkernel 1.3.0 adds an admin skin switcher, a way to protect member-only links, and reorganizes resource.xml into route.xml and dots.xml, at the cost of backward compatibility."
author: "Teo"
date_published: "2010-10-15"
canonical_url: "https://www.dotkernel.com/dotkernel/dotkernel-1-3-0-release/"
category: "Dotkernel"
language: "en"
---

# Dotkernel 1.3.0 release

## TL;DR
Dotkernel 1.3.0 brings a switchable admin skin, a way to protect member-only pages, a rename of Dot_Sessions, and a reorganization of resource.xml into route.xml and dots.xml.
Because of that XML reorganization, 1.3.0 is not backward compatible with earlier versions.

[Dotkernel 1.3.0](../download/?did=23) is released at last. It contains important changes and new features.

- [64](http://www.dotkernel.net/view.php?id=64): **[Feature]** Skin switcher in admin - closed.

The admin skin can be customized. There are several readymade skins like: *blue*, *brown*, *gray* and *green*. To set the admin skin, change the value of *settings.admin.skin* from application (e.g. *settings.admin.skin = green*).

- [76](http://www.dotkernel.net/view.php?id=76): **[Bugs]** Want-Url in frontend - closed.

To protect a link that is accessible by members only, add this line in the controller file to protect what is below it: *Dot_Auth::checkIdentity();*

- [77](http://www.dotkernel.net/view.php?id=77): **[Bugs]** Dot_Sessions / rename - closed. - [70](http://www.dotkernel.net/view.php?id=70): **[Bugs]** Menu issue in Admin and frontend - closed. - [73](http://www.dotkernel.net/view.php?id=73): **[Bugs]** Naming consistency Upper-lower case in url - closed. - [71](http://www.dotkernel.net/view.php?id=71): **[Bugs]** XSS forgot password - closed. - [72](http://www.dotkernel.net/view.php?id=72): **[Bugs]** scan result Oct 12th - closed. - [63](http://www.dotkernel.net/view.php?id=63): **[Bugs]** Scan results Oct 1, 2010 on 1.3.0 RC - closed. - [69](http://www.dotkernel.net/view.php?id=69): **[Bugs]** reorganization of XMl files - closed.

Some xml files from configs folder have been changed to encompass the current needs of Dotkernel. *resource.xml* has been deleted and its content split between *route.xml* and *dots.xml*. Check the manual to find more about [route.xml](../docs/router-xml/) and [dots.xml](../docs/dots-xml/)

- [67](http://www.dotkernel.net/view.php?id=67): **[Bugs]** Security Issue / test controller - closed. - [68](http://www.dotkernel.net/view.php?id=68): **[Bugs]** The canonical URL isn't escaped - closed. - [66](http://www.dotkernel.net/view.php?id=66): **[Bugs]** Blank line at the beginning of every file in the frontend - closed. - [60](http://www.dotkernel.net/view.php?id=60): **[Bugs]** 1.3.0 as Release Candidate Friday Oct 1st - closed. - [62](http://www.dotkernel.net/view.php?id=62): **[Bugs]** Admin text box class dojo - closed. - [55](http://www.dotkernel.net/view.php?id=55): **[Bugs]** geoIP extension: record by name + list in dashboard admin GEOIP version and build - closed. - [59](http://www.dotkernel.net/view.php?id=59): **[Bugs]** Admin listings # - closed. - [61](http://www.dotkernel.net/view.php?id=61): **[Bugs]** Admin Add Transporter not working - closed. - [58](http://www.dotkernel.net/view.php?id=58): **[Bugs]** Admin : list stuff, div float - closed. - [57](http://www.dotkernel.net/view.php?id=57): **[Bugs]** OS name on admin/on mouse over - closed. - [53](http://www.dotkernel.net/view.php?id=53): **[Bugs]** drop-down list in admin/user logins - closed. - [52](http://www.dotkernel.net/view.php?id=52): **[Bugs]** Admin hide debug bar in Login page - closed.

For more details see [ChangeLog 1.3.0](../changelog/1-3-0/).

**Note***: because of the bug [69](http://www.dotkernel.net/view.php?id=69), this release is not compatible with the previous versions. To see what else has changed, check [Dotkernel Tracker](http://www.dotkernel.net/) or [Dotkernel WebSVN](http://websvn.dotkernel.net/listing.php?repname=Dotkernel).

## FAQ

**Q: What is new in the admin interface in Dotkernel 1.3.0?**
A: 1.3.0 adds a skin switcher for the admin, with several ready-made skins (blue, brown, gray, green) that can be set via the settings.admin.skin configuration value.

**Q: How do I protect a page so only logged-in members can access it?**
A: Add the line Dot_Auth::checkIdentity(); in the controller file above the code you want to protect - everything below that line requires the visitor to be logged in.

**Q: What happened to resource.xml in this release?**
A: resource.xml was deleted and its content split between two new files, route.xml and dots.xml.

**Q: Is Dotkernel 1.3.0 backward compatible with earlier versions?**
A: No. Because of the XML file reorganization (bug 69), 1.3.0 is not compatible with previous versions.
