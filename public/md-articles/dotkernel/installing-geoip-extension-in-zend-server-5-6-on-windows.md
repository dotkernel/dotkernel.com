---
title: "Installing GeoIP extension in Zend Server 5.6 on Windows"
description: "Step-by-step guide to testing, downloading, installing and enabling the php_geoip extension and MaxMind databases on Zend Server 5.6 for Windows."
author: "deddu"
date_published: "2013-07-31"
canonical_url: "https://www.dotkernel.com/dotkernel/installing-geoip-extension-in-zend-server-5-6-on-windows/"
category: "Dotkernel"
language: "en"
---

# Installing GeoIP extension in Zend Server 5.6 on Windows

## TL;DR
Test whether php_geoip is already available, and if not, download the correct php_geoip.dll for your PHP build from windows.php.net, copy it into Zend Server's phpext folder, enable it from the Zend Server GUI, and download the MaxMind GeoIP databases.

To test if you have php_geoip extension on your Zend Server, create an php file and copy the following code. This will output true if extension is available or false if not.

```
If your output is false than you have to download an php_geoip.dll file correctly compiled for your zend server version.

For Zend Server 5.6 you can find geoip extension here:

http://windows.php.net/downloads/pecl/releases/geoip/1.0.8/

As you can see there are several files for different architectures and compilers

** NOTE: you can find on what architecture your php is built and the compiler used with phpinfo();

Compiler
MSVC9 (Visual C++ 2008)

Architecture
x86

After you have downloaded the php_geoip.dll you have to copy the file in:

C:\YOUR_LOCATION\Zend\ZendServer\lib\phpext\

Now you have to go in your Zend Server interface, restart php, turn on geoip extension from Server Setup -> Extension and restart php again.

If you get an error loading geoip extension than you picked the wrong dll and you have to try the other ones.

Otherwise your geoip extension is loaded and your test script from the beginning will output "true".

The next step is to download some geoip databases from maxmind.
```

- geoIP.dat
- geoIPCity.dat
- geoIPOrganization.dat
- or anything you need

Copy those .dat files in

C:\YOUR_LOCATION\Zend\ZendServer\bin\

And now you can use geoip functions on your Zend Server 5.6

## FAQ

**Q: How do you test if the php_geoip extension is available on Zend Server?**
A: Create a PHP file with var_dump(function_exists('geoip_database_info'));. It outputs true if the extension is available, or false if it isn't.

**Q: Where can you download the php_geoip extension for Zend Server 5.6?**
A: From http://windows.php.net/downloads/pecl/releases/geoip/1.0.8/, where several php_geoip.dll files are available for different architectures and compilers.

**Q: How do you know which php_geoip.dll file to download?**
A: You can find your PHP's architecture and the compiler it was built with by running phpinfo().

**Q: Where does the php_geoip.dll file need to be copied?**
A: Into C:\YOUR_LOCATION\Zend\ZendServer\lib\phpext\.

**Q: How do you enable the geoip extension after copying the dll?**
A: Go into the Zend Server interface, restart PHP, turn on the geoip extension from Server Setup -> Extension, and restart PHP again. If you get an error loading the extension, you picked the wrong dll and should try another one.

**Q: What else is needed besides the extension itself?**
A: You also need to download geoip databases from MaxMind, such as geoIP.dat, geoIPCity.dat, and geoIPOrganization.dat, and copy those .dat files into C:\YOUR_LOCATION\Zend\ZendServer\bin\.
