---
title: "Zend Framework dropped integration of WURFL adapter"
description: "Zend Framework 1.12.0 drops the WURFL adapter from Zend_Http_UserAgent due to WURFL's licensing change to AGPL; DotKernel is unaffected since it uses its own WURFL adapter."
author: "admin"
date_published: "2012-03-22"
canonical_url: "https://new.dotkernel.com/dotkernel/zend-framework-dropped-integration-of-wurfl-adapter/"
category: "Dotkernel"
language: "en"
---

# Zend Framework dropped integration of WURFL adapter

## What happened

According to Matthew Weier O'Phinney, Zend Framework Project Leader, the WURFL adapter will be dropped from `Zend_Http_UserAgent` in the next release of ZF, the 1.12.0 branch:

> due to a change in licensing by the WURFL project -- the free version is now AGPL -- we're dropping the WURFL adapter from Zend_Http_UserAgent. In discussions with Zend and the CR Team, we feel the AGPL raises too many concerns for end users with regards to how their products must be licensed in order to comply. (This is a similar rationale as to why we did not consider ExtJS when looking at JS toolkits to partner with.)
>
> As such, if you relied on the WURFL adapter in the past, you _will_ need to change code when upgrading to ZF 1.12.0, or grab the WURFL adapter from a previous version.

So it's a good moment to check old or current projects, and proceed accordingly.

## Impact on DotKernel

DotKernel framework uses its own WURFL adapter, so this change will not affect projects built with it.

## FAQ

**Q: Why did Zend Framework drop the WURFL adapter?**
A: According to Matthew Weier O'Phinney, Zend Framework Project Leader, the WURFL adapter was dropped from Zend_Http_UserAgent in the upcoming 1.12.0 branch due to a change in licensing by the WURFL project: the free version became AGPL, which raised too many concerns for end users regarding how their products must be licensed to comply.

**Q: Does this change affect projects built with DotKernel?**
A: No. DotKernel uses its own WURFL adapter, so the removal of the WURFL adapter from Zend_Http_UserAgent in ZF 1.12.0 will not affect projects built with DotKernel.
