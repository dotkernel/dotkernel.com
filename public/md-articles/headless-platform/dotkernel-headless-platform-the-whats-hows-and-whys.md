---
title: "Dotkernel Headless Platform - The Whats, Hows and Whys"
description: "Overview of the Dotkernel Headless Platform architecture, its two main components (Dotkernel API and Dotkernel Admin), how to use them together or separately, and the benefits of the combined setup."
author: "Florin Bidirean"
date_published: "2025-06-13"
canonical_url: "https://www.dotkernel.com/headless-platform/dotkernel-headless-platform-the-whats-hows-and-whys/"
category: "Headless Platform"
language: "en"
---

# Dotkernel Headless Platform - The Whats, Hows and Whys

## TL;DR

A Headless Platform decouples the frontend (UI) from the backend services, with responses consumed by another system such as a website or mobile app. The Dotkernel Headless Platform is made up of Dotkernel API (a REST API based on the Mezzio skeleton) and Dotkernel Admin (a backend management interface), which can be installed separately or together.
Using both together, sharing a common Core module, gives consistent entities and queries, an easy-to-maintain shared file structure, and an architecture that scales from small microservices to enterprise-grade APIs.

## Components of the Dotkernel Headless Platform

The Dotkernel Headless Platform contains two major components:

- [Dotkernel API](https://www.dotkernel.org) — a REST API based on the [Mezzio skeleton](https://github.com/mezzio/mezzio-skeleton).
- [Dotkernel Admin](https://github.com/dotkernel/admin) — a complementary component aimed at quickly setting up a backend for your platform.

The Admin is not bundled by default but is available as a first-party, installable application designed to work seamlessly alongside your API.
Together, they form a cohesive system: an API-first backend paired with a rich administrative interface, both built on the same underlying architecture and standards.

Both components use a PSR-compliant middleware stack, implementing [PSR-7](https://www.php-fig.org/psr/psr-7) and [PSR-15](https://www.php-fig.org/psr/psr-15) as defined by the [PHP Framework Interop Group](https://www.php-fig.org/), promoting a lean, modular architecture.

Rather than relying on elements built into the framework, business logic is built from scratch — from handlers to dependencies — giving full control over business logic and data architecture.

## How to Use the Dotkernel Headless Platform

Dotkernel API and Admin can be installed separately or together, based on business requirements.
They are normally designed to complement each other:

- Dotkernel **Admin** manages the data (create, edit, delete).
- Dotkernel **API** exposes the content to 3rd-party frontends or backends.

A simple project can be designed around one of the two at first, then expand to the other:

- **Start with Dotkernel Admin** to populate your database, then later use Dotkernel API to present the data to other frontends written in any programming language, integrating with 3rd-party applications for full control over how data is presented.
- **Start with Dotkernel API** and integrate it into an existing platform.
The API can manage access permissions to keep data secure — admins create and edit data for the backend, while users read data for the frontend.
Admin can be added later for its simple table-based approach, reports, and graphs.

The recommendation is to use both API and Admin from the start.

## Benefits of the Dotkernel Headless Platform Architecture

Using both API and Admin together brings several benefits:

### Common code between applications

The file structure can be configured to use a **Core** module — a common code repository shared between API and Admin, ensuring entities and queries stay consistent.
Entities (products, articles, etc.) are the building blocks of the application, and queries handle CRUD interactions with them.

### Implement only the handlers you need

Having many entities in the Core doesn't require implementing handlers for each one in both API and Admin.
Each application only handles what it needs — for example, a shop may need to edit orders in Admin but only build monthly sales reports via the API.

### Shared file structure

API and Admin share a file structure, so becoming familiar with one makes the other easy to pick up.
This means onboarding is easy and the applications can be maintained by fewer developers — potentially a single developer, at least initially.
By contrast, an API+Angular approach often needs two developers working together to implement a new feature.

### Can satisfy any application size

Thanks to the shared Core module, API and Admin evolve together, forming a good starting point for anything from microservices to enterprise-grade APIs.
A large number of packages are available for integration, and developers decide exactly what modules to include, keeping the finished application lightweight and preventing bloat and long-term technical debt.

### Versatile architecture

The Dotkernel architecture aims to support several architectural designs:

- **Clean Architecture** — software components are organized into concentric layers, with code dependencies flowing from outer to inner layers, improving testability and independence from libraries, UI, and databases.
- **Domain-Driven Design (DDD)** — the focus is on custom solutions for business logic; rather than building reusable services for a Service Oriented Architecture (SOA), only the specific components needed to satisfy requirements are implemented.
- **Hexagonal Architecture** — divides a system into several loosely-coupled, interchangeable components (application core, database, user interface, test scripts, interfaces with other systems), as an alternative to traditional layered architecture.

### Supported by an active community

The Dotkernel development team actively investigates and implements recommended design patterns.
Dotkernel applications and packages continue to receive updates with bugfixes, improvements, and recommendations from the PHP community.
Some updates may break backward compatibility, but this is typically highlighted in companion articles that may include step-by-step tutorials.
The Dotkernel team is available to help with issues related to Dotkernel and Laminas apps and components.

## FAQ

**Q: What is a Headless Platform?**
A: A Headless Platform decouples the User Interface (frontend) from the backend services.
The responses from the platform are then used by another system, such as a website or mobile app.

**Q: What are the two main components of the Dotkernel Headless Platform?**
A: The Dotkernel Headless Platform contains two major components: Dotkernel API, a REST API based on the Mezzio skeleton, and Dotkernel Admin, a complementary component aimed at quickly setting up a backend.
The Admin is not bundled by default but is available as a first-party, installable application designed to work alongside the API.

**Q: Can Dotkernel API and Admin be installed separately?**
A: Yes.
They can be installed separately or together, based on your business requirements.
You can start with Dotkernel Admin to populate your database and later add Dotkernel API to present the data to other frontends, or start with Dotkernel API and integrate it into your existing platform, adding Admin later for its table-based approach, reports, and graphs.

**Q: What benefits come from using both API and Admin together?**
A: Using both together lets you share a common Core module containing the same entities and queries between the two applications, implement only the handlers you actually need, and benefit from a shared file structure that makes onboarding easy and can be maintained by fewer developers.
This setup can also satisfy any application size, from microservices to enterprise-grade APIs.

**Q: What architectural patterns does the Dotkernel Headless Platform follow?**
A: The architecture is designed to be versatile, following Clean Architecture (concentric layers with dependencies flowing from outer to inner layers), Domain-Driven Design (implementing only the specific components needed for your business logic rather than generic reusable services), and Hexagonal Architecture (dividing the system into loosely-coupled, interchangeable components).

## Resources

- [Dotkernel API](https://github.com/dotkernel/api)
- [Dotkernel Admin](https://github.com/dotkernel/admin)
- [Dotkernel repositories](https://github.com/dotkernel)
- [Dotkernel documentation](https://docs.dotkernel.org/)
- [Headless CMS vs. Traditional CMS: What's Right for PHP Apps?](https://www.zend.com/blog/headless-cms-vs-traditional-cms)
- [Laminas GitHub discussions](https://github.com/orgs/laminas/discussions)
- [Dotkernel Discussions](https://github.com/orgs/dotkernel/discussions)
