---
title: "SVN Export in a virtual host"
description: "How to export the contents of an SVN repository into a virtual host directory using the svn export command."
author: "Adrian"
date_published: "2011-05-30"
canonical_url: "https://www.dotkernel.com/best-practice/svn-export-in-a-virtual-host/"
category: "Best Practice"
language: "en"
---

# SVN Export in a virtual host

## TL;DR
`svn export` lets you export the contents of a repository into a virtual host directory.
The commands should be run in a terminal (e.g. via Putty on Windows) on the target host, ideally using the domain's own user rather than root.

The following commands should be run in the terminal (for example, using Putty in Windows) on the host where you want to export the repository). It's recommended that you run them using the domain's user, not root.

First make sure that Subversion is installed on the host. To check if it is installed, run:

```
svn --version
```

If you don't get a "command not found" message, subversion is installed. Otherwise, you need to install it.

The next step is to go to where you want to export the contents of the repository (eg.: "*cd /var/www/vhosts/example.com/httpdocs*" or "*cd /home/sitename/public_html*").

The command looks like this:

```
svn export repositoryUrl repositoryUrl
```

where:

- **-r revisionNumber** - *optional* - export a specific revision. By default, the latest revision will be used
- **repositoryUrl** - the repository URL (eg: *http://example.com/repos/project-name/trunk/*). Remember to add /trunk/, or change it appropriately if you need to export a certain branch or tag
- **targetDirectory**
  - **./** - means the current directory
  - **./project-name** - will export to the *project-name* subdirectory
  - **/var/www/vhosts/example.com/httpdocs** - will export to an absolute path
- **--force** - *optional* - by default SVN will not export in an existing directory. if you want to override this, you have to use the *force* parameter. **Be careful, this option can overwrite files**

Examples:

```
svn export http://v1.dotkernel.net/svn/trunk ./ --force
svn export -r 423 http://v1.dotkernel.net/svn/trunk ./ --force
svn export http://v1.dotkernel.net/svn/trunk /var/www/vhosts/domain.com/httpdocs/dk
```

For more information, you can run **svn help export**.

If you've exported the repository using a different user (root for example), you can change the permissions back by running the following command as root:

```
chown -R siteuser.psacln /var/www/vhosts/example.com/httpdocs
```

## FAQ

**Q: How do you check if Subversion is installed on the host?**
A: Run svn --version. If you don't get a "command not found" message, Subversion is installed; otherwise, you need to install it.

**Q: What is the basic command to export a repository?**
A: The command is svn export repositoryUrl targetDirectory, run from the host where you want to export the repository, ideally using the domain's user rather than root.

**Q: What does the -r option do?**
A: -r revisionNumber is optional and exports a specific revision; by default, the latest revision is used.

**Q: What does the --force option do, and what is the risk?**
A: By default SVN will not export into an existing directory; --force overrides this. Be careful, since this option can overwrite files.

**Q: How do you fix file permissions if you exported the repository as a different user?**
A: As root, run chown -R siteuser.psacln /var/www/vhosts/example.com/httpdocs to change the permissions back.
