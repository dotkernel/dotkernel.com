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

## Where to find it

The Wurfl Cloud library can be found in the library folder of Dotkernel.

## Quick steps to a functional device detection setup

1. Register for a Wurfl Cloud account [here](https://www.scientiamobile.com/register).
2. Choose **device_os** and **mobile_browser** as your capabilities.
3. Copy your API key into application.ini in Dotkernel:

```ini
resources.useragent.wurflcloud.api_key = 000000:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

## Testing it

Change the user agent in your browser to a mobile device and access your project.
You should be redirected to the mobile module.

## FAQ

**Q: What is WURFL Cloud?**
A: WURFL Cloud is a new way that WURFL (Wireless Universal Resource FiLe) delivers its device detection services.
Dotkernel integrated it as the default detection method for mobile devices starting with version 1.6.0.

**Q: How do I get a functional device detection setup with WURFL Cloud?**
A: Register for a Wurfl Cloud account with Scientia Mobile, choose device_os and mobile_browser as your capabilities, then copy your API key into application.ini in Dotkernel.

**Q: What line do I add to application.ini for the API key?**
A: Add a line such as `resources.useragent.wurflcloud.api_key = 000000:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX`, using your own API key.

**Q: How can I test that mobile detection is working?**
A: Change the user agent in your browser to a mobile device and access your project; you should be redirected to the mobile module.

## Resources

- Register for a Wurfl Cloud account: https://www.scientiamobile.com/register
- Related post — Detecting Mobile Devices in Dotkernel 1.6.0: http://www.dotkernel.com/?p=1465
