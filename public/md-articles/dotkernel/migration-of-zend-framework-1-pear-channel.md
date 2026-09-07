---
title: "Migration of Zend Framework 1 PEAR channel"
description: "The unofficial PEAR channel for Zend Framework 1 moved from Google Code to a new dedicated server, with instructions on how to switch to the new channel."
author: "admin"
date_published: "2016-05-11"
canonical_url: "https://www.dotkernel.com/dotkernel/migration-of-zend-framework-1-pear-channel/"
category: "Dotkernel"
language: "en"
---

# Migration of Zend Framework 1 PEAR channel

## TL;DR
The unofficial PEAR channel for Zend Framework 1 was hosted on Google Code, and once Google Code closed, it had to move.
Because the repository is over 1 GB, it could not be migrated to GitHub, so a dedicated server was built to host the PEAR channel long-term at pear.dotkernel.com.

The unofficial **PEAR channel for Zend Framework 1** was hosted on Google Code at this location: [ZF Pear](http://code.google.com/p/zend/), but since the closing of Google Code we were forced to move it.

Zend Framework 1 is still used by a lot of  projects in Production, it's still a viable library collection  and it's also  running on  PHP7 ; even if is only in maintenance/security-patch mode, so it's not an option to cancel it completely.

We were unable to migrate the project on github.com since the size of the repository is more then 1 GB. We resorted to building a special server that will host only the PEAR channel for Zend Framework 1 and we are commited to **keep it live and running for the long term**.

Instructions on how to use the new channel can be found below .

## PEAR Channel Migration Guide

Use the PEAR installer:

1. Remove the installed pear package

```
pear uninstall zend/zend
```

2. Remove the old googlecode channel

```
pear channel-delete zend.googlecode.com/svn
```

3. Discover the new channel

```
pear channel-discover pear.dotkernel.com/zf1/svn
```

4. Install the package

```
pear install zend/zend
```

Follow [@dotkernel ](https://twitter.com/dotkernel) in order to be up to date related to new releases.

 

## FAQ

**Q: Why did the PEAR channel for Zend Framework 1 need to move?**
A: The unofficial PEAR channel for Zend Framework 1 was hosted on Google Code, and once Google Code was closed the channel was forced to move to a new location.

**Q: Why wasn't the project moved to GitHub instead?**
A: The team was unable to migrate the project to github.com because the repository is more than 1 GB in size, so they built a special server dedicated to hosting only the PEAR channel for Zend Framework 1.

**Q: Is Zend Framework 1 still worth using?**
A: Zend Framework 1 is still used by a lot of projects in production, is still a viable library collection, and runs on PHP7, even though it is only in maintenance/security-patch mode.

**Q: How do I switch to the new PEAR channel?**
A: Using the PEAR installer: remove the installed package with pear uninstall zend/zend, remove the old Google Code channel with pear channel-delete zend.googlecode.com/svn, discover the new channel with pear channel-discover pear.dotkernel.com/zf1/svn, and then reinstall the package with pear install zend/zend.
