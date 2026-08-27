---
title: "Dotkernel API | Open-source REST API skeleton for PHP"
description: "Dotkernel API is an open-source REST API skeleton built on Mezzio, Laminas and Doctrine. OAuth2, RBAC, HAL, problem details and OpenAPI 3.0 come wired together on install. MIT licensed."
canonical_url: "https://www.dotkernel.com/api/"
language: "en"
---

# Dotkernel API

REST API skeleton . PSR-15 middleware

An open-source REST API skeleton for PHP, built on the Mezzio microframework and Laminas components over a Doctrine domain layer.
OAuth 2.0, RBAC authorization, HAL payloads, standardized error responses and an OpenAPI 3.0 specification are assembled on install - not left as decisions for your first sprint.

- [Read the docs](https://docs.dotkernel.org/api-documentation/)
- [View on GitHub](https://github.com/dotkernel/api)
- [Live demo](https://api.dotkernel.net/)

| | |
| --- | --- |
| Runtime | Mezzio + Laminas |
| Persistence | Doctrine ORM |
| License | MIT |

## Request lifecycle

CORS (`mezzio-cors`) -> Router (FastRoute) -> Authentication (OAuth 2.0) -> RBAC guard (per route name) -> Your handler (PSR-15) -> Response (HAL / problem+json).
[All 19 stages](https://www.dotkernel.com/architecture/)

## A REST API you own from the first commit

Dotkernel API is the root of the Dotkernel Headless Platform: a REST API based on the Mezzio skeleton, using a PSR-compliant middleware stack as defined by the PHP Framework Interop Group.
Rather than relying on elements built into a framework, business logic is built explicitly - from handlers to dependencies - so you keep full control over your business logic and data architecture.

It scales down to a single microservice and up to an enterprise-grade API, and it has been developed and released continuously since 2018.

Extending the power of Mezzio by Laminas.

- Doctrine ORM, not Active Record
- Explicit wiring, no runtime magic
- Auth, docs and errors on install
- MIT licensed, actively maintained

## The parts every API needs, already connected

Each of these is configured and working in a fresh install.
Nothing here is a placeholder you have to research and assemble before you can serve your first authenticated endpoint.

### OAuth 2.0 - Security . Authentication

Bearer tokens, issued and validated by the API itself.

Authentication runs on `mezzio-authentication-oauth2`, which wraps `league/oauth2-server` to provide OAuth 2.0 for PSR-7 / PSR-15 applications.
Endpoints marked as authenticated require a valid Bearer token and answer `401 Unauthorized` without one.

### RBAC per route - Security . Authorization

Three levels of protection: none, authenticated, authorized.

Roles and their permissions are declared in configuration and allocated per route name, with role inheritance supported.
An authenticated request that lacks the required permission gets a `403 Forbidden` - decided by config, not scattered through your handlers.

### OpenAPI 3.0 - Contract . Documentation

A machine-readable contract, generated from the code that serves it.

Every module documents its endpoints in an `OpenAPI.php` file, which `zircote/swagger-php` turns into a specification rendered through Swagger UI or Redoc.
A Postman collection and environment ship alongside it, so every endpoint is ready to call by hand.

### Doctrine ORM - Data . Persistence

Your domain in entities, not in the query layer.

Doctrine ORM handles persistence, so you can focus on object-oriented business logic and treat storage as a secondary concern.
Migrations, fixtures via `dot-data-fixtures`, and multiple database connections are part of the setup.

### HAL & problem details - Contract . Responses

One predictable shape for resources, one for failures.

Payloads are built with `mezzio-hal`, describing each resource together with its relational links and embedded child resources.
Errors return problem details responses via `mezzio-problem-details`, giving clients standardized error codes system-wide.

### Content negotiation - Contract . Negotiation

Client and server agree on format and language up front.

Content negotiation is implemented out of the box through the `Content-Type` and `Accept` headers, so diverse systems can consume the same API without custom glue on either side.
CORS preflight requests are recognized and configured by `mezzio-cors`.

### Error reporting endpoint - Operations . Feedback

A channel for the bugs that never reach your logs.

Frontend developers can report incorrect behaviour by posting to `/error-report` with a token header.
The API validates the request against configured tokens, domains and IPs before logging it - useful precisely when nothing fatal was thrown.

### Commands & file locker - Operations . CLI

Scheduled work that refuses to trample itself.

Console commands are registered through `dot-cli` on top of Symfony Console, and `php ./bin/cli.php route:list` prints every endpoint the application exposes.
The file locker, enabled by default, writes a lock file per command so a second instance cannot start while the previous run is still going - the safeguard cron jobs usually lack.

### Transactional email - Operations . Mail

Account mail that works before you configure anything.

`dot-mail` wraps Symfony Mailer with templated messages, so account activation and password reset send out of a fresh install.
It joins the request flow only where it is needed - the rest of the pipeline is unchanged whether a route sends mail or not.

### dot-maker - Productivity . Scaffolding

New modules that already match the house style.

`dot-maker` generates project files and directories following the Dotkernel structure, and knows which files each application type needs.
It replaces hand-copied boilerplate with consistent, standardized scaffolding.

- [Docs](https://docs.dotkernel.org/dot-maker/v1/overview/)

## What version 7 changed

The latest release moves identifiers to native database UUIDs and broadens platform support.

### Native UUID v7

Identifier columns use the database's `uuid` type instead of `binary`, with values generated by `ramsey/uuid`.
You keep full control of the UUID version without depending on database extensions.

### PostgreSQL support

PostgreSQL joins the supported databases.
Because native UUID is required, you need PostgreSQL or MariaDB 10.7 or later; MySQL is no longer supported, as it has no UUID data type.

### PHP 8.5

The API targets PHP 8.5, with Dotkernel Admin on 8.4.
Dependencies are kept current, and the ecosystem's own packages track the versions Laminas and Doctrine support.

### Table prefixes

A configurable string can be prepended to every table name - the practical requirement when an API shares a database with an existing application.

### Clearer database configuration

Multi-connection setups spell out which connection is the default and how to switch to another, based on scenarios from real projects.

### Shared Core module

Common logic lives in a Core module kept as its own Git repository, which can be added as a submodule to any Dotkernel application so entities and queries stay consistent.

## Evolution, not a wall of versions

Dotkernel API favours an evolution pattern with a sunsetting mechanism over maintaining parallel API versions.
The same codebase evolves gradually and clients are given notice, instead of every change forking into another branch you have to keep alive.

The mechanism is `DeprecationMiddleware`: mark a handler with the `#[ResourceDeprecation]` attribute and the response carries `sunset` and `link` headers, so a client learns an endpoint is retiring from the endpoint itself rather than from a changelog it never read.

Full versioning stays reserved for major, format-level changes - the cases where it genuinely earns its maintenance cost.
The two approaches are not mutually exclusive.

- [API evolution guide](https://docs.dotkernel.org/api-documentation/v6/tutorials/api-evolution/)
- [Where it sits in the pipeline](https://www.dotkernel.com/architecture/)

### Coming from Laminas API Tools?

API Tools (formerly Apigility) is archived.
Dotkernel API is an actively maintained alternative with a middleware architecture, a permissive MIT license and a Doctrine data layer.

The comparison below is drawn from the full side-by-side write-up on our blog.

## Dotkernel API vs Laminas API Tools

| Feature | API Tools (formerly Apigility) | Dotkernel API |
| --- | --- | --- |
| First release | 2012 | 2018 |
| Architecture | MVC, event driven | Middleware |
| OSS lifecycle | Archived | Active |
| PHP version | ≤ 8.2 | See the PHP version badge for `dotkernel/api` |
| Style | REST, RPC | REST |
| Change management | Versioning | Deprecations (API evolution) |
| Documentation | Swagger (automated) | OpenAPI 3.0 (Swagger) and Postman (manual) |
| License | BSD-3 | MIT |
| Default DB layer | laminas-db | doctrine-orm 3.x |
| Authorization | ACL | RBAC guard |
| Authentication | HTTP Basic / Digest, OAuth 2.0 | OAuth 2.0 |
| Endpoint generator | Yes | dot-maker |
| PSR standards | PSR-7 | PSR-7, PSR-15 |

Comparison drawn against Dotkernel API v7.
Read the reasoning behind each row in [Dotkernel API versus Laminas API Tools](https://www.dotkernel.com/dotkernel-api/dotkernel-api-versus-laminas-api-tools/).

## Runs alone, or as one of three

Dotkernel API is a complete application on its own.
When you add Admin or Queue, all three declare the same `Core` namespaces, so a `User` means the same thing to the endpoint that creates it, the admin screen that moderates it, and the worker that emails it.
The rest of the ecosystem is below.

### Shared domain layer

`Core\App` `Core\Admin` `Core\User` `Core\Security` `Core\Setting`

One set of entities and repositories, committed in each repository so a single-component start just works.
Having many entities in Core does not oblige you to implement handlers for all of them - each application handles only what it needs.

### Admin - Pair with . Back office

Manage the same data your API serves.

Table-based record management with RBAC guards, CSRF-protected forms and 2FA, over the same Core module.
Start with either one and add the other later.

- [Read more](https://www.dotkernel.com/admin/)
- [GitHub](https://github.com/dotkernel/admin)
- [Demo](https://admin7.dotkernel.net/)

### Queue - Pair with . Async work

Move slow work off the request cycle.

Background workers on Symfony Messenger - a TCP listener, Valkey streams, retries and a dead letter queue for what still fails.

- [Read more](https://www.dotkernel.com/queue/)
- [GitHub](https://github.com/dotkernel/queue)

### Frontend - Instead . Server-rendered

When your users want pages, not payloads.

A web starter skeleton - user accounts, a contact form, sessions and RBAC-guarded controller actions, rendered on the server.

- [Read more](https://www.dotkernel.com/frontend/)
- [Demo](https://v5.dotkernel.net/)

### Light - Smaller . Minimal

A presentation site with no API behind it.

The smallest complete Mezzio application - routing, pipeline and Twig, six direct dependencies and no database layer.

- [Read more](https://www.dotkernel.com/light/)
- [Demo](https://light.dotkernel.net/)

### Dotboost - Tooling . AI context

Teach your AI tools this architecture.

Drop-in Claude Code configuration - ten commands, seventeen skills and permission guardrails that keep your secrets out of the context window.

- [Read more](https://www.dotkernel.com/dotboost/)
- [GitHub](https://github.com/dotkernel/dotboost)

Every layer is also available on its own - see the [packages lifecycle](https://www.dotkernel.com/dotkernel-packages-oss-lifecycle/) for the support status of each `dot-*` package.

## Built on interfaces, checked on every change

### [PSR-15 & PSR-7](https://www.php-fig.org/psr/psr-15/)

Handlers implement `RequestHandlerInterface` and return `ResponseInterface`.
The whole request path is middleware you can read top to bottom.

### [PSR-11 & PSR-4](https://www.php-fig.org/psr/psr-11/)

The application is container-based, with dependencies declared in each module's `ConfigProvider`, and classes located by autoloader.

### [PSR-3 logging](https://www.php-fig.org/psr/psr-3/)

Errors are logged through `LoggerInterface` via `dot-errorhandler`, centralizing how failures are recorded.

### [PHPStan at level 8](https://phpstan.org/)

Static analysis runs at a strict rule level, in line with the choice made by projects like Doctrine and Composer.

### Functional & unit tests

The skeleton ships with a test suite you extend rather than start, so new endpoints have somewhere to be tested from day one.

### An active community

Updates arrive with bugfixes and improvements from the PHP community, and breaking changes come with companion articles and upgrade steps.

[Join the discussion ->](https://github.com/orgs/dotkernel/discussions)

## Install it and call an endpoint

Create the project with Composer, point it at PostgreSQL or MariaDB 10.7+, run the migrations, and you have an authenticated REST API with a browsable OpenAPI specification.

- [Installation guide](https://docs.dotkernel.org/api-documentation/)
- [Try the demo](https://api.dotkernel.net/)

### composer create-project dotkernel/api

The installer walks through database credentials and the initial configuration.
Follow the installation guide for the exact command and post-install steps for your target version, then use `dot-maker` to scaffold your first module.

Questions along the way go to [Dotkernel Discussions](https://github.com/orgs/dotkernel/discussions) - the team answers issues on both Dotkernel and Laminas components.

## Open source, in production

An API foundation you can read, audit and keep.

Dotkernel API is developed and led by the dev team at Apidemia - built first as an internal tool for handling complex architectures, released under MIT as our way of giving back to the community.

[Talk to us ->](https://www.dotkernel.com/contact/)
