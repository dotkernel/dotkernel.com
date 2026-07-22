---
title: "Aptana - set SVN keywords"
description: "How to set the svn:keywords property (e.g. Id) for a file in Aptana, so SVN replaces the keyword marker with commit metadata."
author: "Teo"
date_published: "2011-04-04"
canonical_url: "https://www.dotkernel.com/best-practice/aptana-set-svn-keywords/"
category: "Best Practice"
language: "en"
---

# Aptana - set SVN keywords

## Overview

In Aptana it's very simple to set the svn:keywords property for a file. For example, to set the svn keyword property `Id`:

## Steps

1. In the file where the svn keyword property should be added, write `$Id$`.
2. Right click on the file, then follow Team -> Set Property... (Note: "Set Property..." will not be active if the file hasn't first been added to SVN via Team -> Add to Version Controller).
3. Select `svn:keywords`, and write `Id` in the text field.

When the SVN commit of the file is made, the `$Id$` keyword will be replaced with text containing the file's SVN metadata, in a specific format.

## FAQ

**Q: How do you set the svn:keywords property for a file in Aptana?**
A: Write the keyword marker (for example `$Id$`) in the file, then right click the file and follow Team -> Set Property..., select `svn:keywords`, and write `Id` in the text field.

**Q: Why is "Set Property..." not active when I right click the file?**
A: Set Property... will not be active if the file hasn't first been added to SVN. Use Team -> Add to Version Controller before trying to set the property.

**Q: What happens to the $Id$ keyword after an SVN commit?**
A: After the SVN commit of the file, the `$Id$` keyword is replaced with text containing the file's SVN metadata, in a specific format.

## Resources

- [svn:keywords property documentation](http://svnbook.red-bean.com/en/1.4/svn.advanced.props.special.keywords.html)
