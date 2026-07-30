---
title: "htaccess 301 redirect non-www to www"
description: "How to configure .htaccess RewriteCond/RewriteRule directives to redirect a non-www domain to its www version, or vice versa."
author: "admin"
date_published: "2011-03-14"
canonical_url: "https://www.dotkernel.com/best-practice/htaccess-301-redirect-non-www-to-www/"
category: "Best Practice"
language: "en"
---

# htaccess 301 redirect non-www to www

## Redirect non-www to www

To always redirect users to the www site (for example: `http://dotboost.com` to `http://www.dotboost.com`), add the following lines to `.htaccess`, right after `RewriteEngine On`:

```shell
RewriteCond %{HTTP_HOST} ^dotboost.com
RewriteRule ^(.*)$ http://www.dotboost.com/$1
```

## Redirect www to non-www

If, instead, you want to redirect `http://www.dotboost.com` to `http://dotboost.com`, add the following lines instead:

```shell
RewriteCond %{HTTP_HOST} ^www.dotboost.com
RewriteRule ^(.*)$ http://dotboost.com/$1
```

Replace `dotboost.com` with your site's domain in either case.

## FAQ

**Q: How do I redirect a non-www domain to www using .htaccess?**
A: Add `RewriteCond %{HTTP_HOST} ^dotboost.com` and `RewriteRule ^(.*)$ http://www.dotboost.com/$1` to your .htaccess file, right after `RewriteEngine On`, replacing dotboost.com with your own domain.

**Q: How do I redirect a www domain to non-www instead?**
A: Add `RewriteCond %{HTTP_HOST} ^www.dotboost.com` and `RewriteRule ^(.*)$ http://dotboost.com/$1` instead, again replacing dotboost.com with your own domain.
