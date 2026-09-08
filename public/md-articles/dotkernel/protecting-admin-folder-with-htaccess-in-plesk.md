---
title: "Protecting admin folder with .htaccess in Plesk"
description: "How to protect the /admin folder with HTTP Basic Auth in a Plesk vhost.conf file, and how to apply the change and finish setup with .htpasswd."
author: "admin"
date_published: "2011-06-15"
canonical_url: "https://www.dotkernel.com/dotkernel/protecting-admin-folder-with-htaccess-in-plesk/"
category: "Dotkernel"
language: "en"
---

# Protecting admin folder with .htaccess in Plesk

## Steps

1. In `/var/www/vhosts/exampledomain.com/conf/vhost.conf`, add a Location block for /admin:

```shell
<Location /admin>
   AuthType Basic
   AuthName "My Site Admin"
   AuthUserFile /var/www/vhosts/exampledomain.com/conf/.htpasswd
   Require valid-user
</Location>
```

2. Notify the server that vhost.conf has been added/changed:

```shell
/usr/local/psa/admin/sbin/websrvmng -a
```

3. Create the `.htpasswd` file and upload it to `/var/www/vhosts/exampledomain.com/conf/`.

## FAQ

**Q: How do I protect the /admin folder with .htaccess in Plesk?**
A: Add a Location block for /admin to the vhost.conf file (e.g. /var/www/vhosts/exampledomain.com/conf/vhost.conf) using AuthType Basic, an AuthName, an AuthUserFile pointing to a .htpasswd file, and Require valid-user.

**Q: After editing vhost.conf, how do I apply the change and finish setup in Plesk?**
A: Notify the server that vhost.conf has changed by running `/usr/local/psa/admin/sbin/websrvmng -a`, then create the .htpasswd file and upload it to the same conf directory referenced in the AuthUserFile directive.
