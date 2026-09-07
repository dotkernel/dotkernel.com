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

Did you come to a point where using multiple broadcast receivers to listen for the same intent, separatly, in the same android app, leads to unexpected results? If that's the case, one broadcast receiver might consume the broadcasted intent, [online casino](http://www.cillap.com/) leaving the others with nothing to receive. This can be the case where you use 3rd party libraries with broadcast receivers defined.

The following is a solution for this kind of problem, a code snippet inspired by the way Admob for android seems to solve this, as shown in their [documentation](http://developer.admob.com/wiki/Android_App_Download_Tracking), using meta-data in manifest file...[[read more](http://n3vrax.wordpress.com/2011/07/15/multiple-broadcast-receivers-in-the-same-app-for-the-same-action/)].

## FAQ

**Q: What problem does this article address?**
A: When multiple broadcast receivers are registered separately to listen for the same intent in the same Android app, this can lead to unexpected results: one broadcast receiver might consume the broadcasted intent, leaving the others with nothing to receive.

**Q: When is this issue most likely to occur?**
A: This can happen when you use 3rd party libraries that already define their own broadcast receivers alongside your app's own receivers.
