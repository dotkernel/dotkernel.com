---
title: "Zend_Mail and Zend_Http Security Fixes in Zend Framework 1.12.12"
description: "Zend Framework 1.12.12 was released with security fixes for the Zend_Mail and Zend_Http components, and a follow-up 1.12.13 release soon after fixed a regression."
author: "admin"
date_published: "2015-05-19"
canonical_url: "https://www.dotkernel.com/zend-framework/zend-mail-and-zend-http-security-fixes-in-zend-framework-1-12-12/"
category: "Zend Framework"
language: "en"
---

# Zend_Mail and Zend_Http Security Fixes in Zend Framework 1.12.12

## TL;DR
Zend Framework 1.12.12 was released with security fixes for the Zend_Mail and Zend_Http components.
Consumers of these components, including Dotkernel which relies heavily on Zend_Mail, were strongly urged to upgrade immediately via PEAR or by applying the patch directly.
A follow-up release, 1.12.13, was issued shortly after to fix a regression introduced in 1.12.12.

The release of ZF 1.12.12 was just announced, with Security Updates especially on Zend_Mail and Zend_Http components.

For more information, please read the official release announcement: [Zend Framework 1.12.12 Released](http://bit.ly/1Hr0K6e)

Also, the ZF PEAR channel was updated to latest 1.12.12 release.

**pear upgrade zend/zend**

We strongly recommend that consumers of the Zend_Http and Zend_Mail components upgrade immediately. Also, Dotkernel Application Framework use intensively the Zend_Mail component.

If you cannot, you can download the patch separately and apply it to your ZF install: [ZF2015-04 patch for ZF1](https://packages.zendframework.com/releases/ZendFramework-1.12.12/0001-ZF2015-04-Fix-CRLF-injections-in-HTTP-and-Mail.patch)

**May 20, 2015 EDIT:**

Zend Framework 1.12.13 was released, in order to fix a regression issue introduced in 1.12.12 release.

[Release Announcement](http://framework.zend.com/blog/zend-framework-1-12-13-released.html)
