---
title: "Complementary Admin in Dotkernel Headless Platform"
description: "Overview of Dotkernel Admin, the independent, first-party admin application designed to pair seamlessly with Dotkernel API in the Dotkernel Headless Platform."
author: "Florin Bidirean"
date_published: "2025-08-06"
canonical_url: "https://www.dotkernel.com/headless-platform/complementary-admin-in-dotkernel-headless-platform/"
category: "Headless Platform"
language: "en"
---

# Complementary Admin in Dotkernel Headless Platform

## TL;DR

The Dotkernel Headless Platform's core components are Dotkernel API and Dotkernel Queue, but the suite also offers a fully separate, complementary **Admin** application designed to pair seamlessly with Dotkernel API. Admin is an independent app built on the same Mezzio + Laminas foundation, sharing a unified tech stack with the API so the two form a cohesive, consistent system.

## What is Dotkernel Admin?

Dotkernel Admin lives in its own separate repository and is a fully-functional, independent application. It's built on top of the same Mezzio + Laminas foundation as Dotkernel API, and comes with a built-in UI including authentication, user management and a web interface.

### Installation

1. Clone the repository into your folder of choice:
   ```shell
   git clone https://github.com/dotkernel/admin.git .
   ```
2. Follow the step-by-step [installation guide](https://docs.dotkernel.org/admin-documentation/v5/installation/getting-started/) to configure the database credentials and run the migrations and fixtures that create and populate the database. This takes only a few minutes and results in a ready-to-use admin panel.

> Make sure to change the default credentials when in production.

## Features & Architecture

Dotkernel Admin comes with several useful features out of the box:

- **Role-Based Access Control** using [dot-rbac](https://docs.dotkernel.org/dot-rbac/) and [dot-rbac-guard](https://docs.dotkernel.org/dot-rbac-guard/) to manage permissions, routes and handler-level security.
- **OpenAPI** annotations included in each module via `OpenAPI.php`, making admin endpoints documented and testable.
- **Frontend tooling** using [Twig](https://twig.symfony.com/) and [NodeJS/NPM](https://www.npmjs.com/) for client-side assets, including `build` and `watch` scripts to manage JavaScript/CSS bundling.

The file architecture features a modular design with several functional modules:

| Module | Responsibility |
|---|---|
| Admin module | Manages administrator users |
| App module | Houses core functions like authentication and error reporting |
| Setting module | Stores and manages display or application settings for various report pages |

## How They Work Together

- The [REST API](https://github.com/dotkernel/api) is designed to serve JSON endpoints for clients, services and mobile apps alike.
- The [Admin UI](https://github.com/dotkernel/admin) is a web-based interface that communicates with the database via its own endpoints to manage data, users and settings.

One of the greatest benefits to using both Dotkernel API and Admin is the **unified tech stack**. Both use the [Mezzio microframework](https://docs.mezzio.dev/), [Laminas components](https://docs.laminas.dev/), [Doctrine ORM](https://www.doctrine-project.org/) for database storage and object mapping, [PHP FIG's PSR standards](https://www.php-fig.org/), and share modules like authentication and `dot-rbac`.

Put together, these ensure consistency across backend and admin tooling, while also making it easier to switch between developing one or the other. The similar architecture normally means fewer developers are needed to manage the codebases.

## Summary

Dotkernel Admin is a complementary admin panel that provides effective reporting out of the box and is a good starting point for admin requirements. It's not bundled by default, but is provided as a first-party, installable package designed to work seamlessly alongside the API.

Together, the two applications form a cohesive system: you normally start with the API backend and add the rich administrative interface to complete your admin platform. Both are built with the same underlying file architecture and implement the same PHP standards - [PSR-7 (HTTP messages)](https://www.php-fig.org/psr/psr-7/) and [PSR-15 (middleware)](https://github.com/php-fig/http-server-handler).

Benefits of both components:

- Fully open-source and in active development.
- Highly configurable to suit exact requirements.
- Guarantee a future-proof system using the best practices recommended by the PHP community.

## FAQ

**Q: What is Dotkernel Admin?**
A: Dotkernel Admin is a fully-functional, independent application that lives in its own separate repository. It's built on the same Mezzio + Laminas foundation as Dotkernel API and includes a built-in UI with authentication, user management and a web interface.

**Q: How do I install Dotkernel Admin?**
A: Install it via Git into the folder of your choice with `git clone https://github.com/dotkernel/admin.git .`, then follow the step-by-step installation guide to configure the database credentials and run the migrations and fixtures that create and populate the database. Be sure to change the default credentials before going to production.

**Q: What features does Dotkernel Admin include out of the box?**
A: It ships with Role-Based Access Control powered by dot-rbac and dot-rbac-guard to manage permissions, routes and handler-level security; OpenAPI annotations in each module for documented and testable admin endpoints; and frontend tooling built on Twig and NodeJS/NPM, including build and watch scripts for JavaScript/CSS bundling.

**Q: What modules make up Dotkernel Admin's architecture?**
A: The file architecture is modular, featuring an Admin module that manages administrator users, an App module that houses core functions like authentication and error reporting, and a Setting module that stores and manages display or application settings for various report pages.

**Q: How do Dotkernel API and Admin work together?**
A: The REST API serves JSON endpoints for clients, services and mobile apps, while the Admin UI is a web-based interface that communicates with the database via its own endpoints to manage data, users and settings. Both share a unified tech stack - Mezzio, Laminas components, Doctrine ORM and PHP FIG's PSR standards - plus shared modules like authentication and dot-rbac, which keeps backend and admin tooling consistent and lets fewer developers manage both codebases.

## Resources

- [Dotkernel Admin full documentation](https://docs.dotkernel.org/admin-documentation/)
- [Dotkernel Admin Installation](https://docs.dotkernel.org/admin-documentation/v5/installation/getting-started/)
- [Dotkernel Repositories](https://github.com/dotkernel)
- [PHP Framework Interop Group Standards](https://www.php-fig.org/)
