---
title: "Dotkernel Admin | Open-source PHP admin application"
description: "Dotkernel Admin is an open-source admin application (skeleton) built on Mezzio, Laminas and Doctrine. Table-based record management, RBAC guards, CSRF-protected forms and 2FA, over the same Core as Dotkernel API."
canonical_url: "https://www.dotkernel.com/admin/"
language: "en"
---

# Dotkernel Admin

Admin application · Server-rendered

An open-source application skeleton for standing up the administration site behind your platform.
A fast, reliable way to manage the records in your database with a simple table-based approach, and to build the reports and graphs that let you monitor what is happening - with the graphical components for an intuitive experience already in place.

- [Read the docs](https://docs.dotkernel.org/admin-documentation/)
- [View on GitHub](https://github.com/dotkernel/admin)
- [Live demo](https://admin7.dotkernel.net/)

| | |
| --- | --- |
| Runtime | Mezzio + Laminas |
| Templating | Twig |
| License | MIT |

## Request lifecycle

Session (`dot-session`) → Router (`dot-router`) → Authentication (`laminas-authentication`) → RBAC guard (`dot-rbac-guard`) → Your handler (PSR-15) → Response (Twig template).

## The back office, not a framework to learn

Dotkernel Admin is the complementary application of the Dotkernel Headless Platform: an independent app built on the same Mezzio and Laminas foundation as Dotkernel API, sharing the same tech stack so the two form one consistent system rather than two codebases that merely talk to each other.

Authentication, role-based access control, CSRF-protected forms, validation, navigation and a dashboard are already assembled.
What you add is your own screens for your own entities.

Extending the power of Mezzio by Laminas.

- Table-based record management
- Every shipped form validated and CSRF-guarded
- Shares the Core module with API
- Twig templates, npm asset pipeline

## The unglamorous parts, already done

Everything below works in a fresh install.
The default modules are configured; your custom functionality needs the same configuration entries, and the docs say exactly which ones.

### RBAC guards - Security · Access control

Permissions declared in config, enforced per route handler.

`dot-rbac` and `dot-rbac-guard` work together: roles and their permissions live in `authorization.global.php`, while `authorization-guards.global.php` maps each route handler to the permissions it requires.
Add a route, add its rule - access control never drifts into your handlers.

### TOTP two-factor - Security · 2FA

A password plus a code that expires in 30 seconds.

`dot-totp` adds time-based one-time passwords following the industry standard: administrators authenticate with their password and a 6-digit code from an authenticator app.
Installation is a documented set of forms, handlers, middleware and three new columns.

- [Install guide](https://docs.dotkernel.org/admin-documentation/v7/tutorials/install-dot-totp/)

### CSRF protection - Security · Forms

A fresh token per render, on every form that ships.

Built from the `laminas-form` CSRF element, the `laminas-session` CSRF validator and the `formElement` view helper.
Tokens are not reusable between forms and expire after a configurable timeout, defaulting to one hour.

### Forms & input filters - Input · Validation

Validation rules that live beside the form, not in the handler.

`laminas-form` bridges your domain models and the view layer, while `laminas-inputfilter` normalizes and validates the submitted set.
Every form in the skeleton has its inputs filtered, so your own forms have a pattern to copy.

### Doctrine ORM - Data · Persistence

Migrations and fixtures, both driven from the CLI.

Doctrine ORM 3 and DBAL 4 over MariaDB or PostgreSQL, with UUID identifiers via `ramsey/uuid-doctrine`.
Generate a migration with `doctrine-migrations diff`, apply it with `migrate`, then seed the tables with `bin/doctrine fixtures:execute`.

### Attribute injection - Wiring · DI

Constructor injection declared where the constructor is.

`dot-dependency-injection` reads an `#[Inject]` attribute on the constructor and resolves each listed dependency - including values from a configuration key, by dot notation.
Register the class against `AttributedServiceFactory` and you are done writing factories by hand.

### Menus & UI components - Interface · Navigation

Configuration-driven menus, ready-made interface pieces.

`dot-navigation` defines and parses the top menu from `navigation.global.php`.
The `Page` module carries reusable dropdowns, modal popups, error displays and tooltips; the `Dashboard` module holds the landing page layout and its widgets.

### Commands & lock files - Operations · CLI

Cron-safe console commands out of the box.

`dot-cli` builds the console application on top of laminas-cli, writing lock files into `data/lock` so a scheduled command cannot overlap with itself.
GeoLite2 databases sync through `bin/cli.php geoip:synchronize`, quiet mode included for cron jobs.

### Mail and error handling - Operations · Mail & logs

Transactional mail configured in one file, errors in another.

`dot-mail` handles sendmail or SMTP delivery, sender identity and CC lists from `mail.global.php`.
`dot-errorhandler` writes daily log files to `log/`, with the format set in `error-handling.global.php`.

## Modules you can name before you read them

Admin follows PSR-4, with one folder per module.
Each module keeps its handlers, input filters and services together, plus a `ConfigProvider` and a `RoutesDelegator` - so the second module you write looks like the first.

### Shared domain layer

`Core\Admin` `Core\App` `Core\Security` `Core\Setting` `Core\User`

The Core module is a common codebase shared with the other applications in your project.
Each submodule holds its entities, repositories and its own ConfigProvider, so a user means the same thing to the admin screen and to the API endpoint.

### `Admin`

Managing the users that hold the `admin` role - the accounts stored in the `admin` database table.

### `User`

Managing the platform's own users, stored separately in the `user` table.
Two audiences, two models, no overloaded role column.

### `App`

Core application functionality: authentication, rendering and error reporting.

### `Dashboard`

The default landing page - its layout, widgets and rendering logic.

### `Page`

Reusable interface elements: dropdowns, modals, error displays, tooltips.

### `Setting`

Saving and reading display settings for the administration interface.

## From clone to login page

The full walkthrough, with expected output for every command, is in the documentation.
This is the shape of it.

### 1 · Clone the project

Into an empty directory of your choosing.

```shell
git clone https://github.com/dotkernel/admin.git .
```

### 2 · Install dependencies

Run it from the CLI so the setup prompts stay interactive.
Decline the ConfigProvider injection - Dotkernel ships its own.

```shell
composer install
```

### 3 · Enable development mode

For local work only.
`composer development-status` tells you where you stand, and it must stay off in production.

```shell
composer development-enable
```

### 4 · Configure the database

Fill in the credentials in `config/autoload/local.php`.
Both a MariaDB and a PostgreSQL connection are pre-declared; one is active at a time.

### 5 · Migrate and seed

Migrations build the schema; fixtures populate the default roles, OAuth clients and accounts.

```shell
php ./vendor/bin/doctrine-migrations migrate
php ./bin/doctrine fixtures:execute
```

### 6 · Build the assets

`npm run watch` recompiles while you work; `npm run prod` minifies for release.

```shell
npm install && npm run prod
```

Open the virtual host in your browser and the Dotkernel Admin login page is waiting.
If the fixtures ran, sign in with user `admin` and password `dotadmin` - the same credentials as the public demo, and the first thing to change before going live.

## What the server needs

| Component | Requirement |
| --- | --- |
| Operating system | A \*nix based system is strongly recommended for production. |
| PHP | 8.2 or newer, mod_php or FCGI (FPM). `memory_limit` at least 128M; `upload_max_filesize` and `post_max_size` at least 100M depending on your data. |
| Web server | Apache 2.2+ with `mod_rewrite` and `.htaccess` support (`AllowOverride All`) - a default `.htaccess` ships in `public/`. On Nginx, translate it into server configuration. |
| Database | MariaDB 10.7, 10.11 LTS, 11.4 LTS and 11.8 LTS, or PostgreSQL 13 and above. **MySQL is not supported**, as it has no UUID support. |
| Required extensions | `mbstring`, the CLI SAPI for cron jobs, and Composer available on `$PATH`. |
| Recommended extensions | `opcache`; `pdo_mysql`, `pdo_pgsql` or `mysqli` to match your database; `dom` and `simplexml` for markup; `gd` and `exif` for images; `zlib`, `zip`, `bz2` for compression; `curl` when calling APIs; `sqlite3` for the test suite. |

On Windows, WSL2 is the recommended development environment - Dotkernel provides an AlmaLinux distro implementation and an install script for the whole local stack.

## The checklist nobody writes down

Admin gives you the tools to build a safe application, and the documentation is explicit that some of them are yours to apply.
These are the ones worth checking off before the first deploy.

- [Security guide](https://docs.dotkernel.org/admin-documentation/v7/security/basic-security/)

### Before you go to production

- Change or remove the demo admin account - its identity and password are public.
- Review `cookie_httponly`, `cookie_samesite` and `cookie_secure` in `session.global.php`.
- Confirm development mode is off with `composer development-status`.
- Set your allowed origins in `cors.global.php`.
- Keep secrets in `*.local.php` - the `*.global.php` and `*.php.dist` files are committed.
- Add CSRF fields and authorization rules to every form and route you add.
- Watch dependencies on both sides: Composer advisories and `npm audit`.

## Upgrades you can read before you run them

Admin does not ship an automatic upgrade path, and that is deliberate: you implement the modifications listed for each release, in a codebase you already understand, instead of handing a migration script control over your customizations.

A `CHANGELOG.md` in the project root lists implemented features in reverse chronological order, so it doubles as your version marker.
From version 6.2 onward, the procedure is documented version to version, and releases are also published as an RSS feed.

- [Releases](https://github.com/dotkernel/admin/releases)
- [Upgrade guide](https://docs.dotkernel.org/admin-documentation/v7/upgrading/upgrading/)

### Coming from Admin 6.x?

Version 7 bumped dependencies, synchronised the Core module and refreshed the codebase to stay compatible with Dotkernel API v7 - the release that moved identifiers to native database UUIDs and dropped MySQL.

Work through the pull requests listed for 7.0, then copy the release notes into your own changelog so your project keeps tracking upstream.

## Better with an API in front of it

Admin installs and runs on its own.
Paired with Dotkernel API over the same `Core` namespaces, Admin manages the data while the API exposes it to third-party frontends and backends - and neither one disagrees about what an entity is.
The rest of the ecosystem is below.

### API - Pair with · HTTP surface

Expose the data you manage here to any client.

A REST API on a PSR-15 middleware pipeline, with OAuth 2.0, RBAC, HAL payloads and an OpenAPI 3.0 specification wired up on install.

- [Read more](https://www.dotkernel.com/api/)
- [GitHub](https://github.com/dotkernel/api)
- [Demo](https://api.dotkernel.net/)

### Queue - Pair with · Async work

Queue a bulk operation from an admin screen.

Background workers on Symfony Messenger - a TCP listener, Valkey streams, retries and a dead letter queue for what still fails.

- [Read more](https://www.dotkernel.com/queue/)
- [GitHub](https://github.com/dotkernel/queue)

### Frontend - Instead · Public-facing

The site your users log into, not your staff.

A web starter skeleton - user accounts, a contact form, sessions and RBAC-guarded controller actions, rendered on the server.

- [Read more](https://www.dotkernel.com/frontend/)
- [Demo](https://v5.dotkernel.net/)

### Light - Smaller · Minimal

No records to manage, just pages.

The smallest complete Mezzio application - routing, pipeline and Twig, six direct dependencies and no database layer.

- [Read more](https://www.dotkernel.com/light/)
- [Demo](https://light.dotkernel.net/)

### Dotboost - Tooling · AI context

Teach your AI tools this architecture.

Drop-in Claude Code configuration - ten commands, seventeen skills and permission guardrails that keep your secrets out of the context window.

- [Read more](https://www.dotkernel.com/dotboost/)
- [GitHub](https://github.com/dotkernel/dotboost)

New modules start with [dot-maker](https://docs.dotkernel.org/dot-maker/v1/overview/), which knows which files an Admin needs versus an API.
Support status for every layer is on the [packages lifecycle](https://www.dotkernel.com/dotkernel-packages-oss-lifecycle/) page.

## Open source, in production

Try it with the demo, keep it under MIT.

Sign in to the public demo with `admin` / `dotadmin` to see the interface before you install anything.
Dotkernel Admin is developed and led by the dev team at Apidemia, and released as open source for the community.

[Talk to us →](https://www.dotkernel.com/contact/)
