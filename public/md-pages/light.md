---
title: "Dotkernel Light | Minimal PSR-15 site skeleton for PHP"
description: "Dotkernel Light is a minimal PSR-15 application skeleton on Mezzio and Laminas, for presentation sites and small services. Six direct dependencies, Twig templating, Vite assets, no database layer."
canonical_url: "https://www.dotkernel.com/light/"
language: "en"
---

# Dotkernel Light

Minimal skeleton · PSR-15

A PSR-15 compliant application skeleton on the Mezzio microframework and Laminas components, designed as a minimal project for generating a simple website - a presentation site, a landing page, a small service.
Six direct dependencies, two modules, and nothing to delete before you start.

- [Read the docs](https://docs.dotkernel.org/light-documentation/)
- [View on GitHub](https://github.com/dotkernel/light)
- [Live demo](https://light.dotkernel.net/)

| | |
| --- | --- |
| Runtime | Mezzio + Laminas |
| Templating | Twig |
| Direct deps | Six |

## Request lifecycle

`index.php` (bootstrap) → Container (factories · aliases) → Routing (FastRoute) → Pipeline (`pipeline.php`) → Handler (PSR-15) → Twig (layout · blocks) → Emitter (HtmlResponse).

## The smallest complete Mezzio application

Light is what remains when you take a real PSR-15 application and remove everything a presentation site does not need.
There is no ORM, no authentication stack, no admin scaffolding - not disabled, simply not installed.
What you get is routing, a middleware pipeline, Twig, and error logging.

That makes it two things at once: a genuine starting point for a small site, and the clearest way to read how a Mezzio application is put together, because there is almost nothing else in the way.

Extending the power of Mezzio by Laminas.

- Six direct dependencies
- No database layer to configure
- A new page is one config line and one template
- Twig layout, Vite asset pipeline

## Six packages, and you can name all of them

This is the entire direct dependency list.
Every one of them is doing a job you would have had to solve anyway.

### [mezzio/mezzio](https://github.com/mezzio/mezzio)

The PSR-15 middleware microframework the whole application runs on.

### [mezzio-fastroute](https://github.com/mezzio/mezzio-fastroute)

FastRoute integration - matches the URL and method against your registered routes.

### [mezzio-twigrenderer](https://github.com/mezzio/mezzio-twigrenderer)

Twig integration for Mezzio.
Every template is a `.html.twig` file.

### [dot-errorhandler](https://github.com/dotkernel/dot-errorhandler)

Logging error handler for middleware applications - daily log files, configured in one place.

### [laminas-config-aggregator](https://github.com/laminas/laminas-config-aggregator)

Collects and merges configuration from every source into one array.

### [laminas-component-installer](https://github.com/laminas/laminas-component-installer)

The Composer plugin that injects modules and config providers during installation.

Two modules ship in `src/`: `App` for core functionality - rendering and error reporting - and `Page` for displaying a page.
Each module keeps its `Handler`, `Factory` and `Service` folders next to a `ConfigProvider` and a `RoutesDelegator`, following PSR-4.

## A request, end to end

Eleven steps from the browser to the rendered page, with nothing hidden behind a kernel.
This is the sequence you can follow in the source.

| # | Step | What happens |
| --- | --- | --- |
| 1 | HTTP request | `public/index.php` bootstraps the application, loads configuration, and creates the Mezzio application instance. |
| 2 | Service container | Factories, aliases and delegators are registered; every service is configured and ready to use. |
| 3 | Route registration | All available routes and their allowed methods are read and registered dynamically, managed by FastRoute. |
| 4 | Middleware pipeline | `config/pipeline.php` defines the order middleware runs in, and so how requests travel and responses come back. |
| 5 | Routing | FastRoute matches the URL and method against the registered routes to find the handler. |
| 6 | Handler invocation | The matched route name is pulled off the request and passed to the renderer - `page::about` becomes the template to render. |
| 7 | Custom logic | Your business logic runs in the handler, calling whatever services it needs. |
| 8 | Template rendering | Twig loads the template, applies the layout, renders the blocks and includes the partials. |
| 9 | Response creation | An `HtmlResponse` is built with status, headers and the rendered HTML body. |
| 10 | Response pipeline | The response flows back out through the middleware stack, which can still change headers, cookies or compression. |
| 11 | Response emitter | The final response is sent to the browser. |

Handlers, not controllers - that is what keeps the application PSR-15 compliant.
Each module's `RoutesDelegator` reads the route config from the container and registers the routes it finds, so adding a page never means touching the framework.

## Adding a page is two files

Routes live in configuration as `slug => template` pairs.
Append a line, create the matching Twig template in `src/Page/templates/page/`, and add a link to the menu in the layout.
The handler works out which template to render from the matched route name, so there is no handler to write.

Want your templates in more than one folder?
Add another entry under `paths` in the Page module's `ConfigProvider` - the key does not have to match the folder name.

- [Creating pages](https://docs.dotkernel.org/light-documentation/v1/how-tos/create-pages/)

### One route, one template

In `config/autoload/local.php`, under the `routes` → `page` key:

```php
'example-page' => 'example-template',
```

Then create the template it names:

```text
src/Page/templates/page/example-template.html.twig
```

The key is the page slug, the value is the template.
Your new page answers at `/page/example-page`, under the route name `page::example-template`.

## Assets, menus and social cards

The parts you actually edit on a presentation site, and where each of them lives.

### Asset pipeline — Build · Vite

One command while you work, one before you ship.

Vite concatenates and compresses CSS and JavaScript, preprocesses SCSS, and copies fonts and images - avoiding the network bottleneck of many separate files.
`npm run watch` recompiles on change; `npm run build` compiles once.
Node.js v20 is the minimum supported version.

### assets → public — Build · Source of truth

Edit the source, never the output.

Images, fonts, JavaScript and SCSS live in `src/App/assets/`.
The build deletes and rebuilds `css`, `js`, `fonts` and `images` under `public/` - anything you edit there by hand is lost on the next run.
Everything else in `public/` is left alone.

### Cache busting — Build · Caching

The oldest bug in web deployment, solved in one character.

Browsers cache your built CSS and JS, so a deploy can leave visitors on the old file.
Add a version parameter to the asset URL in the layout - `app.css?v=3` - and increment it whenever you commit a change to that file.

### Menu and footer — Content · Navigation

Plain Twig in one layout file.

The top menu is the list under `id="navbarHeader"` in `src/App/templates/layout/default.html.twig`; each `li` is one item, and items can be grouped into dropdowns or styled as buttons.
The footer is the `app-footer` element in the same file.

### Twitter & OpenGraph — Content · Sharing

Cards for when your pages get posted elsewhere.

Add the card meta tags to the layout head, generating URLs with the same helper the canonical block uses - `{{ url(routeName ?? null) }}` - which also keeps mistyped URLs from breaking the page.

### Errors and logs — Operations · Logs

A log file per day, from the first request.

`dot-errorhandler` writes daily files into `log/`, in the format set under the `stream` key of `error-handling.global.php`.
Development mode adds the error handlers you want locally and nowhere else.

## Running in minutes, honestly

No database to create, no fixtures to seed.
Clone, install, set a URL, open it.

### 1 · Clone into an empty folder

Git refuses a directory that is not empty, and you need write permissions on it.

```shell
git clone https://github.com/dotkernel/light.git .
```

### 2 · Install dependencies

Run it from the CLI so the prompts stay interactive.
Decline the config provider injection - Light already includes its own.

```shell
composer install
```

### 3 · Enable development mode

Local work only.
`composer development-status` reports where you stand.

```shell
composer development-enable
```

### 4 · Set the base URL

Point `$baseUrl` in `config/autoload/local.php` at your virtual host.

### 5 · Fix the writable folders

The two directories the application writes to.
Most first-run errors are this and nothing else.

```shell
chmod -R 777 ./data ./log
```

### 6 · Open it in a browser

The Dotkernel Light welcome page is waiting.
Errors about missing services usually mean a stale config cache.

```shell
php ./bin/clear-config-cache.php
```

A cached `data/cache/config-cache.php` is loaded regardless of the `ConfigAggregator::ENABLE_CACHE` setting - which is exactly why clearing it fixes so much.
On Windows, WSL2 with AlmaLinux is the recommended development environment.

## What the server needs

| Component | Requirement |
| --- | --- |
| Operating system | A \*nix based system is strongly recommended for production. |
| PHP | 8.2, 8.3 or 8.4, with mod_php or FCGI (FPM). `memory_limit` at least 128M. |
| Web server | Apache 2.2+ with `mod_rewrite` and `.htaccess` support (`AllowOverride All`); a default `.htaccess` ships in `public/`. On Nginx, translate it into server configuration. |
| Database | None. Light has no persistence layer - which is the point. |
| Required extensions | `mbstring`, plus Composer available on `$PATH`. |
| Recommended extensions | `opcache`; `dom` and `simplexml` for markup; `gd` and `exif` for images; `zlib`, `zip`, `bz2` for compression; `curl` if you call APIs. |

## When Light is the wrong answer

Light sits outside the Headless Platform on purpose.
It has no sessions, no forms, no authentication and no database - so the moment you need those, start from a skeleton that already has them rather than growing them here.

### Frontend — Instead · Full-stack web

You need logins, forms and sessions.

A server-rendered web starter on Mezzio and Laminas - Twig views, forms, sessions and RBAC-guarded routes, still standing on its own outside the platform.

- [Read more](https://www.dotkernel.com/frontend/)
- [GitHub](https://github.com/dotkernel/frontend)
- [Demo](https://v5.dotkernel.net/)

### API — Instead · HTTP surface

You are serving clients, not pages.

A REST API on a PSR-15 middleware pipeline, with OAuth 2.0, RBAC, HAL payloads and an OpenAPI 3.0 specification wired up on install.

- [Read more](https://www.dotkernel.com/api/)
- [GitHub](https://github.com/dotkernel/api)

### Admin — Instead · Back office

You need to manage records, not publish copy.

Table-based record management with RBAC guards, CSRF-protected forms and 2FA, over a Core module shared with the rest of the platform.

- [Read more](https://www.dotkernel.com/admin/)
- [GitHub](https://github.com/dotkernel/admin)

### Queue — Instead · Async work

You have work that outlives a request.

Background workers on Symfony Messenger - a TCP listener, Valkey streams, retries and a dead letter queue for what still fails.

- [Read more](https://www.dotkernel.com/queue/)
- [GitHub](https://github.com/dotkernel/queue)

### dotboost — Tooling · AI context

Teach your AI tools this architecture.

Drop-in Claude Code configuration - ten commands, seventeen skills and permission guardrails that keep your secrets out of the context window.

- [Read more](https://www.dotkernel.com/dotboost/)
- [GitHub](https://github.com/dotkernel/dotboost)

## Open source, in production

Small enough to read, real enough to ship.

Dotkernel Light is developed and led by the dev team at Apidemia, and released as open source for the community.
If you want to see the whole application before you commit to it, that is the entire promise of this skeleton.

[Talk to us →](https://www.dotkernel.com/contact/)
