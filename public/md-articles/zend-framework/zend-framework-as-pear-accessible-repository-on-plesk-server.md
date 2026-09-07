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

Why we want to install ZF as PEAR ? Because is too boring and time consuming to move all ZF files up and down for each script you want to install , there are a lot of files.

Also that way we can forget about the need to update ZF at latest versions, and keep tracks of which version and on which server we have ZF.  Of course, backward compatibility  can be an issue in future ZF releases ( like 2.0 branch for PHP > 5.3)

1. Install PEAR if is not installed  already .
2. Follow the instructions for [ZF PEAR](http://code.google.com/p/zend/). Then simply use: *pear install zend/zend*
3. Create a vhosts.conf file in /var/www/vhosts/dotkernel.com/conf  or where is your vhost configuration folder located.
4. In that file, remove the open_basedir :
5. rebuild all vhosts:  /usr/local/psa/admin/sbin/websrvmng -a
6. Restart httpd
7. Call directly the preloader: require_once 'Zend/Loader/Autoloader.php';
