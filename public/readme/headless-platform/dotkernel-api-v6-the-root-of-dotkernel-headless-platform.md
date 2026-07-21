---
title: "Dotkernel API v6: The root of Dotkernel Headless Platform"
description: "Overview of what changed in Dotkernel API v6 since the original architecture and components article, covering new features, the Core module, composer updates, and testing/configuration changes."
author: "Florin Bidirean"
date_published: "2025-07-22"
canonical_url: "https://new.dotkernel.com/headless-platform/dotkernel-api-v6-the-root-of-dotkernel-headless-platform/"
category: "Headless Platform"
language: "en"
---

# Dotkernel API v6: The root of Dotkernel Headless Platform

## TL;DR

Dotkernel API has evolved significantly since its original architecture and components article, adding Content Negotiation, standardized error responses via mezzio-problem-details, a shareable Core module, a custom templating solution replacing Twig, and a leaner handler dependency setup. Packages were updated across the board, the test suite switched from Psalm to PHPStan at a stricter rule level, and the roadmap for v6.1 targets Service Manager 4 and PHP 8.4/8.5 support.

## New Features

- **Content Negotiation** makes it possible for diverse systems to work seamlessly together, defining the communication parameters between client and server so both sides agree on how the data exchange takes place.
- **Standardized error codes**, implemented via [mezzio-problem-details](https://github.com/mezzio/mezzio-problem-details), returning problem details responses based on either PHP primitives or exceptions/throwables to help error reporting system-wide.
- A new **Core module** holding common logic, making it easier to share that functionality with other Dotkernel applications. To reuse it, the Core module is saved as a separate Git repository, which can then be added as a submodule to any Dotkernel application.
- **Custom templating solution** replacing Twig, avoiding the use of [mezzio-twigrenderer](https://github.com/mezzio/mezzio-twigrenderer). This offers more reliable template handling, at the small cost of some features that are normally not used in APIs.
- Refactored **handle delegators** and **injected InputFilters**, reducing the number of dependencies in handlers.

Smaller changes:

- Added OpenAPI documentation for all endpoints.
- Added `.gitattributes` to set some git properties based on path and filename.
- Implemented enums in the database, where relevant, mostly as a proof of concept.

## Coming Soon

| | Currently supported | Planned for v6.1 |
|---|---|---|
| Service Manager | Version 3 (restricted because of constraints from some dependencies) | Version 4 |
| PHP | 8.3 | 8.4 and 8.5 |

## Composer Updates

All packages were updated to their most recent versions that still allow for an installable set of dependencies.

One package that stands out is [ramsey/uuid](https://github.com/ramsey/uuid). UUID version 7 is already being used in ramsey/uuid version 4, so it will be available with no additional code changes once ramsey/uuid version 5 is released.

## Tweaked Configuration and Testing

Alongside smaller changes related to cache configuration for Doctrine and route grouping, the test suite has been revised: [Psalm](https://psalm.dev/) has been replaced with [PHPStan](https://phpstan.org/), a decision made to remain in line with developers from popular projects like Doctrine and Composer, who have also made the switch recently. A separate upgrade increased the rule level to 8, enabling stricter static analysis and revealing more potential errors.

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

## Resources

- [Dotkernel API architecture and components (original article)](https://www.dotkernel.com/dotkernel3/dotkernel-api-architecture-components/)
- [mezzio-problem-details on GitHub](https://github.com/mezzio/mezzio-problem-details)
- [mezzio-twigrenderer on GitHub](https://github.com/mezzio/mezzio-twigrenderer)
- [ramsey/uuid on GitHub](https://github.com/ramsey/uuid)
- [Psalm](https://psalm.dev/)
- [PHPStan](https://phpstan.org/)
