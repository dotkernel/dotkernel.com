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

Android market sends information at the moment of app install, delivered as a broadcasted intent by Android market at install time - even before the app is opened for the first time.
This can be used to create custom links to an Android application, including bits of information about the referrer, sent directly to the app for processing at install.
It can be a simple and accurate solution for mobile app install tracking, among other uses.

## FAQ

**Q: Does Android send information when the app is installed?**
A: Yes.
Android market broadcasts an intent containing referrer information at the moment the app is installed.

**Q: When is this referrer information available to the app?**
A: It's delivered as a broadcasted intent at install time, before the app is ever opened.
