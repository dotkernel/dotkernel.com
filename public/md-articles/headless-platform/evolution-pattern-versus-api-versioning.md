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

In programming and software architecture, an **Evolution Pattern** is a reusable, high-level strategy for modifying or evolving existing software systems over time. An evolution pattern tries to keep software relevant for old and new users by whatever means are available, as new needs arise.

**Versioning** in software is the practice of assigning unique identifiers (version numbers) to different releases of a software platform. These numbers help track changes, improvements, bug fixes, compatibility, and evolution over time. In particular, API versioning is the practice of assigning versions to an API so that it can change over time without breaking existing clients that rely on it.

## When to Use Evolution Pattern versus Versioning

Technically, the two concepts are not in conflict:

- An evolution pattern keeps the same codebase and announces compatibility breaking updates via mechanisms like sunsetting which we will discuss later on.
- API versioning focuses on maintaining multiple versions of an API so changes do not affect users that implemented older versions.

You can very well have a platform employ both strategies, if it suits your needs.

| Problem | Potential Solution | Strategy |
| --- | --- | --- |
| Fix a typo in the parameter name | Sunsetting / Do nothing (it's only a typo) | Evolution pattern. Versioning is overkill. |
| Add a new parameter | Make it optional, so it doesn't break existing implementations | Evolution pattern |
| Implement a BC breaking update that clients must adhere to | Announce it in time to allow your clients to adapt | Evolution pattern |
| Implement a major update to an endpoint | Add a `v2` for only that endpoint, not the whole API, and encourage clients to migrate to v2 | Evolution pattern + Versioning |
| Return responses in a totally different format (e.g. GraphQL vs REST APIs) | Keep both versions active | Versioning |

The responsiblity is yours to decide if the costs for development and maintenance are better aimed at one strategy over the other.

## Practical Example from Dotkernel API

We try to stay away from full rewrites. They are too risky, costly, and often times simply not the best solution. Software evolution should be gradual and organic, with the purpose of adapting to new requirements with as little impact to the codebase and clients as possible.

For our Dotkernel API, we chose an evolution pattern that takes advantage of a sunsetting mechanism. The codebase remains the same, with new requirements being implemented in a targeted manner.

If we are required to enhance an endpoint, one solution we employ is to distinguish between two calls based on their payload. For example, if the 3rd-party frontend calls our `/user` endpoint:

- With only an `email`, we use the older functionality and return the response that is currently working for clients.
- With an `email and a name`, we return an enhanced response. This requires clients to update their own code to handle the new structure of the API response.

We announce endpoint deprecation by including an updated response header via handler attributes.

```
...
#[ResourceDeprecation(
    sunset: '2026-01-01',
    link: 'https://docs.dotkernel.org/api-documentation/v7/tutorials/api-evolution/',
    deprecationReason: 'Resource deprecation example.',
    rel: 'sunset',
    type: 'text/html'
)]
class OldHandler implements RequestHandlerInterface
{
...
```

The values for `sunset`, `link` and `deprecationReason` above are customizable. By including this attribute, the reponse for the edited endpoint looks similar to the below.

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

This header must be read and understood by developers who can then plan an update for their own software before the sunsetting date. The new functionality is normally kept in place alongside the old one for a reasonable amount of time. When the sunset date is reached, the old functionality is removed and only the new one is left in production:

- An endpoint would return `404 Not Found`.
- A property would be removed from the response.

We use multiple channels to announce impactful, backward-incompatible updates for Dotkernel API:

- **API headers** include the deprecation header.
- **Documentation pages** detail the change and recommended code updates.
- Announcements in the **public portal**, like articles and newsletters, are added to our blog.

You should send out notifications early enough for them to reach the developers of 3rd-party software that use your API. This way they can plan and implement the update to ensure that their platforms have little or no downtime.

## Conclusions

An evolution pattern requires good planning, which includes allocating enough time for your clients to be aware of your updates and to implement them in their own platforms. Ideally, there should be no downtime for either your, or your clients' platforms.

Though we see it as avoidable in most real-life cases, versioning is still a valid strategy. We strongly believe it should be reserved for impactful architectural changes, as it's not suited for smaller changes like fixing typos in a parameter name or adding a new field.

The strategies discussed in this article are not exhaustive, because ultimately you have to decide what approach works best for your use case.

## Additional Resources

- [What is the HTTP `Sunset` header?](https://sophiabits.com/blog/what-is-the-http-sunset-header)
- [Deprecating Resources and Properties](https://api-platform.com/docs/core/deprecations)
- [API Evolution for REST/HTTP APIs](https://philsturgeon.com/api-evolution-for-rest-http-apis)
- [Just say no - to versioning APIs](https://www.hmeid.com/blog/just-say-no-to-versioning)
- [Ways to version your API, Part 2](https://urthen.github.io/2013/05/16/ways-to-version-your-api-part-2)

## FAQ

**Q: What is the difference between an Evolution Pattern and API versioning?**
A: An Evolution Pattern is a reusable, high-level strategy for modifying or evolving existing software systems over time while keeping the same codebase, often using mechanisms like sunsetting. API versioning instead maintains multiple versions of an API so that changes do not affect clients relying on older versions. The two concepts are not in conflict, and a platform can use both strategies if it suits its needs.

**Q: When should you use an evolution pattern instead of versioning?**
A: Small changes, like fixing a typo in a parameter name or adding a new optional parameter, are best handled with an evolution pattern, since versioning would be overkill. Backward-compatible-breaking updates should be announced in time via an evolution pattern. A major update to a single endpoint can combine an evolution pattern with versioning, for example by adding a v2 for just that endpoint. Only when responses need to be returned in a totally different format, such as GraphQL versus REST, is versioning the better fit.

**Q: How does Dotkernel API announce endpoint deprecations?**
A: Dotkernel API distinguishes calls based on payload (for example, an older response for a request with only an email, versus an enhanced response when both email and name are supplied). Deprecation is announced by including an updated response header via handler attributes, with customizable values for sunset, link, and deprecationReason. These updates are also communicated through documentation pages and public portal channels like blog articles and newsletters.

**Q: What happens once an endpoint's sunset date is reached?**
A: The old functionality is removed and only the new functionality remains in production. In practice this means an endpoint may start returning 404 Not Found, or a property may be removed from the response.
