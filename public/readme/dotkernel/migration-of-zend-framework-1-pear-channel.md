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

The unofficial PEAR channel for Zend Framework 1 was hosted on Google Code, and once Google Code closed, it had to move. Because the repository is over 1 GB, it could not be migrated to GitHub, so a dedicated server was built to host the PEAR channel long-term at pear.dotkernel.com.

## Why the move was needed

- Zend Framework 1 is still used by a lot of projects in production, is still a viable library collection, and runs on PHP7, even though it is only in maintenance/security-patch mode, so cancelling it completely was not an option.
- The team was unable to migrate the project to github.com because the repository is more than 1 GB in size.
- A special server was built to host only the PEAR channel for Zend Framework 1, with a commitment to keep it live and running for the long term.

## PEAR Channel Migration Guide

Use the PEAR installer to switch to the new channel:

1. Remove the installed pear package:

```shell
pear uninstall zend/zend
```

2. Remove the old Google Code channel:

```shell
pear channel-delete zend.googlecode.com/svn
```

3. Discover the new channel:

```shell
pear channel-discover pear.dotkernel.com/zf1/svn
```

4. Install the package:

```shell
pear install zend/zend
```

## FAQ

**Q: Why did the PEAR channel for Zend Framework 1 need to move?**
A: The unofficial PEAR channel for Zend Framework 1 was hosted on Google Code, and once Google Code was closed the channel was forced to move to a new location.

**Q: Why wasn't the project moved to GitHub instead?**
A: The team was unable to migrate the project to github.com because the repository is more than 1 GB in size, so they built a special server dedicated to hosting only the PEAR channel for Zend Framework 1.

**Q: Is Zend Framework 1 still worth using?**
A: Zend Framework 1 is still used by a lot of projects in production, is still a viable library collection, and runs on PHP7, even though it is only in maintenance/security-patch mode.

**Q: How do I switch to the new PEAR channel?**
A: Using the PEAR installer: remove the installed package with `pear uninstall zend/zend`, remove the old Google Code channel with `pear channel-delete zend.googlecode.com/svn`, discover the new channel with `pear channel-discover pear.dotkernel.com/zf1/svn`, and then reinstall the package with `pear install zend/zend`.

## Resources

- [PEAR channel for Zend Framework 1](http://pear.dotkernel.com/)
- [@dotkernel on Twitter](https://twitter.com/dotkernel)
