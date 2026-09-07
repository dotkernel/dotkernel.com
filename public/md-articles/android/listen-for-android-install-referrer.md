---
title: "Listen for Android install referrer"
description: "Android market broadcasts an intent containing referrer information at install time, before the app is opened, which can be used for install tracking."
author: "n3vrax"
date_published: "2011-07-24"
canonical_url: "https://www.dotkernel.com/android/listen-for-android-install-referrer/"
category: "Android"
language: "en"
---

# Listen for Android install referrer

## Getting Referrer Data at Install Time

Have you ever wondered if Android market sends you information at the moment of app install? Wouldn't it be nice to create custom links to your Android application, including bits of information about the referrer, and send it directly to the app for processing at install? This could be a simple and accurate solution for mobile app install tracking, but I'm sure you can find this useful in many ways.

With Android, you actually get this information as a broadcasted intent by Android market at install time - even before opening your app...

## FAQ

**Q: Does Android send information when the app is installed?**
A: Yes. Android market broadcasts an intent containing referrer information at the moment the app is installed.

**Q: When is this referrer information available to the app?**
A: It's delivered as a broadcasted intent at install time, before the app is ever opened.
