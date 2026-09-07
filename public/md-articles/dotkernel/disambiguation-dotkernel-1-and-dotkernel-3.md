---
title: "Disambiguation: Dotkernel 1 and Dotkernel 3"
description: "Clarifies what Dotkernel 1 and Dotkernel 3 are, how they differ architecturally, and which version is meant when someone simply says 'Dotkernel'."
author: "Gabi DJ"
date_published: "2017-04-24"
canonical_url: "https://www.dotkernel.com/dotkernel/disambiguation-dotkernel-1-and-dotkernel-3/"
category: "Dotkernel"
language: "en"
---

# Disambiguation: Dotkernel 1 and Dotkernel 3

## TL;DR
Dotkernel 1 is the original PHP Application Framework built on Zend Framework 1 with an MVC architecture, released in 2010 and now in bugfix-only mode at version 1.8 LTS.
Dotkernel 3 is a newer collection of PSR-7 middleware applications built on the Zend Expressive microframework and Zend Framework 3 components, implementing PSR-1, PSR-2, PSR-4, PSR-7, and PSR-11.
Since Dotkernel 3's release, the unqualified name "Dotkernel" refers to Dotkernel 3, while Dotkernel 1 is always referenced explicitly.

## What is the meaning behind 'Dotkernel'?

The name **Dotkernel** symbiotically combines the string **Dot,** as a representation of the Internet, and **Kernel**, the quintessential components of any IT application.

In other words, **Dotkernel** aims to become the starting point for development Internet applications and hence ensure increased development productivity and run-time performance.

## What was Dotkernel 1?

Dotkernel 1 was a ***PHP* *Application Framework***, built on top of Zend Framework 1 (ZF1).

It had the first public release in July 2010. It was tightly coupled with **Zend Framework 1** and adds a set of custom or external features (such as Router, Template Engine, etc.). It was composed of **Zend Framework 1** and a set of custom or external features (such as Router, Template Engine, etc.). Dotkernel 1 architecture was based on **MVC**.

The latest version is **1.8 Long Term Support**. It will not be getting any new releases or bugfixes because Zend Framework 1 is also not supported. If you are still using either Dotkernel 1 or Zend Framework 1, you need to refactor your code to the [Dotkernel Headless Platform](https://docs.dotkernel.org/headless-documentation/).

## What is Dotkernel?

A **collection** of PSR-15 Middleware applications built on top of the [**Mezzio**](https://docs.mezzio.dev/mezzio/v3/getting-started/quick-start/) microframework. It is composed of a set of custom and extended [**Laminas**](https://docs.laminas.dev/) components.

Dotkernel architecture is based on **Middleware**. Dotkernel implements the following PSR's, where applicable: PSR-7, PSR-11, PSR-15, PSR-3, PSR-4, PSR-6, PSR-13, PSR-14, PSR-17, PSR-18, PSR-20.

Currently, there are three applications:

- API
 - Admin
 - Queue

## Dotkernel = Dotkernel 1 or the new Dotkernel?

In posts older than 2017 **Dotkernel 1** was referred to as **Dotkernel** because it was the only Dotkernel version. Since the release of newer versions, we have dropped the number at the end, so currently we refer to our platform as 'Dotkernel'.

## FAQ

**Q: What does the name "Dotkernel" mean?**
A: It combines "Dot", as a representation of the Internet, with "kernel", the quintessence of any IT application, reflecting the aim of being a central part of Internet development.

**Q: What is Dotkernel 1?**
A: A PHP Application Framework built on top of Zend Framework 1, first publicly released in July 2010, with an architecture based on MVC. Its latest version is 1.8 Long Term Support, which per the article will not be followed by a new version, only bugfixes.

**Q: What is Dotkernel?**
A: A collection of PSR-15 Middleware applications built on top of the Mezzio microframework. It implements PSR-7, PSR-11, PSR-15, PSR-3, PSR-4, PSR-6, PSR-13, PSR-14, PSR-17, PSR-18, PSR-20.

**Q: How many applications make up Dotkernel?**
A: At the time of the article, there were three available applications, API, Admin and Queue.

**Q: When someone writes just "Dotkernel", which version is meant?**
A: In posts older than 2017, "Dotkernel" referred to Dotkernel 1, since it was the only version. Since the release of Dotkernel 3, "Dotkernel" refers to Dotkernel 3, and all future references to Dotkernel 1 are made explicitly.
