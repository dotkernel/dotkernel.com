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

## Setup steps

1. Installed on a clean CentOS 6.3 VPS: Nginx 1.4.1, PHP 5.4.16, PHP-FPM.
2. Installed PHP modules: APC, GeoIP, and others.
3. Installed PEAR and Zend Framework from the PEAR ZF channel (`http://code.google.com/p/zend/`).
4. Fine-tuned `php.ini`: date, default charset, include path, etc.
5. Created a MySQL database on a remote server and allowed permissions from the Nginx server's IP to connect to it.
6. Edited the Nginx config (`/etc/nginx/conf.d/default.conf`):
   - Set the server name:
     ```
     server_name  nginx.dotkernel.net;
     ```
   - Set the document root, in the `location /` area:
     ```
     root   /var/www/html;
     ```
   - The equivalent of the main `.htaccess` file, sending all requests to `index.php`:
     ```
     try_files    $uri $uri/ /index.php;
     ```
   - Set the location for the PHP directive:
     ```
     location ~ \.php$ {
                root           /var/www/html;
                fastcgi_pass   127.0.0.1:9000;
                fastcgi_index  index.php;
                fastcgi_param  SCRIPT_FILENAME  /var/www/html$fastcgi_script_name;
                fastcgi_param APPLICATION_ENV staging;
                include        fastcgi_params;
     }
     ```
     `APPLICATION_ENV` can be changed to `production` or `development`.
   - Protect the `configs` folder from web access:
     ```
     #protect folders
     location ~ ^/configs/ {
             deny all;
     }
     ```
7. Exported a Dotkernel copy from SVN:
   ```shell
   svn export --force  http://v1.dotkernel.net/svn/trunk/ /var/www/html
   ```
8. Edited `application.ini` to reflect the current settings, and checked that it was protected.
9. Result: the site was running on Nginx.
Admin login was disabled on the demo for security reasons.

## FAQ

**Q: What server stack was used to test Dotkernel on Nginx?**
A: A clean CentOS 6.3 VPS running Nginx 1.4.1, PHP 5.4.16, and PHP-FPM, with the APC and GeoIP PHP modules installed.

**Q: What is the Nginx equivalent of the main .htaccess file?**
A: The directive `try_files $uri $uri/ /index.php;`, which sends all requests to the index.php file.

**Q: How is the configs folder protected from web access in Nginx?**
A: By adding a location block matching `^/configs/` that returns `deny all;`.

**Q: How was the Dotkernel codebase deployed onto the server?**
A: It was exported directly from SVN using `svn export --force http://v1.dotkernel.net/svn/trunk/ /var/www/html`.

## Resources

- [PEAR ZF Channel](http://code.google.com/p/zend/)
- [application.ini example](http://nginx.dotkernel.net/configs/application.ini)
- [Live Nginx demo](http://nginx.dotkernel.net/)
