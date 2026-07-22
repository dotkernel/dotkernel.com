---
title: "DotKernel API 1.0.0 Released"
description: "Announcement of the DotKernel API 1.0.0 release, a Zend Expressive 3 application for quickly building APIs, including the libraries it uses and the features it offers out of the box."
author: "Alex Karajos"
date_published: "2019-12-15"
canonical_url: "https://www.dotkernel.com/dotkernel-api/dotkernel-api-1-0-0-released/"
category: "Dotkernel API"
language: "en"
---

# DotKernel API 1.0.0 Released

> Note: Dotkernel API has come a long way since this post was created; a newer version is documented separately.

DotKernel API 1.0.0 was just released.

## What is DotKernel API?

It is a Zend Expressive 3 application aiming to help developers quickly and efficiently develop an API.

## How Does It Work?

Under the hood, it uses the following libraries:

- `ezimuel/zend-expressive-api` - skeleton application on which this API is based
- `dotkernel/dot-annotated-services` (^1.1) - for handling dependency injection in your services
- `dotkernel/dot-console` (^0.1.1) - for developing console applications
- `dotkernel/dot-errorhandler` (^1.0) - which provides customizable error logging
- `dotkernel/dot-mail` (^1.0) - for sending emails via SMTP
- `zendframework/zend-expressive-authentication-oauth2` (^1.0) - for OAuth2 authentication
- `zendframework/zend-expressive-authorization-rbac` (^1.0) - for role-based permissions
- `zendframework/zend-expressive-twigrenderer` (^2.4) - for composing email bodies
- `dasprid/container-interop-doctrine` (^1.1) - database abstraction layer
- `tuupola/cors-middleware` (^0.9.4) - for automatically sending CORS headers with each request
- `swagger-api/swagger-ui` (^3.22) - for creating OpenAPI 3 documentation

## What Does It Offer?

Out-of-the-box, DotKernel API provides the following features:

- Secure authentication via OAuth2
- Two user roles: admin and member
- Admin users are allowed to manage any user account
- Members are allowed to manage only their own accounts
- OpenAPI 3 documentation - also an interactive interface that developers can use to integrate your API

## FAQ

**Q: What is DotKernel API?**
A: It is a Zend Expressive 3 application aiming to help developers quickly and efficiently develop an API.

**Q: What key libraries does DotKernel API 1.0.0 use?**
A: Among others, it's built on the `ezimuel/zend-expressive-api` skeleton, and uses `dotkernel/dot-annotated-services` for dependency injection, `dotkernel/dot-console` for console applications, `dotkernel/dot-errorhandler` for error logging, `dotkernel/dot-mail` for SMTP email, `zend-expressive-authentication-oauth2` for OAuth2 authentication, `zend-expressive-authorization-rbac` for role-based permissions, and `swagger-api/swagger-ui` for OpenAPI 3 documentation.

**Q: What features does DotKernel API 1.0.0 offer out of the box?**
A: Secure authentication via OAuth2, two user roles (admin and member), where admins can manage any user account and members can manage only their own, plus OpenAPI 3 documentation with an interactive interface developers can use to integrate the API.

## Resources

- [DotKernel API 1.0.0 release on GitHub](https://github.com/dotkernel/api/releases/tag/v1.0.0)
- [ezimuel/zend-expressive-api](https://github.com/ezimuel/zend-expressive-api)
- [dotkernel/dot-annotated-services](https://github.com/dotkernel/dot-annotated-services)
- [dotkernel/dot-console](https://github.com/dotkernel/dot-console)
- [dotkernel/dot-errorhandler](https://github.com/dotkernel/dot-errorhandler)
- [dotkernel/dot-mail](https://github.com/dotkernel/dot-mail)
- [zendframework/zend-expressive-authentication-oauth2](https://github.com/zendframework/zend-expressive-authentication-oauth2)
- [zendframework/zend-expressive-authorization-rbac](https://github.com/zendframework/zend-expressive-authorization-rbac)
- [zendframework/zend-expressive-twigrenderer](https://github.com/zendframework/zend-expressive-twigrenderer)
- [dasprid/container-interop-doctrine](https://github.com/DASPRiD/container-interop-doctrine)
- [tuupola/cors-middleware](https://github.com/tuupola/cors-middleware)
- [swagger-api/swagger-ui](https://github.com/swagger-api/swagger-ui)
- [Newest version of Dotkernel API](https://www.dotkernel.com/headless-platform/version-7-adds-postgresql-native-uuid-and-php-8-5/)
