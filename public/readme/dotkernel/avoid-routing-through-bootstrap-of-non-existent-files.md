---
title: "Avoid routing through bootstrap of non existent files"
description: "How to stop missing static files from being routed through the bootstrap and logging out users whose session regenerates on each request."
author: "admin"
date_published: "2013-11-29"
canonical_url: "https://www.dotkernel.com/dotkernel/avoid-routing-through-bootstrap-of-non-existent-files/"
category: "Dotkernel"
language: "en"
---

# Avoid routing through bootstrap of non existent files

In some cases you may encounter missing files: images, CSS, or JS files. All those missing files are processed by the current bootstrap: `index.php`.

If the session is set to regenerate on each request, as a normal security measure, the currently logged-in user is logged off, because the session ID is different now.

To avoid this, below the following line:

```
RewriteEngine On
```

add the line:

```
RewriteCond %{REQUEST_FILENAME} (\.gif|\.jpg|\.png|\.css|\.js)$
```

Save, and don't forget to test.

## FAQ

**Q: What problem does this fix address?**
A: When missing static files (images, CSS, JS) are routed through the bootstrap (index.php) and the session is set to regenerate on each request, the currently logged-in user gets logged off because the session ID changes.

**Q: What is the fix?**
A: Below the "RewriteEngine On" line, add a RewriteCond matching file extensions like .gif, .jpg, .png, .css, and .js, so requests for those files are not routed through the bootstrap.
