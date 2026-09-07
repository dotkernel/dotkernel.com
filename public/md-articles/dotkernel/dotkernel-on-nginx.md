---
title: "Dotkernel on Nginx"
description: "A walkthrough of testing whether Dotkernel runs out of the box on Nginx, and how the Nginx configuration was set up as a substitute for Apache's .htaccess."
author: "admin"
date_published: "2013-06-25"
canonical_url: "https://www.dotkernel.com/dotkernel/dotkernel-on-nginx/"
category: "Dotkernel"
language: "en"
---

# Dotkernel on Nginx

## TL;DR
Since Nginx was becoming the buzzword instead of Apache, this article tests Dotkernel on Nginx and documents the configuration needed: server block settings, a `try_files` directive in place of `.htaccess`, PHP-FPM handling, and protecting the `configs` folder.

Due to the fact that the current buzzword is **Nginx** instead of **Apache**, we decided to test if Dotkernel is running out of the box on it. And how to configure Nginx :-)

1. Installed on a clean Centos 6.3 VPS : Nginx 1.4.1 , PHP 5.4.16 , PHP-FPM
2. Installed PHP modules: APC, GeoIP and such.
3. Installed PEAR and Zend Framework from [http://code.google.com/p/zend/](http://code.google.com/p/zend/)
4. Fine tune php.ini file: date, default charset, include path, etc
5. Create on a remote server a MySql database, allow permissions from Nginx server  IP to connect to it.
6. Now the fine part: editing the **Nginx** config ( in our case */etc/nginx/conf.d/default.conf*)
  - Set server name: server_name nginx.dotkernel.net;
  - Set document root , in the location / area root /var/www/html;
  - Equivalent of the main .htaccess file is try_files $uri $uri/ /index.php; This directive will send all requests to index.php file
  - Set the location for php directive location ~ \.php$ { root /var/www/html; fastcgi_pass 127.0.0.1:9000; fastcgi_index index.php; fastcgi_param SCRIPT_FILENAME /var/www/html$fastcgi_script_name; fastcgi_param APPLICATION_ENV staging; include fastcgi_params; } In the lines above , you can change the value for APPLICATION_ENV into *production* or *development*
  - Protect *configs* folder from web access #protect folders location ~ ^/configs/ { deny all; }
7.   Export a Dotkernel copy from SVN svn export --force http://v1.dotkernel.net/svn/trunk/ /var/www/html
8. Edit ***application.ini*** to reflect the current settings and check if is  protected [http://nginx.dotkernel.net/configs/application.ini](http://nginx.dotkernel.net/configs/application.ini)
9. And walla, site is running on Nginx       [http://nginx.dotkernel.net/](http://nginx.dotkernel.net/) *Admin login is disabled due to security reason.*

## FAQ

**Q: What server stack was used to test Dotkernel on Nginx?**
A: A clean CentOS 6.3 VPS running Nginx 1.4.1, PHP 5.4.16, and PHP-FPM, with the APC and GeoIP PHP modules installed.

**Q: What is the Nginx equivalent of the main .htaccess file?**
A: The directive try_files $uri $uri/ /index.php;, which sends all requests to the index.php file.

**Q: How is the configs folder protected from web access in Nginx?**
A: By adding a location block matching ^/configs/ that returns deny all;.

**Q: How was the Dotkernel codebase deployed onto the server?**
A: It was exported directly from SVN using svn export --force http://v1.dotkernel.net/svn/trunk/ /var/www/html.
