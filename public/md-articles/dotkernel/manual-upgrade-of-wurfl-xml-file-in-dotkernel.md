---
title: "Manual upgrade of WURFL xml file in Dotkernel"
description: "How to manually upgrade the bundled WURFL XML file in Dotkernel now that its license has changed and Dotkernel no longer updates it."
author: "admin"
date_published: "2011-11-22"
canonical_url: "https://www.dotkernel.com/dotkernel/manual-upgrade-of-wurfl-xml-file-in-dotkernel/"
category: "Dotkernel"
language: "en"
---

# Manual upgrade of WURFL xml file in Dotkernel

## TL;DR
Dotkernel Application Framework bundles a WURFL XML file, but it's the last GPL version (from June 2011).
Because of a license change to that WURFL file, Dotkernel will no longer upgrade the bundled file - it must be upgraded manually.

## Steps to manually upgrade

1. Download the `wurfl-2.3.xml.zip` file.
2. Rename it to `wurfl.zip`.
3. Rename the file inside the archive to `wurfl.xml`.
4. Replace `/externals/wurfl/wurfl.zip` with this new `wurfl.zip` file.
5. Download the `web_browsers_patch.xml` file.
6. Replace the `/externals/wurfl/web_browsers_patch.xml` file with this new one.
7. Go to the Admin panel, click on the **empty** wurfl cache link, then rebuild the cache.

Please pay attention to WURFL license changes when performing this upgrade.

## FAQ

**Q: Why does the WURFL xml file need to be manually upgraded in Dotkernel?**
A: Dotkernel bundles an old WURFL XML file (the latest GPL version, from June 2011).
Because of a license change to that WURFL file, the bundled file will no longer be upgraded by Dotkernel, so it must be upgraded manually if you still want to use it.

**Q: What are the main steps to manually upgrade the WURFL file?**
A: Download the wurfl-2.3.xml.zip file, rename it to wurfl.zip, rename the file inside the archive to wurfl.xml, and replace /externals/wurfl/wurfl.zip with this new wurfl.zip file.

**Q: Is there anything else to replace besides wurfl.zip?**
A: Yes.
Download the web_browsers_patch.xml file and replace the /externals/wurfl/web_browsers_patch.xml file with the new one.

**Q: What's the last step after replacing the files?**
A: Go to the Admin panel, click on the "empty" wurfl cache link, then rebuild the cache.
