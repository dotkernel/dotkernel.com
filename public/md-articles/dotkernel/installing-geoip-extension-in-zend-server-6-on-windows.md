---
title: "Installing GeoIP extension in Zend Server 6 on Windows"
description: "Update to the Zend Server 5.6 GeoIP guide, covering how to install and enable php_geoip on Zend Server 6.1 on Windows."
author: "admin"
date_published: "2013-07-31"
canonical_url: "https://www.dotkernel.com/dotkernel/installing-geoip-extension-in-zend-server-6-on-windows/"
category: "Dotkernel"
language: "en"
---

# Installing GeoIP extension in Zend Server 6 on Windows

## TL;DR
As an update to [Installing GeoIP extension in Zend Server 5.6 on Windows](https://www.dotkernel.com/dotkernel/installing-geoip-extension-in-zend-server-5-6-on-windows/), here's how to enable php_geoip on Zend Server 6.1.

## Steps

1. Download the `php_geoip-1.0.8-5.4-nts-vc9-x86.zip` file from [http://windows.php.net/downloads/pecl/releases/geoip/1.0.8/](http://windows.php.net/downloads/pecl/releases/geoip/1.0.8/).
2. From the archive, copy `php_geoip.dll` to `ZEND_PATH\Zend\ZendServer\lib\phpext\`.
3. Open the `php.ini` file, located at `ZEND_PATH\Zend\ZendServer\etc\php.ini`.
4. Add the following line at the end of the file:

   ```ini
   extension=php_geoip.dll
   ```

5. Save the file and click **"Restart PHP"** from the Zend Server GUI.
6. Follow the steps from the previous article to test the GeoIP integration and download the Geoip*.dat files.

## FAQ

**Q: Which php_geoip file should be downloaded for Zend Server 6.1?**
A: Download the php_geoip-1.0.8-5.4-nts-vc9-x86.zip file from http://windows.php.net/downloads/pecl/releases/geoip/1.0.8/.

**Q: Where does the php_geoip.dll go on Zend Server 6.1?**
A: Copy the php_geoip.dll file from the archive to ZEND_PATH\Zend\ZendServer\lib\phpext\.

**Q: How is the extension enabled on Zend Server 6.1?**
A: Open the php.ini file at ZEND_PATH\Zend\ZendServer\etc\php.ini, add the line extension=php_geoip.dll at the end of the file, save it, and click "Restart PHP" from the Zend Server GUI.

**Q: How do you verify GeoIP is working after this setup?**
A: Follow the same steps described in the previous article (Installing GeoIP extension in Zend Server 5.6 on Windows) to test the GeoIP integration and download the Geoip*.dat files.

## Resources

- [Installing GeoIP extension in Zend Server 5.6 on Windows](http://www.dotkernel.com/dotkernel/installing-geoip-extension-in-zend-server-5-6-on-windows/)
- [php_geoip 1.0.8 downloads](http://windows.php.net/downloads/pecl/releases/geoip/1.0.8/)
