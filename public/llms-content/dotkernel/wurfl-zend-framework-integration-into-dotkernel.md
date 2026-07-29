---
title: "WURFL Zend Framework Integration into Dotkernel"
description: "Step-by-step tutorial on integrating WURFL into Dotkernel using the Zend_Http_UserAgent class from Zend Framework 1.11.0rc1."
author: "Teo"
date_published: "2010-10-27"
canonical_url: "https://www.dotkernel.com/dotkernel/wurfl-zend-framework-integration-into-dotkernel/"
category: "Dotkernel"
language: "en"
---

# WURFL Zend Framework Integration into Dotkernel

## TL;DR

[WURFL](http://wurfl.sourceforge.net/) is integrated into Dotkernel using the [Zend_Http_UserAgent](http://framework.zend.com/manual/1.11/en/zend.http.user-agent.html) class from [ZF 1.11.0rc1](http://framework.zend.com/download/latest) (the beta release at the time of the post).
This post walks through the required folders, config files, and code to wire it up.

## Installation steps

1. Download the [WURFL PHP API](http://sourceforge.net/projects/wurfl/files/WURFL%20PHP/1.1/wurfl-php-1.1.tar.gz/download) and unzip it into a folder named `wurfl-php-1.1`.
2. Create the following folders and make them writable by the web server:
   - `cache\wurfl\FILE_CACHE_PROVIDER`
   - `cache\wurfl\FILE_PERSISTENCE_PROVIDER`
3. In the `configs` folder:
   - Copy `wurfl-config.xml` from `wurfl-php-1.1\examples\resources`.
   - Rename `wurfl-config.xml` to `wurfl.xml`.
4. Edit the `configs\application.ini` file and add these lines:

```ini
resources.useragent.wurflapi.wurfl_api_version = "1.1"
resources.useragent.wurflapi.wurfl_lib_dir = APPLICATION_PATH "/library/Wurfl/"
resources.useragent.wurflapi.wurfl_config_file = APPLICATION_PATH "/configs/wurfl.xml"
```

5. Create the folder `externals\wurfl`, and copy the following files into it:
   - `wurfl-php-1.1\examples\resources\web_browsers_patch.xml`
   - `wurfl-php-1.1\examples\resources\wurfl-regression.zip`, renamed to `wurfl.zip` (or download the [latest wurfl zip](http://sourceforge.net/projects/wurfl/files/WURFL/) database and rename it `wurfl.zip`).
6. Copy the contents of the folder `wurfl-php-1.1\WURFL` to `library\Wurfl`.

## Using WURFL in Dotkernel

[WURFL](http://wurfl.sourceforge.net/) is integrated into Dotkernel in the mobile module.
To access WURFL configuration:

```php
$userAgent = new Zend_Http_UserAgent($config->resources->useragent);
$device = $userAgent->getDevice();
```

`$userAgent->getDevice()` returns all the relevant information about the current user agent (`$_SERVER`).

## FAQ

**Q: Which Zend Framework class is used to integrate WURFL into Dotkernel?**
A: WURFL is integrated using the Zend_Http_UserAgent class from Zend Framework 1.11.0rc1 (the beta release at the time of the post).

**Q: What is the first step to integrate WURFL into Dotkernel?**
A: Download the WURFL PHP API and unzip it into a folder named wurfl-php-1.1.

**Q: Which folders need to be created and made writable?**
A: Two folders must be created and made writable by the web server: cache/wurfl/FILE_CACHE_PROVIDER and cache/wurfl/FILE_PERSISTENCE_PROVIDER.

**Q: What needs to be added to application.ini?**
A: Three lines need to be added: resources.useragent.wurflapi.wurfl_api_version = "1.1", resources.useragent.wurflapi.wurfl_lib_dir pointing to APPLICATION_PATH "/library/Wurfl/", and resources.useragent.wurflapi.wurfl_config_file pointing to APPLICATION_PATH "/configs/wurfl.xml".

**Q: How do you access WURFL configuration in code?**
A: Instantiate a Zend_Http_UserAgent with the useragent config, then call getDevice() on it. getDevice() returns all the relevant information about the current user agent.

## Resources

- WURFL: http://wurfl.sourceforge.net/
- Zend_Http_UserAgent manual: http://framework.zend.com/manual/1.11/en/zend.http.user-agent.html
- Zend Framework latest download: http://framework.zend.com/download/latest
- WURFL PHP API 1.1 download: http://sourceforge.net/projects/wurfl/files/WURFL%20PHP/1.1/wurfl-php-1.1.tar.gz/download
- Latest WURFL zip database: http://sourceforge.net/projects/wurfl/files/WURFL/
