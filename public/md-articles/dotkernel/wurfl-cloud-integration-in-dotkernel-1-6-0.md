---
title: "Wurfl Cloud Integration in Dotkernel 1.6.0"
description: "How WURFL Cloud, WURFL's cloud-based device detection service, was integrated as the default mobile detection method in Dotkernel 1.6.0."
author: "deddu"
date_published: "2012-05-18"
canonical_url: "https://www.dotkernel.com/dotkernel/wurfl-cloud-integration-in-dotkernel-1-6-0/"
category: "Dotkernel"
language: "en"
---

# Wurfl Cloud Integration in Dotkernel 1.6.0

## TL;DR
Dotkernel 1.6.0 integrates Wurfl Cloud, WURFL's (Wireless Universal Resource FiLe) new cloud-based way of delivering device detection services, as its default method for detecting mobile devices.

Another new feature in version 1.6.0 of Dotkernel is the integration of Wurfl Cloud.

Wurfl or Wireless Universal Resource FiLe is coming with a new way to deliver their services of device detection and they named it Wurfl Cloud.

Because of the need to detect mobile devices in Dotkernel we have integrated Wurfl Cloud as the default detection method.

If you want to see the Wurfl Cloud library you can find it in the library folder of Dotkernel.

Quick steps to have a functional device detection in Dotkernel.

- register for an Wurfl Cloud account [here](https://www.scientiamobile.com/register)
- choose **device_os** and **mobile_browser** as your capabilities
- copy your API Key in application.ini in Dotkernel

```
resources.useragent.wurflcloud.api_key = 000000:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

Now you can test it by changing the user agent in your browser to a mobile device and access your project. Now you can see that you are redirected to the mobile module.

To see how mobile detection is working in Dotkernel read this [post](http://www.dotkernel.com/?p=1465).

## FAQ

**Q: What is WURFL Cloud?**
A: WURFL Cloud is a new way that WURFL (Wireless Universal Resource FiLe) delivers its device detection services. Dotkernel integrated it as the default detection method for mobile devices starting with version 1.6.0.

**Q: How do I get a functional device detection setup with WURFL Cloud?**
A: Register for a Wurfl Cloud account with Scientia Mobile, choose device_os and mobile_browser as your capabilities, then copy your API key into application.ini in Dotkernel.

**Q: What line do I add to application.ini for the API key?**
A: Add a line such as resources.useragent.wurflcloud.api_key = 000000:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX, using your own API key.

**Q: How can I test that mobile detection is working?**
A: Change the user agent in your browser to a mobile device and access your project; you should be redirected to the mobile module.
