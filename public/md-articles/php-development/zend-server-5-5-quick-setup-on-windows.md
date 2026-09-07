---
title: "Zend Server 5.5 Quick Setup on Windows"
description: "Three quick configuration steps to make a fresh Zend Server 5.5 install on Windows 7 ready for development: enabling mod_rewrite, tuning PHP directives, and enabling APC."
author: "admin"
date_published: "2011-09-07"
canonical_url: "https://www.dotkernel.com/php-development/zend-server-5-5-quick-setup-on-windows/"
category: "PHP Development"
language: "en"
---

# Zend Server 5.5 Quick Setup on Windows

## TL;DR
A fresh Zend Server 5.5.0 install on Windows 7 needs a few quick tweaks before it's ready for development: enabling mod_rewrite in Apache, adjusting a handful of PHP directives, and fixing APC so it actually works even though it's shown as enabled.

In order to make  usable a fresh installation of Zend Server 5.5.0 on Windows 7, and be ready for development , few quick steps are required:

**1. Enable mod_rewrite**

-  Open the file ZEND_INSTALATION_PATH/Apache2/conf/httpd.conf. Change the directive *AllowOverride* fron None to All

- Restart Apache service

**2. PHP fine tunes**

- go to Zend Server administration interface, [http://localhost:10081/ZendServer/](http://localhost:10081/ZendServer/), Server Setup-> Directives, and change the following:

- set timezone:  set ***date*** to *America/New_York*

-set error reporting: **display_errors ** set to **on**, and **error_reporting** to **-1** .

**3. Enable APC**

- APC is listed as been enabled in Zend Server admin interface, but the app dll file is missing, and APC is not working

- download the latest php apc dll file, from [here](http://windows.php.net/downloads/pecl/releases/apc/) . Quite old file at the time of this post, *php_apc-3.1.5-5.3-nts-vc9-x86.zip*, which is the latest Non-thread-safe VC9 file available. Download the archive and extract the file *php_apc.dll* to ZEND_INSTALATION_PATH/ZendServer/lib/phpext/

- open *php.ini* file, from ZEND_INSTALATION_PATH/ZendServer\etc\php.ini , add the line *extension=php_apc.dll*

- Restart Apache service

 

 

## FAQ

**Q: How do you enable mod_rewrite after a fresh Zend Server 5.5 install on Windows?**
A: Open ZEND_INSTALATION_PATH/Apache2/conf/httpd.conf, change the AllowOverride directive from None to All, then restart the Apache service.

**Q: What PHP directive changes are recommended in the Zend Server admin interface?**
A: In the Zend Server administration interface (http://localhost:10081/ZendServer/), under Server Setup -> Directives, set the date timezone to America/New_York, set display_errors to on, and set error_reporting to -1.

**Q: How do you enable APC when it's listed as enabled but not actually working?**
A: Download the php_apc dll file (e.g. php_apc-3.1.5-5.3-nts-vc9-x86.zip, the latest non-thread-safe VC9 build at the time), extract php_apc.dll into ZEND_INSTALATION_PATH/ZendServer/lib/phpext/, add the line extension=php_apc.dll to the php.ini file in ZEND_INSTALATION_PATH/ZendServer/etc/php.ini, and restart the Apache service.
