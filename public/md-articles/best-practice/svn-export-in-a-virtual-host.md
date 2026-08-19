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

## Steps

1. Make sure Subversion is installed on the host by running `svn --version`.
If you don't get a "command not found" message, it's installed; otherwise, install it.
2. Go to the directory where you want to export the contents of the repository (e.g. `cd /var/www/vhosts/example.com/httpdocs` or `cd /home/sitename/public_html`).
3. Run the export command:

```shell
svn export repositoryUrl repositoryUrl
```

Where:

| Parameter | Meaning |
|---|---|
| `-r revisionNumber` | Optional. Exports a specific revision. By default, the latest revision is used. |
| `repositoryUrl` | The repository URL (e.g. `http://example.com/repos/project-name/trunk/`). Remember to add `/trunk/`, or change it appropriately for a branch or tag. |
| `targetDirectory` - `./` | The current directory. |
| `targetDirectory` - `./project-name` | Exports to the `project-name` subdirectory. |
| `targetDirectory` - `/var/www/vhosts/example.com/httpdocs` | Exports to an absolute path. |
| `--force` | Optional. By default SVN will not export into an existing directory; this overrides that. **Be careful, this option can overwrite files.** |

4. For more information, run `svn help export`.

## Examples

```shell
svn export http://v1.dotkernel.net/svn/trunk ./ --force
svn export -r 423 http://v1.dotkernel.net/svn/trunk ./ --force
svn export http://v1.dotkernel.net/svn/trunk /var/www/vhosts/domain.com/httpdocs/dk
```

## Fixing permissions afterward

If the repository was exported using a different user (e.g. root), change the permissions back as root:

```shell
chown -R siteuser.psacln /var/www/vhosts/example.com/httpdocs
```

## FAQ

**Q: How do you check if Subversion is installed on the host?**
A: Run svn --version.
If you don't get a "command not found" message, Subversion is installed; otherwise, you need to install it.

**Q: What is the basic command to export a repository?**
A: The command is svn export repositoryUrl targetDirectory, run from the host where you want to export the repository, ideally using the domain's user rather than root.

**Q: What does the -r option do?**
A: -r revisionNumber is optional and exports a specific revision; by default, the latest revision is used.

**Q: What does the --force option do, and what is the risk?**
A: By default SVN will not export into an existing directory; --force overrides this.
Be careful, since this option can overwrite files.

**Q: How do you fix file permissions if you exported the repository as a different user?**
A: As root, run chown -R siteuser.psacln /var/www/vhosts/example.com/httpdocs to change the permissions back.
