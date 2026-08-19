---
title: "Evolution Pattern versus API Versioning"
description: "How Dotkernel weighs an evolution pattern against API versioning when changing an API, and the sunsetting mechanism used in Dotkernel API to announce breaking changes."
author: "Florin Bidirean"
date_published: "2025-12-09"
canonical_url: "https://www.dotkernel.com/headless-platform/evolution-pattern-versus-api-versioning/"
category: "Headless Platform"
language: "en"
---

# Evolution Pattern versus API Versioning

## TL;DR

An Evolution Pattern keeps the same codebase and evolves it gradually (for example via sunsetting), while API versioning maintains multiple parallel versions of an API so existing clients aren't broken.
The two are not mutually exclusive.
Dotkernel API favors an evolution pattern with a sunsetting mechanism, reserving full versioning for major, format-level changes.

## Definitions

- **Evolution Pattern**: A reusable, high-level strategy for modifying or evolving existing software systems over time, keeping software relevant for old and new users as new needs arise.
- **Versioning**: The practice of assigning unique version numbers to different releases of a software platform to track changes, improvements, bug fixes, compatibility, and evolution.
API versioning specifically assigns versions to an API so it can change without breaking existing clients.
- **Sunsetting**: A mechanism for announcing that an API's old functionality will be phased out on a given date, via response headers, documentation, and public announcements.

## When to use an Evolution Pattern versus Versioning

Technically, the two concepts are not in conflict - an evolution pattern keeps the same codebase and announces breaking changes via mechanisms like sunsetting, while API versioning maintains multiple versions of an API so that changes don't affect clients using older versions.
A platform can use both strategies together if it suits its needs. The responsibility falls on the team to decide where to invest development and maintenance costs.

| Problem | Potential Solution | Strategy |
|---|---|---|
| Fix a typo in the parameter name | Sunsetting / do nothing (it's only a typo) | Evolution pattern. Versioning is overkill. |
| Add a new parameter | Make it optional, so it doesn't break existing implementations | Evolution pattern |
| Implement a backward-compatibility-breaking update that clients must adhere to | Announce it in time to allow clients to adapt | Evolution pattern |
| Implement a major update to an endpoint | Add a `v2` for only that endpoint, not the whole API, and encourage clients to migrate | Evolution pattern + Versioning |
| Return responses in a totally different format (e.g. GraphQL vs REST APIs) | Keep both versions active | Versioning |

## Practical example from Dotkernel API

Dotkernel avoids full rewrites, considering them too risky, costly, and often not the best solution.
Software evolution should instead be gradual and organic, adapting to new requirements with as little impact to the codebase and clients as possible.

For Dotkernel API, an evolution pattern with a sunsetting mechanism was chosen: the codebase stays the same, and new requirements are implemented in a targeted manner.

To enhance an endpoint, Dotkernel distinguishes between calls based on their payload.
For example, on the `/user` endpoint:

- A request with only an `email` uses the older functionality and returns the response currently working for existing clients.
- A request with both `email` and `name` returns an enhanced response, requiring clients to update their own code to handle the new response structure.

### Announcing deprecation

Deprecation is announced by including an updated response header via handler attributes, with customizable values for `sunset`, `link`, and `deprecationReason`.
A deprecated endpoint's response includes headers similar to:

```
HTTP/1.1 200 OK
Host: 0.0.0.0:8080
Date: Mon, 1 Dec 2025 10:10:10 GMT
Connection: close
X-Powered-By: PHP/6.4.20
Content-Type: application/json
Permissions-Policy: interest-cohort=()
Sunset: 2026-01-01
Link: https://docs.dotkernel.org/api-documentation/v7/tutorials/api-evolution/;rel="sunset";type="text/html"
Vary: Origin
```

Developers must read and understand this header so they can plan an update before the sunset date.
New functionality is normally kept alongside the old functionality for a reasonable period.
Once the sunset date is reached, the old functionality is removed and only the new one remains in production:

- An endpoint may return `404 Not Found`.
- A property may be removed from the response.

Dotkernel uses multiple channels to announce impactful, backward-incompatible updates:

- **API headers** include the deprecation header.
- **Documentation pages** detail the change and recommended code updates.
- **Public portal** announcements, like blog articles and newsletters.

Notifications should go out early enough to reach developers of third-party software using the API, so they can plan and implement updates with little or no downtime.

## Conclusions

An evolution pattern requires good planning, including allocating enough time for clients to be aware of updates and implement them, ideally with no downtime for either side.
Versioning remains a valid strategy but is best reserved for impactful architectural changes rather than smaller changes like fixing a typo or adding a field.
Ultimately, the best approach depends on the specific use case.

## FAQ

**Q: What is the difference between an Evolution Pattern and API versioning?**
A: An Evolution Pattern is a reusable, high-level strategy for modifying or evolving existing software systems over time while keeping the same codebase, often using mechanisms like sunsetting.
API versioning instead maintains multiple versions of an API so that changes do not affect clients relying on older versions.
The two concepts are not in conflict, and a platform can use both strategies if it suits its needs.

**Q: When should you use an evolution pattern instead of versioning?**
A: Small changes, like fixing a typo in a parameter name or adding a new optional parameter, are best handled with an evolution pattern, since versioning would be overkill.
Backward-compatible-breaking updates should be announced in time via an evolution pattern.
A major update to a single endpoint can combine an evolution pattern with versioning, for example by adding a v2 for just that endpoint.
Only when responses need to be returned in a totally different format, such as GraphQL versus REST, is versioning the better fit.

**Q: How does Dotkernel API announce endpoint deprecations?**
A: Dotkernel API distinguishes calls based on payload (for example, an older response for a request with only an email, versus an enhanced response when both email and name are supplied).
Deprecation is announced by including an updated response header via handler attributes, with customizable values for sunset, link, and deprecationReason.
These updates are also communicated through documentation pages and public portal channels like blog articles and newsletters.

**Q: What happens once an endpoint's sunset date is reached?**
A: The old functionality is removed and only the new functionality remains in production.
In practice this means an endpoint may start returning 404 Not Found, or a property may be removed from the response.

## Resources

- [What is the HTTP Sunset header?](https://sophiabits.com/blog/what-is-the-http-sunset-header)
- [Deprecating Resources and Properties](https://api-platform.com/docs/core/deprecations)
- [API Evolution for REST/HTTP APIs](https://philsturgeon.com/api-evolution-for-rest-http-apis)
- [Just say no - to versioning APIs](https://www.hmeid.com/blog/just-say-no-to-versioning)
- [Ways to version your API, Part 2](https://urthen.github.io/2013/05/16/ways-to-version-your-api-part-2)
