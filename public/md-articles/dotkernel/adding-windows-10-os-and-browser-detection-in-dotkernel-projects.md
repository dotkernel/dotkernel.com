---
title: "Adding Windows 10 OS and Browser detection in Dotkernel projects"
description: "A guide to installing the patch that adds Windows 8, 8.1 and 10 OS icons and the Microsoft Edge browser icon in Dotkernel."
author: "Gabi DJ"
date_published: "2015-09-08"
canonical_url: "https://www.dotkernel.com/dotkernel/adding-windows-10-os-and-browser-detection-in-dotkernel-projects/"
category: "Dotkernel"
language: "en"
---

# Adding Windows 10 OS and Browser detection in Dotkernel projects

## TL;DR
Dotkernel added Windows 8, 8.1 and 10 OS icons and a Microsoft Edge browser icon, shown in the User and Admin login icons.
This article is the upgrade guide for applying that icon patch.

Recently we have added the Windows 8, 8.1 and 10 OS icon and Microsoft's Edge browser icon.

In this article we will have the icon upgrade guide.

![Icons Patch](/uploads/article/019f8a80-cc47-710e-9ef1-b2257262e376/icons.png)The new Icons listed in User and Admin Logins

 

1. Make sure your project is running on version **1.5.0** or **newer**
2. Download the [patch](http://www.dotkernel.com/download/?did=46)
3. Extract the archive in a folder, let's name it **icons_patch**
4. We recommend creating a backup of your project before you continue
5. Now copy all the files in the **icons_patch** folder in your Dotkernel
6. You will be prompted to replace 2 files, simply replace the files and agree to merge the folders content (files will be added, not replaced this time)
7. You need to clear the cache for changes to take effect, the os and browser xml's are cached. For more information read [this article](http://www.dotkernel.com/dotkernel/dotkernel-reserved-variable-names-for-caching/) (look for **Browser & OS**)
8. You can now delete the **icons_patch** folder or use it to patch another project

List of affected files:

```
M /configs/useragent/browser.xml
M /configs/useragent/os.xml
A /images/browsers/edge.png
A /images/os/windows_metro.png
```

**M** stands for **modify**

**A** stands for **add**

## FAQ

**Q: What Dotkernel version is required before applying this patch?**
A: Your project must be running version 1.5.0 or newer.

**Q: Which files does the patch modify or add?**
A: It modifies configs/useragent/browser.xml and configs/useragent/os.xml, and adds images/browsers/edge.png and images/os/windows_metro.png.

**Q: Why do you need to clear the cache after applying the patch?**
A: Because the OS and browser XML files are cached, so the new icons won't show up until the cache is cleared.

**Q: Will applying the patch overwrite existing files?**
A: You'll be prompted to replace 2 files (browser.xml and os.xml) and should agree, and also agree to merge the folders' contents since the other files listed are added rather than replaced.
