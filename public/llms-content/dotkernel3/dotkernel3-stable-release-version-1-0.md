---
title: "DotKernel3 - Stable Release version 1.0"
description: "DotKernel3's stable 1.0 release adds Zend Expressive 3 and PSR-15 middleware support, listing what's new, what changed for existing middleware, and every package updated or removed."
author: "Gabi DJ"
date_published: "2018-06-27"
canonical_url: "https://www.dotkernel.com/dotkernel3/dotkernel3-stable-release-version-1-0/"
category: "Dotkernel 3"
language: "en"
---

# DotKernel3 - Stable Release version 1.0

## TL;DR

DotKernel3 1.0 updates the core packages to support Zend Expressive 3 and PSR-15 middleware, making both frontend (1.0.0) and admin (1.0.1) easier to migrate.
No functional changes were made to the core code, though projects using the old http-interop/http-middleware package must migrate to the psr/http-server-middleware interfaces.
Existing DotKernel 3 (Expressive 2) projects can follow a separate guide to move to Zend Expressive 3.

## Release Overview

DotKernel was updated to support Zend Expressive 3 alongside PSR-15 middleware.

We have updated the core packages to support PSR-15 Middleware.

By updating the core packages, both frontend and admin are easier to migrate.

The new versions are:
frontend -> 1.0.0
admin -> 1.0.1

If your project is a DotKernel 3 instance (based on Expressive 2), you can migrate your project to Zend Expressive 3 by following this guide.

## What's New

- Runs on Zend Stratigility / Zend Expressive 3.0
- Middleware is now PSR-15 compliant
- PHP >=7.1 Support

## Changes

- No functional changes were made in the core code
- If your middleware code is based on the dotkernel/dot-controller package, no middleware migration is needed
- If your middleware code is based on the http-interop/http-middleware, migration must be made to implement interfaces in psr/http-server-middleware

## Packages Updated

- dotkernel/dot-authentication-service:^1.0
- dotkernel/dot-authentication-web:^1.0.1
- dotkernel/dot-authentication:^1.0
- dotkernel/dot-controller:^1.0
- dotkernel/dot-controller-plugin-authentication:^1.0
- dotkernel/dot-controller-plugin-authorization:^1.0
- dotkernel/dot-controller-plugin-flashmessenger:^1.0
- dotkernel/dot-controller-plugin-forms:^1.0
- dotkernel/dot-controller-plugin-mail:^1.0
- dotkernel/dot-controller-plugin-session:^1.0
- dotkernel/dot-flashmessenger:^1.0
- dotkernel/dot-helpers:^1.0
- dotkernel/dot-log:^1.1.1
- dotkernel/dot-mail:^1.0
- dotkernel/dot-mapper:^1.0
- dotkernel/dot-navigation:^1.0
- dotkernel/dot-rbac:^0.2.1
- dotkernel/dot-rbac-guard:^1.0
- dotkernel/dot-session:^3.0
- dotkernel/dot-twigrenderer:^1.1
- dotkernel/dot-user:^1.0

## Zend Packages (Support) Updated

- psr/http-server-middleware:^1.0
- psr/http-server-handler:^1.0
- zendframework/zend-expressive-helpers:^5.0
- zendframework/zend-expressive-twigrenderer:^2.0
- zendframework/zend-expressive-template:^2.0
- zendframework/zend-expressive:^3.0
- zendframework/zend-expressive-fastroute:^3.0
- zendframework/zend-expressive-tooling:^1.0
- zendframework/zend-expressive-router:^3.0
- zendframework/zend-stratigility:^3.0
- zendframework/zend-component-installer:^2.0

## Packages Removed

- http-interop/http-middleware
- webimpress/http-middleware-compatibility

## FAQ

**Q: What is the headline change in this stable release?**
A: DotKernel was updated to support Zend Expressive 3 alongside PSR-15 middleware, with the core packages updated to support PSR-15 Middleware, making both frontend and admin easier to migrate.

**Q: What are the new component versions in this release?**
A: frontend 1.0.0 and admin 1.0.1.

**Q: What PHP version does this release require?**
A: PHP >= 7.1.

**Q: Do you need to migrate your middleware code for this release?**
A: If your middleware code is based on the dotkernel/dot-controller package, no migration is needed. If it's based on http-interop/http-middleware, you must migrate it to implement the interfaces in psr/http-server-middleware.

**Q: What packages were removed in this release?**
A: http-interop/http-middleware and webimpress/http-middleware-compatibility.
