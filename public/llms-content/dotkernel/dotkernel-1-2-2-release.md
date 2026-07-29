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

## Bug fixes in 1.2.2

- **31** - captcha errors try/catch
- **32** - pagination issue
- **33** - admin wrong link
- **34** - Acunetix scan results from July 24th (notices and one fatal error)
- **35** - update copyright line in files

**Note:** because of bug 35, all PHP files changed in this release.

## Upgrading

To get only the changed files from 1.2.1 to 1.2.2, download the upgrade package (linked in the post) instead of the full distribution.
Full details are available in the ChangeLog 1.2.2, and further changes can be tracked on the Dotkernel Tracker or Dotkernel WebSVN.

Note also that Dotkernel 1.2.1 had been released a few days earlier, on July 22, 2010, with its own ChangeLog and upgrade package.

## FAQ

**Q: What does the Dotkernel 1.2.2 release include?**
A: It's a bug-fix release that closes five issues: captcha error handling (try/catch), a pagination issue, a wrong admin link, notices and a fatal error found by an Acunetix scan, and an update to the copyright line in files.

**Q: Why did all PHP files change in the 1.2.2 release?**
A: Because of the fix for bug 35, which updated the copyright line, every PHP file in the codebase was touched, which is why the note in the post warns that all PHP files have changed.

**Q: How can I upgrade from a previous version to 1.2.2?**
A: You can download just the changed files from 1.2.1 to 1.2.2 using the upgrade package linked in the post, or check the ChangeLog 1.2.2 for full details of what changed.

## Resources

- ChangeLog 1.2.2 (linked in the original post as `../changelog/1-2-2/`)
- Upgrade package for 1.2.2 (linked in the original post as `../download/?did=17`)
- Dotkernel Tracker: http://www.dotkernel.net/
- Dotkernel WebSVN: http://websvn.dotkernel.net/listing.php?repname=Dotkernel+ver.+1
- ChangeLog 1.2.1 (linked in the original post as `../changelog/1-2-1/`)
- Upgrade package for 1.2.1 (linked in the original post as `../download/?did=14`)
