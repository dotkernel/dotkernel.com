---
title: "Zend_Session usage in DotKernel - Refactor of Dot_Session class"
description: "A session cookie bug found in IE8/IE9 on DotKernel 1.5.0, traced to redundant regenerateID()/rememberMe() calls in Dot_Session, and the fix shipped in 1.5.1."
author: "admin"
date_published: "2011-07-28"
canonical_url: "https://www.dotkernel.com/dotkernel/zend-session-usage-in-dotkernel-refactor-of-dot-session-class/"
category: "Dotkernel"
language: "en"
---

# Zend_Session usage in DotKernel - Refactor of Dot_Session class

## TL;DR

A strange session bug was found on a project running DotKernel 1.5.0: in IE8 and IE9, the session cookie was sometimes not saved, forcing repeated logins.
Investigation traced it to the `Dot_Session` class calling both `regenerateID()` and `rememberMe()` unnecessarily, generating the session cookie 3 times.
The fix, shipped in DotKernel 1.5.1, removed the `regenerateID()` call and added two new application.ini settings.

## The bug

We found a strange behaviour of sessions in one of our projects, running DotKernel version 1.5.0 — similar to [one described here](http://trac.elgg.org/ticket/2677).
In unknown circumstances, and only in IE 8 and IE9, the session cookie is not saved on the client machine, and the user needs to log in over and over again.
It was reproduced once on the staging server, and the only way to fix it at the time was to open a new tab with the same page.

## The investigation

Investigating the `Dot_Session` class showed that the session cookie is generated **3 times**.
See this [bug report](http://www.dotkernel.net/view.php?id=184).

The code used both **regenerateID()** and **rememberMe()** methods of Zend_Session, which is **not necessary**.
Quote from the [ZF documentation](http://framework.zend.com/manual/1.11/en/zend.session.global_session_management.html):

> If you call the rememberMe() function, then don't use regenerateId(), since the former calls the latter.
> If a user has successfully logged into your website, use rememberMe() instead of regenerateId().

## The fix

The **regenerateID()** call was removed, and 2 new settings were added in application.ini related to session:

- **use_only_cookies** — must be **ON** at all times in order to avoid session fixation.
- **remember_me_seconds**.

These bug fixes were included in the new DotKernel version 1.5.1.

## Tip

If you encounter the same issue in IE8 and IE9, then with all regret, you need to deactivate the **rememberMe()** and **regenerateId()** methods calls.

## FAQ

**Q: What session bug was found in DotKernel 1.5.0?**
A: In unknown circumstances, and only in IE8 and IE9, the session cookie was not saved on the client machine, forcing the user to log in over and over again.
The only workaround found was to open a new tab with the same page.

**Q: What caused the session cookie to be generated multiple times?**
A: Investigation of the Dot_Session class showed the session cookie was generated 3 times, because the code called both regenerateID() and rememberMe() methods of Zend_Session.
According to the Zend Framework documentation, this is unnecessary: if you call rememberMe(), you should not also call regenerateId(), since rememberMe() already calls it internally.

**Q: What was the fix, and where was it released?**
A: The regenerateID() call was removed, and two new application.ini settings were added: use_only_cookies, which must be ON at all times to avoid session fixation, and remember_me_seconds.
These fixes were included in DotKernel version 1.5.1.

**Q: What should I do if I still see this issue in IE8/IE9?**
A: If you encounter the same issue in IE8 and IE9, the tip given is to deactivate the rememberMe() and regenerateId() method calls.

## Resources

- Similar issue described on Elgg: http://trac.elgg.org/ticket/2677
- DotKernel bug report: http://www.dotkernel.net/view.php?id=184
- Zend Framework documentation on global session management: http://framework.zend.com/manual/1.11/en/zend.session.global_session_management.html
