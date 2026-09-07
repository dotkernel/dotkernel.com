---
title: "Dotkernel Light - Starting with Mezzio microframework and Laminas components"
description: "Dotkernel Light is a stripped-down version of Dotkernel Frontend built on Mezzio and Laminas components, keeping only routing, templating, error handling, and tests, for a gentler learning curve."
author: "Florin Bidirean"
date_published: "2024-10-03"
canonical_url: "https://www.dotkernel.com/dotkernel/dotkernel-light-starting-with-mezzio-microframework-and-laminas-components/"
category: "Dotkernel"
language: "en"
---

# Dotkernel Light - Starting with Mezzio microframework and Laminas components

## TL;DR
Dotkernel Light is a version of Dotkernel Frontend that includes only the bare-bones essentials.
It's built on the Mezzio microframework using Laminas components, and is designed as a presentation site, a fast-start introduction to Mezzio, or a clean starting point for a project where you want full control over functionality.

**Dotkernel Light** is the smallest complete Mezzio application that includes only the bare-bones essentials. Though simpler than Frontend, it's perfect for:

- A presentation site,
- An introduction into the Mezzio microframework architecture,
- A starting point for a more complex project where you have full control over functionality.

## Goal

**Dotkernel Light** is the smallest complete Mezzio application, designed at the same time to be a fast-start example of using **Mezzio microframework**, as well as of using an **entry-level version** of Dotkernel Frontend. Primarily, its purpose is to present the **novice developer** with as few moving parts as possible. It's also a perfect starting point for the more **advanced developer** who wants full control of the platform's functionality.

Light retains the modern architecture of **Mezzio microframework** and several **Laminas components** used in Dotkernel Frontend. The low number of out-of-box components **encourages the active exploration of functionality** required by your application. Basically, you add only the packages your application needs.

## Components and functionality

We designed Dotkernel Light to be the smallest complete Mezzio application, a stripped-down version of Dotkernel Frontend. Just like Dotkernel Frontend, Dotkernel Light is built on top of the Mezzio microframework using Laminas components. The big difference is the limited features and thus the lower number of packages. This makes the **learning curve** of working with the repo considerably gentler.

Currently, Light contains this **functionality**:

- Routing
- Templating
- Error handling
- Tests and code quality checks

Compared to Frontend, Light had these **items removed**:

- Doctrine and all database related stuff
- Sessions/Cookies/Flash messages
- Authentication/Authorization
- Dependency Injection
- Mail related stuff
- Navigation
- CORS
- Forms/Validators/InputFilters
- User module
- Contact module
- Plugin module
- CSS/JS code that was no longer in use due to the above module removals
- Instructions from README.md that are no longer needed

As expected, several packages used by Frontend are not required:

- dotkernel/dot-authorization
- dotkernel/dot-data-fixtures
- dotkernel/dot-dependency-injection
- dotkernel/dot-flashmessenger
- dotkernel/dot-mail
- dotkernel/dot-navigation
- dotkernel/dot-rbac-guard
- dotkernel/dot-response-header
- dotkernel/dot-session
- laminas/laminas-form
- laminas/laminas-i18n
- mezzio/mezzio-authorization-rbac
- mezzio/mezzio-cors
- ramsey/uuid-doctrine
- roave/psr-container-doctrine
- mezzio/mezzio-tooling
- rector/rector

## Useful links

- [Dotkernel Light GitHub repository](https://github.com/dotkernel/light)
- [Working demo of Dotkernel Light](https://light.dotkernel.net/)
- [Dotkernel Light documentation](https://docs.dotkernel.org/light-documentation/)
- [Laminas Project](https://getlaminas.org/)
- [Documentation of Mezzio](https://docs.mezzio.dev/)

## FAQ

**Q: What is Dotkernel Light?**
A: Dotkernel Light is the smallest complete Mezzio application, a PSR-15 pipeline, routing and templating, with nothing to strip out. It's a good base for a presentation site, an introduction to the Mezzio microframework architecture, or a starting point for a more complex project where you want full control over functionality.

**Q: What is the goal of Dotkernel Light?**
A: It's designed to be the smallest complete Mezzio application. It's a fast-start example of using the Mezzio microframework as well as an entry-level version of Dotkernel Frontend. It presents the novice developer with as few moving parts as possible, while still allowing the more advanced developer to have full control of the platform's functionality.

**Q: What functionality does Dotkernel Light retain?**
A: It keeps routing, templating, error handling, and tests and code quality checks.

**Q: What was removed compared to Dotkernel Frontend?**
A: Items removed include Doctrine and all database related stuff, sessions/cookies/flash messages, authentication/authorization, dependency injection, mail related stuff, navigation, CORS, forms/validators/input filters, the User module, the Contact module, the Plugin module, unused CSS/JS code, and outdated README instructions.

**Q: Which packages are no longer required in Dotkernel Light?**
A: Packages such as dotkernel/dot-authorization, dotkernel/dot-mail, dotkernel/dot-session, dotkernel/dot-navigation, dotkernel/dot-flashmessenger, laminas/laminas-form, mezzio/mezzio-cors, and several others used by Frontend are not required.
