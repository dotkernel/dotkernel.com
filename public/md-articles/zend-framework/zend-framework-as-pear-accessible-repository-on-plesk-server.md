---
title: "Zend Framework as PEAR accessible repository on Plesk server"
description: "How to install Zend Framework as a PEAR-accessible repository on a Plesk server, so it doesn't need to be copied into every project and can be updated centrally."
author: "admin"
date_published: "2008-10-03"
canonical_url: "https://www.dotkernel.com/zend-framework/zend-framework-as-pear-accessible-repository-on-plesk-server/"
category: "Zend Framework"
language: "en"
---

# Zend Framework as PEAR accessible repository on Plesk server

## TL;DR

Rather than copying all of Zend Framework's many files into every project, this article shows how to install ZF as a PEAR-accessible repository on a Plesk server.
This makes it easier to track which ZF version is installed on which server and avoids manually updating each project, though it can introduce backward compatibility concerns in future ZF releases.

Why we want to install ZF as PEAR?
Because it's too boring and time consuming to move all ZF files up and down for each script you want to install, and there are a lot of files.

Also that way we can forget about the need to update ZF at latest versions, and keep track of which version and on which server we have ZF.
Of course, backward compatibility can be an issue in future ZF releases (like the 2.0 branch for PHP > 5.3).

1. Install PEAR if it is not installed already.
2. Follow the instructions for [ZF PEAR](http://code.google.com/p/zend/).
Then simply use: `pear install zend/zend`
3. Create a vhosts.conf file in /var/www/vhosts/dotkernel.com/conf or wherever your vhost configuration folder is located.
4. In that file, remove the open_basedir:

```ini
>php_admin_value open_basedir "/var/www/vhosts/dotkernel.com/httpdocs:/tmp:/usr/share/pear"
```

5. Rebuild all vhosts: /usr/local/psa/admin/sbin/websrvmng -a
6. Restart httpd
7. Call directly the preloader:

```php
> require_once 'Zend/Loader/Autoloader.php';
```

## FAQ

**Q: Why install Zend Framework as a PEAR-accessible repository?**
A: Because manually moving all ZF files up and down for each script you want to install is boring and time consuming, given the large number of files involved.
It also lets you avoid manually updating ZF to the latest version in every project, and keep track of which version is installed on which server.

**Q: What is a potential downside of this approach?**
A: Backward compatibility can be an issue in future ZF releases, such as the 2.0 branch built for PHP > 5.3.

**Q: What is the first step to set this up?**
A: Install PEAR if it isn't installed already, then follow the instructions for ZF PEAR and run `pear install zend/zend`.

**Q: What needs to change in the vhosts configuration?**
A: Create a vhosts.conf file in your vhost configuration folder (e.g. /var/www/vhosts/dotkernel.com/conf), remove the open_basedir restriction, rebuild all vhosts with websrvmng -a, and restart httpd.

**Q: How do you load Zend Framework classes after this setup?**
A: By calling the preloader directly, e.g. `require_once 'Zend/Loader/Autoloader.php';`
