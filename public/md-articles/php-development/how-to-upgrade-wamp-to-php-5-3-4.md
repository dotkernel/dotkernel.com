---
title: "How To Upgrade Wamp to PHP 5.3.4"
description: "Step-by-step instructions for upgrading a WAMP server's PHP version to 5.3.4."
author: "admin"
date_published: "2010-12-13"
canonical_url: "https://www.dotkernel.com/php-development/how-to-upgrade-wamp-to-php-5-3-4/"
category: "PHP Development"
language: "en"
---

# How To Upgrade Wamp to PHP 5.3.4

## TL;DR
A step-by-step guide to manually upgrading the PHP version used by a WAMP server to PHP 5.3.4, by downloading the VC6 Thread Safe build, copying over configuration files, and switching the active PHP version in WAMP.

1.    Stop WAMP server.

2.    Go to [windows.php.net](http://windows.php.net/download/) and download the the latests ZIPPED package for php5.3.4.

Make sure it is the **VC6 Thread Safe build**. DO NOT DOWNLOAD THE INSTALLER.

3.  Create a folder php5.3.4 into wamp/bin/php

4.  Extract the downloaded zip to the newly created php5.3.4 folder

5.  Copy the files:

-  *php.ini*

-  *phpForApache.ini*

-   *wampserver.conf*

from your existing php5.3 (eg.   wamp/bin/php/php5.3.3) folder to the new php5.3.4 folder.

6.  Open the files:

-   *php.ini*

-  *phpForApache.ini*

and search/replace the string 5.3.3 with 5.3.4

7.   Go to wamp/bin/apache/apache/apache2.2.11/bin and delete the file called *php.ini*

8.   Restart WAMP server.

9.   Choose php version 5.3.0

10.   Restart WAMP server.

11.   Now choose php version 5.3.4 .

12.   Check if PEAR path is correct in php.ini , and modify accordingly.

13.   Restart WAMP server.

14.   Enjoy.

## FAQ

**Q: Where do I download the PHP 5.3.4 package for WAMP?**
A: Download the latest ZIPPED package for PHP 5.3.4 from windows.php.net. Make sure it is the VC6 Thread Safe build, and do not download the installer.

**Q: Where should the downloaded PHP 5.3.4 files be extracted?**
A: Create a folder named php5.3.4 inside wamp/bin/php, then extract the downloaded zip into that newly created folder.

**Q: Which configuration files need to be copied and edited when upgrading?**
A: Copy php.ini, phpForApache.ini, and wampserver.conf from the existing php5.3 folder (e.g. wamp/bin/php/php5.3.3) into the new php5.3.4 folder. Then, in php.ini and phpForApache.ini, search and replace the string 5.3.3 with 5.3.4.

**Q: Is there anything to remove from the Apache folder before restarting WAMP?**
A: Yes. Go to wamp/bin/apache/apache/apache2.2.11/bin and delete the file called php.ini, then restart the WAMP server.

**Q: How do I actually switch WAMP to the new PHP version after restarting?**
A: After restarting WAMP, choose PHP version 5.3.0, restart WAMP server again, and then choose PHP version 5.3.4. Also check that the PEAR path is correct in php.ini and modify it accordingly, then restart WAMP server once more.
