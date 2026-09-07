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

The principle of a **Headless Platform** is to decouple the User Interface (**frontend**) from the **backend** services. The responses from the platform are then used by another system, such as a website or mobile app. In this article we will explore the Dotkernel implementation of the **Headless Platform** architecture, how to use it and what are its benefits.

## Components of the Dotkernel Headless Platform

The Dotkernel Headless Platform contains 2 major components:

- [Dotkernel API](https://www.dotkernel.org).
- [Dotkernel Admin](https://github.com/dotkernel/admin).

**Dotkernel API** is a REST API based on the [**Mezzio skeleton**](https://github.com/mezzio/mezzio-skeleton).

**Dotkernel Admin**, a complementary component for the API, is aimed at quickly setting up an backend for your platform.

The Admin is not bundled by default but is available as a first-party, installable application designed to work seamlessly alongside your API. When the API and Admin are put together, you gain a cohesive system that contains an API-first backend with a rich administrative interface, both built with the same underlying architecture and standards support.

Both of the above have a PSR-Compliant Middleware Stack to promote a lean, modular architecture and create a common ground between components from various sources. They implement [PSR-7](https://www.php-fig.org/psr/psr-7) and [PSR-15](https://www.php-fig.org/psr/psr-15) as defined by the [PHP Framework Interop Group](https://www.php-fig.org/).

Rather than making use of elements built into the framework, you **build business logic from scratch**, from handlers, to dependencies. This gives you **full control** over business logic and data architecture.

## How to use the Dotkernel Headless Platform

Dotkernel API and Admin can be **installed separately or together**, based on your business requirements. The two are normally designed to **complement each other**:

- Dotkernel **Admin** manages the data (create, edit, delete).
- Dotkernel **API** exposes the content to 3rd-party frontends or backends.

A simple project can be designed around one of the two at first, then expand to the other. For example, you can **start with Dotkernel Admin** to populate your database. Later on you can employ Dotkernel API to present the data to other frontends written in your programming language of choice. You can integrate the API with 3rd-party applications which gives you full control over how you present the data.

Alternatively, you can **start with Dotkernel API** and integrate it into your existing platform. The API can manage the access permissions to keep your data secure:

- Admins create and edit the data for your existing **backend**.
- Users read the data for your **frontend**.

The Admin can be added later on for its simple table-based approach, its reports and graphs.

Our recommendation is to use both API and Admin from the get go. We will highlight the benefits of this setup in the next chapter.

## Benefits of the Dotkernel Headless Platform architecture

As we mentioned before, you decide what components of the Dotkernel Headless Platform are installed in your project. If you decide to use both, you gain several benefits, as we will highlight below.

### Can be set up to have common code between applications

You can configure the file structure to use a **Core** module. The Core then becomes the common code repository between API and Admin, ensuring that the entities and queries are the same. The **entities** for your platform, whether they be products, articles or something else are the building blocks of your application. The **queries** interact with the entities for CRUD purposes. By having these components in a single repository used in both API and Admin, you ensure consistency when interacting with them.

![](/uploads/article/019f8a80-cc94-7123-80d3-9c21a30c19ea/Core2.png)

### Implement only the handlers you need

Just because you have e.g. 20 entities in your Core doesn't mean you have to implement handlers for each one in both API and Admin. You only **handle what you need** in each application. For example, your shop may need to edit orders in Admin, but only build monthly sales reports via your API.

### Has a shared file structure

Another advantage is the fact that the API and Admin **share a file structure**. When you become familiar with one, you're good to go with the other as well. This means that **onboarding is easy** and can be **maintained by fewer developers**, maybe even a single developer, at least in the beginning. Conversely, working with an API+Angular approach means you often need 2 developers working in concert to implement any new feature.

![](/uploads/article/019f8a80-cc94-7123-80d3-9c21a30c19ea/files-api.jpg)

![](/uploads/article/019f8a80-cc94-7123-80d3-9c21a30c19ea/files-admin-1.jpg)

### Can satisfy any application size

Thanks to its use of the common Core module, the API and Admin evolve together. The pair are a good starting point for anything **from microservices, to enterprise-grade APIs**. The initial learning curve is worth it, considering you have a large number of packages available for integrating into your platform. You decide exactly what modules to include, meaning the finished application is more lightweight. This prevents bloat and helps with technical debt in the long run.

### Has a versatile architecture

The aim of the architecture implemented in Dotkernel applications is to provide solutions that follow several architectural designs.

#### Clean Architecture

The software components are organized into concentric layers. Code dependencies go from the outer to the inner layers which results in improved testability and independence from components like libraries, user interfaces and databases.

![](/uploads/article/019f8a80-cc94-7123-80d3-9c21a30c19ea/Flow2.png)

#### Domain-Driven Design (DDD)

The main focus of the Dotkernel applications is to provide a custom solution for your business logic. Rather than implementing reusable services for a Service Oriented Architecture (SOA), we encourage DDD which only implements the specific components to satisfy your requirements.

#### Hexagonal Architecture

The hexagonal architecture divides a system into several loosely-coupled interchangeable components, such as the application core, the database, the user interface, test scripts and interfaces with other systems. This approach is an alternative to the traditional layered architecture.

### Supported by an active community

The Dotkernel development team is actively working on investigating and implementing recommended design patterns. All Dotkernel applications and a large number of packages are still receiving updates that implement bugfixes, improvements and recommendations from the PHP community. Some updates may brake BC (backward compatibility), but this is often highlighted in companion articles that may also contain step-by-step tutorials. The Dotkernel team is available to help you with any issues related to the Dotkernel and Laminas apps and components.

## Additional Resources

- [Dotkernel API](https://github.com/dotkernel/api)
- [Dotkernel Admin](https://github.com/dotkernel/admin)
- [Dotkernel repositories](https://github.com/dotkernel)
- [Dotkernel documentation](https://docs.dotkernel.org/)
- [Headless CMS vs. Traditional CMS: What's Right for PHP Apps?](https://www.zend.com/blog/headless-cms-vs-traditional-cms)
- [Laminas github discussions](https://github.com/orgs/laminas/discussions)
- [Dotkernel Discussions](https://github.com/orgs/dotkernel/discussions)

## FAQ

**Q: What is a Headless Platform?**
A: A Headless Platform decouples the User Interface (frontend) from the backend services. The responses from the platform are then used by another system, such as a website or mobile app.

**Q: What are the two main components of the Dotkernel Headless Platform?**
A: The Dotkernel Headless Platform contains two major components: Dotkernel API, a REST API based on the Mezzio skeleton, and Dotkernel Admin, a complementary component aimed at quickly setting up a backend. The Admin is not bundled by default but is available as a first-party, installable application designed to work alongside the API.

**Q: Can Dotkernel API and Admin be installed separately?**
A: Yes. They can be installed separately or together, based on your business requirements. You can start with Dotkernel Admin to populate your database and later add Dotkernel API to present the data to other frontends, or start with Dotkernel API and integrate it into your existing platform, adding Admin later for its table-based approach, reports, and graphs.

**Q: What benefits come from using both API and Admin together?**
A: Using both together lets you share a common Core module containing the same entities and queries between the two applications, implement only the handlers you actually need, and benefit from a shared file structure that makes onboarding easy and can be maintained by fewer developers. This setup can also satisfy any application size, from microservices to enterprise-grade APIs.

**Q: What architectural patterns does the Dotkernel Headless Platform follow?**
A: The architecture is designed to be versatile, following Clean Architecture (concentric layers with dependencies flowing from outer to inner layers), Domain-Driven Design (implementing only the specific components needed for your business logic rather than generic reusable services), and Hexagonal Architecture (dividing the system into loosely-coupled, interchangeable components).
