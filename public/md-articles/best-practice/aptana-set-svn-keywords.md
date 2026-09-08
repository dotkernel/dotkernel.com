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

In Aptana it's very simple to set the [svn:keywords](http://svnbook.red-bean.com/en/1.4/svn.advanced.props.special.keywords.html) property for a file.

For example if you want to set the svn keyword property ***Id***:

1. In the file where you want to add the svn keyword property write **$Id$**

![](/uploads/article/019f8a80-cc86-73d9-a427-0621b2a55777/id-file-300x235.gif)

2. Right click on the file, then follow Team -> Set Property...**Note**: *Set Property...* will not be active if you haven't first added the file to SVN: *Team*->*Add to Version Controller*

![](/uploads/article/019f8a80-cc86-73d9-a427-0621b2a55777/set-property-300x152.gif)

3. Select **svn:keywords**, and write **Id** in the text field

![](/uploads/article/019f8a80-cc86-73d9-a427-0621b2a55777/svn-keywords-300x298.gif)

When you make the SVN commit of the file, the *$Id$* keyword will be replaced with text in the format shown below:

![](/uploads/article/019f8a80-cc86-73d9-a427-0621b2a55777/id-file-svn-300x141.gif)

## FAQ

**Q: How do you set the svn:keywords property for a file in Aptana?**
A: Write the keyword marker (for example $Id$) in the file, then right click the file and follow Team -> Set Property..., select svn:keywords, and write Id in the text field.

**Q: Why is "Set Property..." not active when I right click the file?**
A: Set Property... will not be active if the file hasn't first been added to SVN.
Use Team -> Add to Version Controller before trying to set the property.

**Q: What happens to the $Id$ keyword after an SVN commit?**
A: After the SVN commit of the file, the $Id$ keyword is replaced with text containing the file's SVN metadata, in a specific format.

## Resources

- [svn:keywords property documentation](http://svnbook.red-bean.com/en/1.4/svn.advanced.props.special.keywords.html)
