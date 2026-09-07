---
title: "Dotkernel API v6: The root of Dotkernel Headless Platform"
description: "Overview of what changed in Dotkernel API v6 since the original architecture and components article, covering new features, the Core module, composer updates, and testing/configuration changes."
author: "Florin Bidirean"
date_published: "2025-07-22"
canonical_url: "https://www.dotkernel.com/headless-platform/dotkernel-api-v6-the-root-of-dotkernel-headless-platform/"
category: "Headless Platform"
language: "en"
---

# Dotkernel API v6: The root of Dotkernel Headless Platform

## TL;DR
Dotkernel API has evolved significantly since its original architecture and components article, adding Content Negotiation, standardized error responses via mezzio-problem-details, a shareable Core module, a custom templating solution replacing Twig, and a leaner handler dependency setup.
Packages were updated across the board, the test suite switched from Psalm to PHPStan at a stricter rule level, and the roadmap for v6.1 targets Service Manager 4 and PHP 8.4/8.5 support.

Dotkernel API has come a long way since we published a list of its [architecture and components](https://www.dotkernel.com/dotkernel3/dotkernel-api-architecture-and-components/) a while ago. We implemented new features, while some components were replaced, and others were enhanced. Our ultimate goal is to stay relevant in the PHP ecosystem by implementing best practices recommended by the PHP community.

## New Features

- **Content Negotiation** makes it possible for diverse systems to work seamlessly together. It defines the communication parameters between client and server to make sure both sides agree on how the data exchange takes place.
- The implemented of [mezzio-problem-details](https://github.com/mezzio/mezzio-problem-details) is used to return **standardized error codes**. The generated problem details responses are based on either PHP primitives or exceptions/throwables. This ultimately helps **error reporting** system-wide.
- The common logic is now moved to the **Core module**. This also allows an easier **sharing** of the **functionality** within Core with other Dotkernel applications. To share the Code module, you need to save it as a separate Git repository which can then be added as a **submodule** to any Dotkernel application.
- Replaced Twig with **custom templating** solution to avoid using [mezzio-twigrenderer](https://github.com/mezzio/mezzio-twigrenderer). This ultimately offers more reliable template handling as the small cost of some features that are normally not used in APIs.
- Handle delegators and injected InputFilters were refactored to **reduce the number of dependencies in handlers**.

Some smaller changes are listed below:

- Added OpenAPI documentation for all endpoints.
- Added .gitattributes to set some git properties based on path and filename.
- Implemented enums in the database, where relevant, mostly as a proof of concept.

### Coming Soon

Currently we support:

- Service Manager version 3 - restricted because of constraints from some dependencies.
- PHP 8.3.

This setup is soon going to change. The very next version in the Roadmap for version 6.1 will implement:

- Service Manager 4.
- Support for PHP 8.4 and 8.5.

## Composer updates

As with all regular updates, all **packages** were **updated to their most recent versions** that still allow for an installable set of dependencies.

One of the packages that stands out is [ramsey/uuid](https://github.com/ramsey/uuid). **UUID version 7** is already being used for ramsey/uuid version 4 and thus will be available with no additional code changes for the release of ramsey/uuid version 5.

## Tweaked configuration and testing

Alongside some smaller changes related to cache configuration for doctrine and route grouping, the **test suite** has been revised. [Psalm](https://psalm.dev/) has been replaced with [PHPStan](https://phpstan.org/). This decision was made to remain in line with developers from popular projects like Doctrine and Composer who have also made the switch recently. A separate upgrade was dedicated to increasing the **rule level to 8** which enables stricter static analysis and reveales more potential errors.

## FAQ

**Q: What are the main new features in Dotkernel API v6?**
A: Dotkernel API v6 adds Content Negotiation for smoother communication between client and server, standardized error codes via mezzio-problem-details, a new Core module that centralizes common logic for easier sharing across Dotkernel applications, a custom templating solution that replaces Twig/mezzio-twigrenderer, and refactored handle delegators and injected InputFilters that reduce the number of dependencies in handlers.

**Q: Why was common logic moved into a Core module?**
A: Moving the shared logic into a Core module makes it easier to reuse that functionality across other Dotkernel applications. The Core module can be saved as a separate Git repository and then added as a submodule to any Dotkernel application.

**Q: Why did Dotkernel API replace Twig with a custom templating solution?**
A: Twig was replaced to avoid depending on mezzio-twigrenderer. The custom solution offers more reliable template handling, at the small cost of some features that are normally not used in APIs anyway.

**Q: What is planned for Dotkernel API v6.1?**
A: Dotkernel API currently supports Service Manager version 3, restricted because of constraints from some dependencies, and PHP 8.3. The next version on the roadmap, v6.1, is planned to add support for Service Manager 4 as well as PHP 8.4 and 8.5.

**Q: Why was Psalm replaced with PHPStan in the test suite?**
A: Psalm was replaced with PHPStan to remain in line with developers from popular projects like Doctrine and Composer, who have also made the switch. A separate upgrade also increased the rule level to 8, enabling stricter static analysis and revealing more potential errors.
