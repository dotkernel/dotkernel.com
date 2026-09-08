---
title: "Multiple broadcast receivers in the same app, for the same action"
description: "Using multiple broadcast receivers to listen separately for the same intent in the same Android app can lead to unexpected results, since one receiver may consume the broadcast and leave the others with nothing."
author: "n3vrax"
date_published: "2011-07-22"
canonical_url: "https://www.dotkernel.com/android/multiple-broadcast-receivers-in-the-same-app-for-the-same-action/"
category: "Android"
language: "en"
---

# Multiple broadcast receivers in the same app, for the same action

## The problem

When multiple broadcast receivers are registered separately to listen for the same intent within the same Android app, this can lead to unexpected results: one broadcast receiver might consume the broadcasted intent, leaving the others with nothing to receive.
This can happen when using 3rd party libraries that define their own broadcast receivers alongside an app's own receivers.

## The approach

A solution for this kind of problem is a code snippet inspired by the way Admob for Android solves this, as shown in Admob's own documentation, using meta-data in the manifest file.

## FAQ

**Q: What problem does this article address?**
A: When multiple broadcast receivers are registered separately to listen for the same intent in the same Android app, this can lead to unexpected results: one broadcast receiver might consume the broadcasted intent, leaving the others with nothing to receive.

**Q: When is this issue most likely to occur?**
A: This can happen when you use 3rd party libraries that already define their own broadcast receivers alongside your app's own receivers.

## Resources

- [Admob App Download Tracking documentation](http://developer.admob.com/wiki/Android_App_Download_Tracking)
