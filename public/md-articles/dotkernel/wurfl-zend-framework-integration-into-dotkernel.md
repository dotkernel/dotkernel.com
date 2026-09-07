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

[WURFL](http://wurfl.sourceforge.net/) is integrated into Dotkernel, using the [Zend_Http_UserAgent](http://framework.zend.com/manual/1.11/en/zend.http.user-agent.html) class from the latest release [ZF 1.11.0rc1](http://framework.zend.com/download/latest) ( Beta release at the date of this post).

The integration of [WURFL](http://wurfl.sourceforge.net/) into Dotkernel is described below.

Download [WURFL PHP API](http://sourceforge.net/projects/wurfl/files/WURFL%20PHP/1.1/wurfl-php-1.1.tar.gz/download) and unzip it into folder ***wurfl-php-1.1***

- Create folders and make it **writable** by web server
  - ***cache\wurfl\FILE_CACHE_PROVIDER***
  - ***cache\wurfl\FILE_PERSISTENCE_PROVIDER***
- In folder *configs*:
  - Copy ***wurfl-config.xml*** from ***wurfl-php-1.1\examples\resources***
  - Rename ***wurfl-config.xml*** to ***wurfl.xml***
- Edit ***configs\application.ini***file; add these lines:

```
resources.useragent.wurflapi.wurfl_api_version = "1.1"
resources.useragent.wurflapi.wurfl_lib_dir = APPLICATION_PATH "/library/Wurfl/"
resources.useragent.wurflapi.wurfl_config_file = APPLICATION_PATH "/configs/wurfl.xml"
```

- Create folder ***externals\wurfl***, and copy the following files from:
  - ***wurfl-php-1.1\examples\resources\web_browsers_patch.xml***
  - ***wurfl-php-1.1\examples\resources\wurfl-regression.zip*** and rename it ***wurfl.zip*** or download the [latest wurfl zip](http://sourceforge.net/projects/wurfl/files/WURFL/) database and rename it ***wurfl.zip***
- Copy the contents of the folder ***wurfl-php-1.1\WURFL*** to ***library\Wurfl***

[WURFL](http://wurfl.sourceforge.net/) is integrated into Dotkernel in the mobile module, but to access WURFL configuration, use

```
$userAgent = new Zend_Http_UserAgent($config->resources->useragent);
$device = $userAgent->getDevice();
```

*$userAgent->getDevice()* returns all the relevant information about the current user agent *($_SERVER['HTTP_USER_AGENT'])*

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
A: Instantiate a Zend_Http_UserAgent with the useragent config, then call getDevice() on it, for example $userAgent = new Zend_Http_UserAgent($config->resources->useragent); $device = $userAgent->getDevice();. getDevice() returns all the relevant information about the current user agent.
