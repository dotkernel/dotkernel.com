---
title: "Dotkernel - A Headless Platform for Modern Web Applications"
description: "Dotkernel is a collection of open-source PHP application skeletons built on Mezzio and Laminas, ranging from a minimal presentation-site skeleton to a full headless platform (API, Admin, Queue) for enterprise-grade applications."
url: "https://www.dotkernel.com/"
language: "en"
entities:
  - name: "Dotkernel API"
    type: "SoftwareApplication"
    url: "https://github.com/dotkernel/api"
  - name: "Dotkernel Admin"
    type: "SoftwareApplication"
    url: "https://github.com/dotkernel/admin"
  - name: "Dotkernel Queue"
    type: "SoftwareApplication"
    url: "https://github.com/dotkernel/queue"
  - name: "Dotkernel Light"
    type: "SoftwareApplication"
    url: "https://github.com/dotkernel/light"
  - name: "Dotkernel Frontend"
    type: "SoftwareApplication"
    url: "https://github.com/dotkernel/frontend"
  - name: "Mezzio"
    type: "SoftwareFramework"
    url: "https://github.com/mezzio/mezzio-skeleton"
standards: ["PSR-7", "PSR-11", "PSR-15"]
keywords: ["headless platform", "PHP", "Mezzio", "Laminas", "PSR-15 middleware", "REST API", "admin panel", "task queue", "open source"]
---

# Dotkernel

## TL;DR

Dotkernel is a collection of open-source PHP application skeletons built on Mezzio and Laminas, licensed MIT.
Its headless platform combines three components - **API**, **Admin**, and **Queue** - that can be adopted separately or together.
Two additional skeletons, **Light** and **Frontend**, stand outside the platform for simpler or frontend-only use cases.

## Key facts

| Fact | Value |
|---|---|
| Runtime | Mezzio + Laminas |
| Standards | PSR-7, PSR-11, PSR-15 |
| License | MIT |
| Router | FastRoute |
| Response | Diactoros |
| Maintainer | Apidemia |

## Request lifecycle

Router (FastRoute) -> Authentication (dot-authentication) -> RBAC guard (dot-rbac-guard) -> your PSR-15 handler -> Response (Diactoros).

## The headless platform: three applications, one platform

API, Admin, and Queue are designed to integrate into a single, unified headless platform - better together than the sum of their parts.

| Component | Role | Repository |
|---|---|---|
| **API** | Framework-less, opinionated toolkit for shipping REST APIs; an alternative to legacy Laminas API Tools (Apigility) | https://github.com/dotkernel/api |
| **Admin** | Table-based backend for managing platform records, plus tools to build reports and dashboards | https://github.com/dotkernel/admin |
| **Queue** | Asynchronous task queuing built on Symfony Messenger, wired into the Laminas Service Manager container via netglue/laminas-messenger | https://github.com/dotkernel/queue |

## Skeletons outside the platform

Not every project needs a headless platform - these two stand on their own.

| Skeleton | Purpose | Repository |
|---|---|---|
| **Light** | Minimal, PSR-15 compliant skeleton built for learning purposes - a didactic example, not a platform component | https://github.com/dotkernel/light |
| **Frontend** | Standalone skeleton for building frontend applications on Mezzio and Laminas, separate from the headless platform | https://github.com/dotkernel/frontend |

## Components (dot-* packages)

Small, independent packages - pull in what you need. Grouped by concern: authentication (dot-authentication, dot-auth-social), authorization (dot-authorization, dot-rbac, dot-rbac-guard), controllers (dot-controller), forms (laminas-form), session (dot-session, dot-flashmessenger, dot-cache), logging & error handling (dot-log, dot-errorhandler), emailing (dot-mail), backend/database abstraction (doctrine/orm, doctrine/dbal, dot-data-fixtures), navigation (dot-navigation), templating (dot-twigrenderer), and supporting tools (dot-annotated-services, dot-event, dot-helpers, dot-cli, dot-doctrine-metadata, dot-response-header, dot-user-agent-sniffer, dot-debugbar, dot-geoip).

Full package list and lifecycle/support status: https://www.dotkernel.com/dotkernel-packages-oss-lifecycle/

## About

Dotkernel is an open-source project created and led by the dev team at Apidemia - built first as an internal tool for handling complex architectures, now freely shared with the community.

## Resources

- Documentation: https://docs.dotkernel.org
- GitHub organization: https://github.com/dotkernel
- Blog: https://www.dotkernel.com/blog/
- Categories: https://www.dotkernel.com/categories/
- Contact: https://www.dotkernel.com/contact/
