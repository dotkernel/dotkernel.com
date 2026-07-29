---
title: "Detecting Mobile Devices in Dotkernel 1.6.0"
description: "Explains how mobile device detection changed in Dotkernel 1.6.0 with the move to Wurfl Cloud, including the required application.ini settings and sample Dot_UserAgent usage code."
author: "deddu"
date_published: "2012-05-18"
canonical_url: "https://www.dotkernel.com/dotkernel/detecting-mobile-devices-in-dotkernel-1-6-0/"
category: "Dotkernel"
language: "en"
---

# Detecting Mobile Devices in Dotkernel 1.6.0

## TL;DR

Dotkernel 1.6.0 no longer ships with a working built-in mobile detection method, because mobile detection now relies on the new Wurfl Cloud integration and must be configured via a Wurfl Cloud account and API key.
The old Dot_UserAgent_Wurfl class was removed and replaced by Dot_UserAgent_WurflCloud, which uses the Wurfl Cloud API adapter.
The article walks through the application.ini settings and shows sample code for reading device info and redirecting mobile visitors.

The new Dotkernel version 1.6.0 is coming with some changes to how we detect mobile devices; these changes are because of the new Wurfl Cloud integration.
This version of Dotkernel no longer comes with a working built-in method for mobile detection, so first we have to configure it.

- Go to the scientiamobile website and register for a Wurfl Cloud account.
- Choose device_os and mobile_browser for your account and save.
- Go to API Keys and copy the right key into application.ini.

We chose device_os and mobile_browser capabilities because with these two capabilities we can get some extra capabilities (isMobile, isSmartPhone, isIphone, isAndroid, isBlackberry, isSymbian, and isWindowsMobile) using our built-in methods.
Choosing other capabilities from scientiamobile will result in wrong detection of these extra capabilities, but you can get only those capabilities using another method from the Dot_UserAgent_WurflCloud class.

Wurfl Cloud setting in application.ini:

```ini
resources.useragent.wurflcloud.active = TRUE
resources.useragent.wurflcloud.redirect = TRUE
resources.useragent.wurflcloud.cache = TRUE
resources.useragent.wurflcloud.cache_lifetime = 3600
resources.useragent.wurflcloud.cache_namespace = WURFLCLOUD
resources.useragent.wurflcloud.api_key = 000000:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
resources.useragent.wurflcloud.lib_dir = APPLICATION_PATH "/library/WurflCloud/"
```

- active - used to turn on (TRUE) or off (FALSE) the Wurfl Cloud detection (default: TRUE).
- redirect - if TRUE, visitors from the frontend will be redirected to the mobile module (default: TRUE).
- cache - caches every distinct result to optimize the number of requests to scientiamobile (default: TRUE).
- cache_lifetime - time in seconds to keep the results in cache (default: 3600).
- cache_namespace - the prefix used for cache keys (default: WURFLCLOUD).
- api_key - the API key from your WURFL Cloud account (change this to your own key).
- lib_dir - the Wurfl Cloud library location in Dotkernel (don't change this, unless you want to move the library).

Because of these changes, we removed the old Dot_UserAgent_Wurfl class and added the new Dot_UserAgent_WurflCloud class, which uses the Wurfl Cloud API adapter.

## Example of Dot_UserAgent Usage in Dotkernel

Get Wurfl configuration:

```php
$wurflConf = $registry->configuration->resources->useragent->wurflcloud;
```

Note: you can have more Wurfl configurations if you have more libraries, like the Wurfl Package (GPL).

If Wurfl is active, then get device info:

```php
if($wurflConf->active)
{
    $deviceInfo = Dot_UserAgent :: getDeviceInfo($_SERVER);
    ...
}
```

If the detected device is a mobile device, we save the device info in the database and redirect it to the mobile controller:

```php
if( (0 < count((array)$deviceInfo)) && $deviceInfo->isMobile)
{

    if(!$registry->session->visitId)
    {
        $registry->session->visitId = Dot_Statistic::registerVisit();
    }

    // if the Statistic module is integrate, record the deviceInfo too, and record TRUE
        //in $session->mobile
    if(!$registry->session->mobile)
    {
        $registry->session->mobile =
                Dot_Statistic::registerMobileDetails($registry->session->visitId, $deviceInfo);

        //redirect to mobile controller , only if the session is not set.
        //Otherwise will trap the user in mobile controller
        if($wurflConf->redirect)
        {
            header('location: '.
                        $registry->configuration->website->params->url.'/mobile');
            exit;
        }
    }
}
```

## FAQ

**Q: Why did mobile detection change in Dotkernel 1.6.0?**
A: Because of the new Wurfl Cloud integration. This version no longer ships with a working built-in method for mobile detection, so it must be configured first.

**Q: What are the steps to configure Wurfl Cloud detection?**
A: Go to the scientiamobile website and register for a Wurfl Cloud account, choose the device_os and mobile_browser capabilities for the account and save, then go to API Keys and copy the key into application.ini.

**Q: Why choose the device_os and mobile_browser capabilities specifically?**
A: With these two capabilities, Dotkernel's built-in methods can also derive extra capabilities such as isMobile, isSmartPhone, isIphone, isAndroid, isBlackberry, isSymbian, and isWindowsMobile. Choosing other capabilities from scientiamobile results in wrong detection of these extra capabilities.

**Q: What happened to the old Dot_UserAgent_Wurfl class?**
A: It was removed and replaced with the new Dot_UserAgent_WurflCloud class, which uses the Wurfl Cloud API adapter.

**Q: What does the redirect setting in application.ini control?**
A: When resources.useragent.wurflcloud.redirect is TRUE (the default), visitors from the frontend are redirected to the mobile module the first time a mobile device is detected.
