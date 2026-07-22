---
title: "PHP 5.3.6 released. No upgrade possible for WampServer."
description: "PHP 5.3.6 dropped Visual Studio C++ 6 Windows builds, making an in-place WampServer upgrade impossible since WampServer itself is built with VC++ 6."
author: "admin"
date_published: "2011-03-18"
canonical_url: "https://www.dotkernel.com/php-development/php-5-3-6-released-no-upgrade-possible-for-wampserver/"
category: "PHP Development"
language: "en"
---

# PHP 5.3.6 released. No upgrade possible for WampServer.

PHP 5.3.6 was released, but it comes with bad news for WampServer users. The release notes state:

> Windows users: please mind that we do no longer provide builds created with Visual Studio C++ 6

Since WampServer is built using VC++ 6, an upgrade isn't possible without a rewrite of the entire WampServer package from scratch.

## FAQ

**Q: Why can't WampServer users upgrade to PHP 5.3.6?**
A: PHP 5.3.6 no longer provides builds created with Visual Studio C++ 6 for Windows users. Since WampServer is built using VC++ 6, upgrading isn't possible without rewriting the entire WampServer package from scratch.

**Q: Where can WampServer users find more details about this issue?**
A: More details are available on the WampServer forum, linked in this article.

## Resources

- [WampServer forum discussion](http://www.wampserver.com/phorum/read.php?2,72243)
