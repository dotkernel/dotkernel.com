---
title: "SVN keywords setup in PHP IDE ( Zend Studio)"
description: "How to set SVN ignore, bug tracker, and svn:keywords properties per project in the Zend Studio PHP IDE."
author: "admin"
date_published: "2013-02-21"
canonical_url: "https://new.dotkernel.com/best-practice/svn-keywords-setup-in-php-ide-zend-studio/"
category: "Best Practice"
language: "en"
---

# SVN keywords setup in PHP IDE ( Zend Studio)

## TL;DR

For better integration between SVN, the Zend Studio PHP IDE, and a bug tracker, a set of SVN properties must be set for each project. This article lists which properties to set and how.

## Steps

1. Right click on the **project**.
2. Go to **Team -> Set Propriety**.
3. Set `svn:ignore` so local settings aren't committed to the main repository:

```
Name: svn:ignore
Propriety:
*.project
*.prefs
.project
cache
.settings
.buildpath
*.ini
```

4. Set up basic bug tracker integration:

```
Name: bugtracq:label
Propriety: Tracker ID:
```

```
Name: bugtraq:message
Propriety:
```

5. If using a public bug tracker (e.g. Mantis), also set:

```
Name: bugtraq:url
Propriety: http://www.dotkernel.net/view.php?id=%BUGID%
```

For the properties above, apply them **only** to the project folder, **not** recursively.

## Final step (svn:keywords only)

1. Check **Apply property recursively to:**.
2. Select **All resources**.
3. Check **Use filtration by the resource name** and add mask: `*.php`.

## FAQ

**Q: Why set these SVN properties on each project?**
A: They provide better integration of SVN, your PHP IDE (Zend Studio), and a bug tracker of choice, and must be set for each project you have.

**Q: What does the svn:ignore property do here?**
A: It tells SVN to ignore local settings files such as *.project, *.prefs, .project, cache, .settings, .buildpath, and *.ini, since you don't want to commit your local settings to the main repository.

**Q: How do you set up basic bug tracker integration?**
A: Set the bugtracq:label property to "Tracker ID:" and bugtraq:message; if you have a public bug tracker such as Mantis, also set bugtraq:url to a URL pattern like http://www.dotkernel.net/view.php?id=%BUGID%.

**Q: Should these properties be applied recursively?**
A: No. For the properties above, apply them only to the project folder, not recursively.

**Q: How is the svn:keywords property applied differently?**
A: Check "Apply property recursively to:", select "All resources", then check "Use filtration by the resource name" and add the mask *.php.
