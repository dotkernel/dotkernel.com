---
title: "Disambiguation: DotKernel 1 and DotKernel 3"
description: "Clarifies what DotKernel 1 and DotKernel 3 are, how they differ architecturally, and which version is meant when someone simply says 'DotKernel'."
author: "Gabi DJ"
date_published: "2017-04-24"
canonical_url: "https://www.dotkernel.com/dotkernel/disambiguation-dotkernel-1-and-dotkernel-3/"
category: "Dotkernel"
language: "en"
---

# Disambiguation: DotKernel 1 and DotKernel 3

## TL;DR

DotKernel 1 is the original PHP Application Framework built on Zend Framework 1 with an MVC architecture, released in 2010 and now in bugfix-only mode at version 1.8 LTS.
DotKernel 3 is a newer collection of PSR-7 middleware applications built on the Zend Expressive microframework and Zend Framework 3 components, implementing PSR-1, PSR-2, PSR-4, PSR-7, and PSR-11.
Since DotKernel 3's release, the unqualified name "DotKernel" refers to DotKernel 3, while DotKernel 1 is always referenced explicitly.

## What Is DotKernel?

The name DotKernel symbiotically combines the string Dot, as a representation of the Internet, and Kernel, the quintessence of any IT application.
In other words, DotKernel wishes to be, with modesty, the central part of Internet development, ensuring increased development productivity and run-time performance.

## What Is DotKernel 1?

DotKernel 1 is a PHP Application Framework, built on top of Zend Framework 1 (ZF1).
It had its first public release in July 2010.
It is tightly coupled with Zend Framework 1, and adds a set of custom or external features (such as Router, Template Engine, etc.).
It is composed of Zend Framework 1 and a set of custom or external features (such as Router, Template Engine, etc.).
DotKernel 1's architecture is based on MVC.

The latest version is 1.8 Long Term Support.
No new version will be released anymore, only bugfixes.

## What Is DotKernel 3?

A collection of PSR-7 Middleware applications built on top of the [Zend Expressive](https://docs.zendframework.com/zend-expressive/) microframework.
It is composed of a set of custom and extended [Zend Framework 3](https://framework.zend.com/) components.
DotKernel 3's architecture is based on Middleware.
DotKernel implements the following PSRs: PSR-1, PSR-2, PSR-4, PSR-7, PSR-11.

Currently there are 2 applications: Frontend and Admin, and a 3rd one is under development: API.

## DotKernel = DotKernel 1 or DotKernel 3?

In posts older than 2017, DotKernel 1 was referred to as DotKernel, because it was the only DotKernel version.
Since the release of DotKernel 3, it is referred to as DotKernel 3 or DotKernel.
All future references to DotKernel 1 will be explicitly made.

### As of DotKernel 3 Release:

DotKernel 1 = DotKernel 1
DotKernel 3 = DotKernel 3

#### DotKernel = DotKernel 3

## FAQ

**Q: What does the name "DotKernel" mean?**
A: It combines "Dot", as a representation of the Internet, with "Kernel", the quintessence of any IT application, reflecting the aim of being a central part of Internet development.

**Q: What is DotKernel 1?**
A: A PHP Application Framework built on top of Zend Framework 1, first publicly released in July 2010, with an architecture based on MVC. Its latest version is 1.8 Long Term Support, which per the article will not be followed by a new version, only bugfixes.

**Q: What is DotKernel 3?**
A: A collection of PSR-7 Middleware applications built on top of the Zend Expressive microframework, composed of a set of custom and extended Zend Framework 3 components, with an architecture based on Middleware. It implements PSR-1, PSR-2, PSR-4, PSR-7, and PSR-11.

**Q: How many applications make up DotKernel 3?**
A: At the time of the article, there were two available applications, Frontend and Admin, with a third one, API, under development.

**Q: When someone writes just "DotKernel", which version is meant?**
A: In posts older than 2017, "DotKernel" referred to DotKernel 1, since it was the only version. Since the release of DotKernel 3, "DotKernel" refers to DotKernel 3, and all future references to DotKernel 1 are made explicitly.
