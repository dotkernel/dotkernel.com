---
title: "Dotkernel Frontend | Server-rendered PHP web starter"
description: "Dotkernel Frontend is a web starter skeleton on Mezzio and Laminas - user accounts, a contact form, RBAC guards, CSRF-protected forms, flash messages and GDPR account anonymization, server-rendered in Twig."
canonical_url: "https://www.dotkernel.com/frontend/"
language: "en"
---

# Dotkernel Frontend

Web starter · Server-rendered

A web starter skeleton on the Mezzio microframework and Laminas components, for the applications people log into.
User accounts, a working contact form and a content page ship as proof of concept - real, running features whose only job is to show you where your own code goes.

- [Read the docs](https://docs.dotkernel.org/frontend/)
- [View on GitHub](https://github.com/dotkernel/frontend)
- [Live demo](https://v5.dotkernel.net/)

| | |
| --- | --- |
| Runtime | Mezzio + Laminas |
| Pattern | Action controllers |
| Templating | Twig |

## Request lifecycle

Session (`dot-session`) → Router (FastRoute) → Authentication (User identity) → RBAC guard (route + action) → Controller action (`dot-controller`) → Response (Twig + flash).

## The web application half of the stack

Frontend is the skeleton for a server-rendered application with users in it.
Where Light gives you pages, Frontend gives you the machinery around them: sessions, authentication, registration, validated forms, flash messages between redirects, and role-based access to individual controller actions.

The shipped features - contact us, a generic content page, user accounts - are deliberately presented as building blocks rather than a finished product.
They exist to showcase the file architecture and to be copied.

Extending the power of Mezzio by Laminas.

- User accounts, from register to unregister
- Action controllers, not request handlers
- CSRF and reCAPTCHA on public forms
- GDPR anonymization out of the box

## Everything a logged-in site needs first

These are the parts you would otherwise spend your first two weeks assembling, already working together in a fresh install.

### Registration & login — Users · Accounts

The whole account lifecycle, already routed.

Login, registration and account management, including avatar upload, password change and unregistering.
Password reset and account activation emails are part of the flow, which is why the skeleton stores a name and an email address and nothing more.

### Guards per action — Security · Access control

Permissions applied to individual controller actions.

`dot-rbac-guard` and `dot-rbac` read roles and permissions from `authorization.global.php`, then `authorization-guards.global.php` maps rules onto a route and a named list of its actions - or an empty list to cover all of them.
Fine-grained without being scattered.

### CSRF tokens — Security · Forms

A new token per render, validated on submit.

Built from the `laminas-form` CSRF element, a `laminas-session` CSRF validator in the input filter, and the `formElement` view helper in the template.
Tokens expire after a configurable timeout - one hour by default - and are never reusable between forms.

### Contact form with reCAPTCHA — Public · Contact

A public form that does not become a spam relay.

The contact form uses Google reCAPTCHA, with the site and secret keys read from local configuration and the message recipients - `to` and any number of `cc` addresses - configured alongside them.
Whitelist `localhost` while developing, and take it out again for production.

### Flash messages — UX · Feedback

The message survives the redirect.

`dot-flashmessenger` carries session messages across redirects - the small piece that makes post-then-redirect flows feel finished.
`dot-session` extends laminas-session underneath, configured in `session.global.php`.

### Attribute injection — Wiring · DI

Constructor injection declared on the constructor.

`dot-dependency-injection` reads an `#[Inject]` attribute and resolves each listed dependency - a service, the whole `config` array, or a single key by dot notation.
Register the class against `AttributedServiceFactory` and stop writing factories.

### Doctrine ORM — Data · Persistence

Migrations and fixtures, driven from the CLI.

Doctrine through `roave/psr-container-doctrine`, with UUIDs as a field type via `ramsey/uuid-doctrine`.
Migrations live in `data/doctrine/migrations`; `bin/doctrine fixtures:execute` seeds the default roles.

### Headers & CORS — Delivery · HTTP

Response headers declared per route.

`dot-response-header` sets custom headers per route from `response-header.global.php`, while `mezzio-cors` handles origins, headers and cookies from `cors.global.php`.

### Menus, templates, i18n — Content · Presentation

Navigation from configuration, translation when you need it.

`dot-navigation` defines and parses menus inside templates from configuration.
`dot-twigrenderer` adds Dotkernel's Twig extensions, and `laminas-i18n` is present for a complete translation suite.
The `Plugin` module carries dynamic forms and templates.

## Account anonymization, not just deletion

Under the GDPR, a company recording personal data from EU citizens must delete it on request - or anonymize it, which the European Commission accepts as an alternative.
Frontend implements the second option, because deleting a user row is rarely what your foreign keys want.

The skeleton stores only what it needs to run those flows: first name, last name and the email address used as the identity, for password reset and account activation.
Anonymizing replaces exactly those.

- [Anonymization reference](https://docs.dotkernel.org/frontend/v5/reference/account-anonymization/)

### What anonymization changes

- First and last name become `anonymous` plus the current UNIX timestamp - for example `anonymous1725980747`.
- The email becomes the same value plus whatever you set in `userAnonymizeAppend` - `anonymous1725980747@example.com`.
- The avatar image and its database record are deleted.

Point `userAnonymizeAppend` at a domain you control and it doubles as a catch-all address, if your mail provider supports one.
Leave it empty and the local part stands alone.

## Five modules, PSR-4 throughout

Each module keeps its controllers, entities, repositories and services together, alongside a `ConfigProvider` and a `RoutesDelegator` - so a new feature has an obvious shape before you write it.

### `User`

Login, registration and account management - the largest module, and the one worth reading first.

### `App`

Core functionality: authentication, rendering and error reporting.

### `Contact`

The contact us form, from validation through to the outgoing mail.

### `Page`

Displaying a page - the minimal case, for static copy.

### `Plugin`

Plugin functionality for dynamic forms and templates.

### Module contents

`Controller`, `Entity`, `Repository` and `Service` folders, plus `InputFilter`, `EventListener`, `Helper`, `Command` or `Factory` as needed.

## From clone to welcome page

The documentation walks through every command with its expected output.
This is the sequence.

### 1 · Clone into an empty folder

Git refuses a non-empty directory, and you need write permissions on it.

```shell
git clone https://github.com/dotkernel/frontend.git .
```

### 2 · Install dependencies

From the CLI, so the prompts stay interactive.
Decline the config provider injection - Frontend ships its own.

```shell
composer install
```

### 3 · Enable development mode

Sets debug on, configuration caching off, and clears any existing cache.

```shell
composer development-enable
```

### 4 · Prepare the config files

Copy the `.dist` files into place - `local.php`, `development.local.php`, `mail.local.php`, `debugbar.local.php` - then fill in the database, SMTP and reCAPTCHA details.

### 5 · Migrate and seed

Migrations build the schema and are logged so none runs twice; the fixtures populate the default user roles.

```shell
php vendor/bin/doctrine-migrations migrate
php bin/doctrine fixtures:execute
```

### 6 · Fix permissions and open it

Three writable paths cover almost every first-run error.

```shell
chmod -R 777 data log public/uploads
```

Two local-only notes worth keeping: `session.cookie_secure` has to be `false` in your own `local.php` - never in `local.php.dist`, where it stays `true` for production - and a stale `data/cache/config-cache.php` is loaded regardless of `ConfigAggregator::ENABLE_CACHE`, so clear it with `bin/clear-config-cache.php` when services go missing.
Duplicating `local.test.php.dist` gives your tests an in-memory database.

## What the server needs

| Component | Requirement |
| --- | --- |
| Operating system | A \*nix based system is strongly recommended for production. |
| PHP | 8.2 or newer, mod_php or FCGI (FPM). `memory_limit` at least 128M; `upload_max_filesize` and `post_max_size` at least 100M depending on your data. |
| Web server | Apache 2.2+ with `mod_rewrite` and `.htaccess` support (`AllowOverride All`); a default `.htaccess` ships in `public/`. On Nginx, translate it into server configuration. |
| Database | Tested with MariaDB 10.11 LTS and 11.4 LTS, and with MySQL 8.4 LTS. For MySQL 8.4, `my.cnf` needs `mysql_native_password=ON`. |
| Required extensions | `mbstring`, the CLI SAPI for cron jobs, and Composer on `$PATH`. |
| Recommended extensions | `opcache`; `pdo_mysql` or `mysqli`; `dom` and `simplexml` for markup; `gd` and `exif` for images; `zlib`, `zip`, `bz2` for compression; `curl` when calling APIs; `sqlite3` for the test suite. |

Note that Frontend still supports MySQL - unlike API and Admin v7, which require native UUID support and therefore PostgreSQL or MariaDB 10.7+.

## Where Frontend sits

Frontend stands on its own, outside the Headless Platform.
It is the right starting point when your users log in and your pages are rendered on the server - and the wrong one in both directions from there.

### Light — Smaller · Minimal

No users, no database, no forms to protect.

The smallest complete Mezzio application - routing, pipeline and Twig, six direct dependencies.
Right for a presentation site.

- [Read more](https://www.dotkernel.com/light/)
- [Demo](https://light.dotkernel.net/)

### API — Different · HTTP surface

Your frontend is someone else's framework.

A REST API on a PSR-15 middleware pipeline, with OAuth 2.0, RBAC, HAL payloads and an OpenAPI 3.0 specification wired up on install.

- [Read more](https://www.dotkernel.com/api/)
- [Demo](https://api.dotkernel.net/)

### Admin — Bigger · Platform

You need a back office over a shared domain.

Table-based record management with RBAC guards, CSRF-protected forms and 2FA, over a Core module shared with API and Queue.

- [Read more](https://www.dotkernel.com/admin/)
- [Demo](https://admin7.dotkernel.net/)

### Queue — Alongside · Async work

Move the registration email off the request.

Background workers on Symfony Messenger - a TCP listener, Valkey streams, retries and a dead letter queue for what still fails.

- [Read more](https://www.dotkernel.com/queue/)
- [GitHub](https://github.com/dotkernel/queue)

### Dotboost — Tooling · AI context

Teach your AI tools this architecture.

Drop-in Claude Code configuration - ten commands, seventeen skills and permission guardrails that keep your secrets out of the context window.

- [Read more](https://www.dotkernel.com/dotboost/)
- [GitHub](https://github.com/dotkernel/dotboost)

## Open source, in production

Accounts, forms and compliance - already handled.

Dotkernel Frontend is developed and led by the dev team at Apidemia and released as open source for the community.
Try the demo to see the account flows before you install anything.

[Talk to us →](https://www.dotkernel.com/contact/)
