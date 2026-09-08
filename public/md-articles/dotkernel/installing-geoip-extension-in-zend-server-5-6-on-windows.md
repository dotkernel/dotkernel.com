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

## Steps

1. Test if you have the php_geoip extension:

   ```php
   <?php
   var_dump(function_exists('geoip_database_info'));
   ```

   This outputs `true` if the extension is available, or `false` if not.

2. If the output is `false`, download a php_geoip.dll file correctly compiled for your Zend Server version from [http://windows.php.net/downloads/pecl/releases/geoip/1.0.8/](http://windows.php.net/downloads/pecl/releases/geoip/1.0.8/).
There are several files for different architectures and compilers.

3. Find your PHP's architecture and compiler by running `phpinfo();`.
Example values:

   | Field | Value |
   |---|---|
   | Compiler | MSVC9 (Visual C++ 2008) |
   | Architecture | x86 |

4. Copy the downloaded `php_geoip.dll` file into:

   ```
   C:\YOUR_LOCATION\Zend\ZendServer\lib\phpext\
   ```

5. In the Zend Server interface, restart PHP, turn on the geoip extension from **Server Setup -> Extension**, and restart PHP again.

   If you get an error loading the geoip extension, you picked the wrong dll - try one of the others.

   Otherwise, the extension is loaded and the test script from step 1 will output `true`.

6. Download GeoIP databases from MaxMind, as needed:
   - geoIP.dat
   - geoIPCity.dat
   - geoIPOrganization.dat
   - or anything else you need

7. Copy those `.dat` files into:

   ```
   C:\YOUR_LOCATION\Zend\ZendServer\bin\
   ```

You can now use geoip functions on Zend Server 5.6.

## FAQ

**Q: How do you test if the php_geoip extension is available on Zend Server?**
A: Create a PHP file with `var_dump(function_exists('geoip_database_info'));`.
It outputs true if the extension is available, or false if it isn't.

**Q: Where can you download the php_geoip extension for Zend Server 5.6?**
A: From http://windows.php.net/downloads/pecl/releases/geoip/1.0.8/, where several php_geoip.dll files are available for different architectures and compilers.

**Q: How do you know which php_geoip.dll file to download?**
A: You can find your PHP's architecture and the compiler it was built with by running phpinfo().

**Q: Where does the php_geoip.dll file need to be copied?**
A: Into C:\YOUR_LOCATION\Zend\ZendServer\lib\phpext\.

**Q: How do you enable the geoip extension after copying the dll?**
A: Go into the Zend Server interface, restart PHP, turn on the geoip extension from Server Setup -> Extension, and restart PHP again.
If you get an error loading the extension, you picked the wrong dll and should try another one.

**Q: What else is needed besides the extension itself?**
A: You also need to download geoip databases from MaxMind, such as geoIP.dat, geoIPCity.dat, and geoIPOrganization.dat, and copy those .dat files into C:\YOUR_LOCATION\Zend\ZendServer\bin\.

## Resources

- [php_geoip 1.0.8 downloads](http://windows.php.net/downloads/pecl/releases/geoip/1.0.8/)
