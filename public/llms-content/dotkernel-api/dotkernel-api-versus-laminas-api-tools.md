---
title: "DotKernel API versus Laminas API Tools"
description: "A feature-by-feature comparison of Laminas API Tools and Dotkernel API, showing why Dotkernel API is a solid alternative now that Laminas API Tools is archived."
author: "Florin Bidirean"
date_published: "2024-06-03"
canonical_url: "https://www.dotkernel.com/dotkernel-api/dotkernel-api-versus-laminas-api-tools/"
category: "Dotkernel API"
language: "en"
---

# DotKernel API versus Laminas API Tools

## TL;DR

This article compares the basic features of Laminas API Tools and Dotkernel API side by side, covering architecture, versioning, documentation, authentication, and more.
It highlights that Dotkernel API is a solid alternative now that Laminas API Tools has been archived, since Dotkernel API uses a modern middleware architecture, MIT license, and evolution-based deprecations instead of traditional versioning.

Below is an analysis of the basic features available in Laminas API Tools and DotKernel API.
It's intended to highlight the differences between the two and also to showcase why DotKernel API is a good alternative for Laminas API Tools, especially considering the latter's archived status.

> The table below refers to [Dotkernel API V7](https://github.com/dotkernel/api/tree/7.0).

| | API Tools (formerly Apigility) | Dotkernel API |
|---|---|---|
| URL | [api-tools](https://api-tools.getlaminas.org/) | [Dotkernel API](https://www.dotkernel.org) |
| First Release | 2012 | 2018 |
| PHP Version | <= 8.2 | Shown via a dynamic Packagist badge (see the project repository for the current supported version) |
| Architecture | MVC, Event Driven | Middleware |
| OSS Lifecycle | Archived | Shown via a dynamic OSS Lifecycle badge (see the project repository for the current status) |
| Style | REST, RPC | REST |
| Versioning | Yes | Deprecations (API Evolution) * |
| Documentation | Swagger (Automated) | Postman (Manual), OpenAPI 3.0 (Swagger) |
| Content-Negotiation | Custom | Custom |
| License | BSD-3 | MIT |
| Default DB Layer | laminas-db | doctrine-orm 3.x |
| Authorization | ACL | RBAC-guard |
| Authentication | HTTP Basic/Digest OAuth2.0 | OAuth2.0 |
| CI/CD | Yes | Yes |
| Unit Tests | Yes | Yes |
| Code (Endpoint) Generator | Yes | [dot-maker](https://docs.dotkernel.org/dot-maker/v1/overview/) |
| PSR | PSR-7 | PSR-7, PSR-15 |

## Note

- Versioning is replaced by [Deprecations](https://docs.dotkernel.org/api-documentation/v6/tutorials/api-evolution/), using an evolution strategy.

## FAQ

**Q: What is the purpose of this comparison?**
A: It highlights the differences between Laminas API Tools and Dotkernel API, and shows why Dotkernel API is a good alternative now that Laminas API Tools is archived.

**Q: Which version of Dotkernel API does the comparison table refer to?**
A: Dotkernel API V7.

**Q: What architecture does each project use?**
A: Laminas API Tools uses an MVC, event-driven architecture, while Dotkernel API uses a middleware architecture.

**Q: What license does each project use?**
A: Laminas API Tools is licensed under BSD-3, while Dotkernel API is licensed under MIT.

**Q: How does Dotkernel API handle API versioning?**
A: Instead of traditional versioning, Dotkernel API replaces it with Deprecations, using an evolution (API Evolution) strategy.

**Q: What documentation options does each project support?**
A: Laminas API Tools generates Swagger documentation automatically, while Dotkernel API supports manual Postman documentation as well as automated OpenAPI 3.0 (Swagger) documentation.
