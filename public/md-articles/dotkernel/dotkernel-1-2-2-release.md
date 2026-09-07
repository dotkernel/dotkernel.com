---
title: "Dotkernel 1.2.2 release"
description: "Dotkernel 1.2.2 is a bug-fix release closing five issues, including captcha error handling, a pagination bug, and a copyright line update that touched every PHP file."
author: "Teo"
date_published: "2010-07-30"
canonical_url: "https://www.dotkernel.com/dotkernel/dotkernel-1-2-2-release/"
category: "Dotkernel"
language: "en"
---

# Dotkernel 1.2.2 release

## TL;DR
Dotkernel 1.2.2 is a bug-fix release that closes five tracked issues.
Because one of the fixes updated the copyright line, every PHP file in the codebase changed, so the full release or the incremental upgrade package is needed.

Yesterday, we released **Dotkernel 1.2.2**. It contains some bug fixes:

- [31](http://www.dotkernel.net/view.php?id=31) – captcha errors try catch
- [32](http://www.dotkernel.net/view.php?id=32) – pagination issue
- [33](http://www.dotkernel.net/view.php?id=33) – Admin wrong link
- [34](http://www.dotkernel.net/view.php?id=34) – Acunetix results July 24th ( notices and one fatal error)
- [35](http://www.dotkernel.net/view.php?id=35) – update copyright line in files

For more details see [ChangeLog 1.2.2](../changelog/1-2-2/). To get only the changed files from 1.2.1 to 1.2.2, download the [upgrade](../download/?did=17) file

**Note***: because of the Bug 35, all php files have changed. To see what else has changed, check the [Dotkernel Tracker](http://www.dotkernel.net/) or the [Dotkernel WebSVN](http://websvn.dotkernel.net/listing.php?repname=Dotkernel+ver.+1) .

*P.S.* On July 22, 2010 we released *Dotkernel 1.2.1.* You can check the [ChangeLog 1.2.1](../changelog/1-2-1/) or download [the upgrade 1.2.1](../download/?did=14) zip file.

## FAQ

**Q: What does the Dotkernel 1.2.2 release include?**
A: Dotkernel 1.2.2 is a bug-fix release that closes five issues: captcha error handling (try/catch), a pagination issue, a wrong admin link, notices and a fatal error found by an Acunetix scan, and an update to the copyright line in files.

**Q: Why did all PHP files change in the 1.2.2 release?**
A: Because of the fix for bug 35, which updated the copyright line, every PHP file in the codebase was touched, which is why the note in the post warns that all PHP files have changed.

**Q: How can I upgrade from a previous version to 1.2.2?**
A: You can download just the changed files from 1.2.1 to 1.2.2 using the upgrade package linked in the post, or check the ChangeLog 1.2.2 for full details of what changed.
