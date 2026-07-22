---
title: "Adding Windows 10 OS and Browser detection in DotKernel projects"
description: "A guide to installing the patch that adds Windows 8, 8.1 and 10 OS icons and the Microsoft Edge browser icon in DotKernel."
author: "Gabi DJ"
date_published: "2015-09-08"
canonical_url: "https://www.dotkernel.com/dotkernel/adding-windows-10-os-and-browser-detection-in-dotkernel-projects/"
category: "Dotkernel"
language: "en"
---

# Adding Windows 10 OS and Browser detection in DotKernel projects

## TL;DR
DotKernel added Windows 8, 8.1 and 10 OS icons and a Microsoft Edge browser icon, shown in the User and Admin login icons.
This article is the upgrade guide for applying that icon patch.

## Upgrade steps

1. Make sure your project is running version **1.5.0** or **newer**.
2. Download the [patch](http://www.dotkernel.com/download/?did=46).
3. Extract the archive into a folder, e.g. `icons_patch`.
4. Create a backup of your project before continuing (recommended).
5. Copy all the files in the `icons_patch` folder into your DotKernel project.
6. You will be prompted to replace 2 files — replace them and agree to merge the folders' content (other files will be added, not replaced).
7. Clear the cache for changes to take effect, since the OS and browser XMLs are cached (see "DotKernel Reserved Variable Names for Caching", the "Browser & OS" section).
8. You can now delete the `icons_patch` folder, or keep it to patch another project.

## Affected files

```
M /configs/useragent/browser.xml
M /configs/useragent/os.xml
A /images/browsers/edge.png
A /images/os/windows_metro.png
```

`M` stands for **modify**, `A` stands for **add**.

## FAQ

**Q: What DotKernel version is required before applying this patch?**
A: Your project must be running version 1.5.0 or newer.

**Q: Which files does the patch modify or add?**
A: It modifies configs/useragent/browser.xml and configs/useragent/os.xml, and adds images/browsers/edge.png and images/os/windows_metro.png.

**Q: Why do you need to clear the cache after applying the patch?**
A: Because the OS and browser XML files are cached, so the new icons won't show up until the cache is cleared.

**Q: Will applying the patch overwrite existing files?**
A: You'll be prompted to replace 2 files (browser.xml and os.xml) and should agree, and also agree to merge the folders' contents since the other files listed are added rather than replaced.

## Resources

- [Icon patch download](http://www.dotkernel.com/download/?did=46)
- [DotKernel Reserved Variable Names for Caching](http://www.dotkernel.com/dotkernel/dotkernel-reserved-variable-names-for-caching)
