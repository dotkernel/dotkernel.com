---
title: "What \"Production Ready\" Means for Dotkernel API"
description: "What Dotkernel API's production-ready claim actually covers, what still sits on you (rate limiting, gateway, federated identity, error tracking, health checks, caching), and where to read the full checklist before going live."
author: "arhimede"
date_published: "2026-09-09"
canonical_url: "https://www.dotkernel.com/dotkernel-api/what-production-ready-means-for-dotkernel-api/"
category: "Dotkernel API"
language: "en"
---

# What "Production Ready" Means for Dotkernel API

## TL;DR

Dotkernel API ships a complete, tested application layer — pipeline, OAuth2 auth, RBAC, validation, error responses, OpenAPI. It does not ship the infrastructure layer around it: rate limiting, an API gateway, federated identity, error tracking, health checks, or caching/background jobs. Those are deliberate boundaries, not gaps, and the linked documentation page lists each one with what to configure before going live.

Dotkernel API describes itself as production ready, and it is — but the phrase covers less ground than most people assume, and the difference is worth naming.

## What Ships in the Box

A REST API needs a lot of machinery before it can serve a single useful request.
Dotkernel API brings all of it already assembled: a readable middleware pipeline, OAuth2 authentication, role-based access control, request validation, standardised error responses and a generated OpenAPI specification.
None of that is scaffolding you finish yourself.
It is written, tested and documented, and it is the reason you can go from an empty project to a working endpoint in an afternoon.

That is what production ready means here.
The application is complete.

## What You Still Own

What it does not mean is that everything a public API needs on the day it goes live is included.
Around the application sits a second layer, and that layer is yours:

- **Traffic control.** Nothing limits how fast a client can call your endpoints.
    Your login endpoint will answer a brute-force attempt as patiently as it answers a real user.
- **An API gateway.** Certificates, per-customer quotas, API keys, edge caching and a web application firewall all live in front of the application, not inside it.
- **Federated identity.** Dotkernel API issues its own tokens.
    If your organisation signs in through Keycloak, Auth0, Microsoft Entra ID or Okta, connecting the two is work you do.
- **Somewhere for errors to go.** Errors are written to a file on the server.
    They are not sent to Sentry or any other tracking service, which means that by default, nobody is told when something breaks.
- **Health checks and monitoring.** There is no endpoint a load balancer can ask "are you actually working?", and no metrics coming out of the application at all.
- **Caching and background work.** Responses are not cached, and email is sent while the user waits.

None of these are bugs.
Most of them are decisions.

## Why the Line Is Drawn There

Rate limiting is the clearest example.
It genuinely belongs in your infrastructure, where it can protect every node at once, rather than inside application code that only sees its own traffic.
The same argument applies to certificates, secrets and log shipping.
A framework that shipped opinionated versions of all of these would be a framework you spend your first week fighting.

The real problem was never the missing pieces.
It was that nobody wrote down which pieces were missing.
A team can reasonably read "production ready", deploy on Friday, and only discover on Monday that the login endpoint has no rate limit — not because they were careless, but because nothing told them that half was theirs.

## Where the Line Is Moving

Some of it is moving inward.
There are open proposals to add a health check endpoint, to investigate a rate limiting middleware for demonstration purposes, and to extend caching.
The boundary is not fixed, and it is being discussed in the open.

## Start Here

We have added a page to the documentation that names every gap, says whether it belongs in your application or in the platform in front of it, and gives you the concrete thing to configure for each one.
It closes with a short ordered checklist of what to do before you go live.

Read it before your first deployment, not after:

**[Production Readiness — Dotkernel API documentation](https://docs.dotkernel.org/api-documentation/v7/reference/production-readiness/)**

## FAQ

**Is Dotkernel API safe to run in production?**

Yes, with the layer around it in place.
The application itself is complete and tested; what needs attention is the infrastructure it runs on and a handful of settings you should review before launch.

**Is this different from other PHP frameworks?**

Not really.
Very few frameworks ship rate limiting, monitoring or identity federation, and the ones that do usually ship a version you end up replacing.
What is different here is that we have written down where the line falls instead of leaving you to find it.

**How long does the missing half take?**

Less time than you would expect, if you know about it in advance.
Most of the list is configuration in your web server or gateway rather than code, and the documentation page gives you a priority order so you can start with what matters most.

**Will these become part of Dotkernel API?**

Some of them.
Health checks, caching and a demonstration rate limiter are all under discussion.
Others, such as certificates and secret storage, will stay where they belong — in your infrastructure.

## Resources

- [Production Readiness](https://docs.dotkernel.org/api-documentation/v7/reference/production-readiness/) — the full documentation page
- [Basic Security](https://docs.dotkernel.org/api-documentation/v7/security/basic-security/) — application-level hardening before launch
- [Dotkernel API on GitHub](https://github.com/dotkernel/api) — issues and open proposals
