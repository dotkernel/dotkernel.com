---
title: "Architecture | How a request moves through a Dotkernel application"
description: "The PSR-15 middleware pipeline behind Dotkernel API, stage by stage: error boundary, routing, content negotiation, deprecation headers, OAuth 2.0, RBAC and dispatch - plus the library flow underneath it."
canonical_url: "https://www.dotkernel.com/architecture/"
language: "en"
---

# How a request moves through a Dotkernel application

Architecture · PSR-15 pipeline

Every Dotkernel application is a stack of PSR-15 middleware with no framework runtime hidden underneath it.
A request enters at the error boundary, passes through routing, negotiation, authentication and authorization, and reaches your handler - and you can read every layer it crossed on the way.

- [Read the docs](https://docs.dotkernel.org/api-documentation/)
- [See pipeline.php](https://github.com/dotkernel/api/blob/7.0/config/pipeline.php)
- [Dotkernel API](https://www.dotkernel.com/api/)

| | |
| --- | --- |
| Contract | PSR-7 + PSR-15 |
| Container | PSR-11 |
| Logging | PSR-3 |

## Five phases, 19 stages

Error boundary (1 stage) → Request preparation (4 stages) → Routing (4 stages) → Contract & headers (4 stages) → Identity (2 stages) → Dispatch & fallback (4 stages).

## Framework-less, not framework-free

Dotkernel is an opinionated, framework-less tool aimed at intermediate-to-advanced programmers.
Framework-less does not mean nothing is underneath: Mezzio and Laminas components are there, and they are excellent.
It means there is no framework runtime deciding things on your behalf.
The pipeline is a list you wrote, in a file you can open, executed top to bottom.

That is the whole trade.
You give up the convenience of conventions you never have to look at, and you get an application where every layer between the socket and your handler is named, ordered and yours to reorder.

Extending the power of Mezzio by Laminas.

- Gradual learning curve
- Fine-tuned security
- KISS, YAGNI, DRY
- Long-term support

## Nineteen stages, in the order they run

This is the default pipeline of Dotkernel API, unedited.
Each stage receives the request, may hand it onward, and may alter the response on the way back out - which is why the error boundary is first in and last out.

Legend: Mezzio / Laminas component · Dotkernel.

### Error boundary

#### 01 · ProblemDetailsMiddleware — mezzio/mezzio-problem-details

The outermost layer, deliberately.
Anything thrown deeper in the stack is handed to `dot-errorhandler` for logging and comes back as a problem details response, so a failure reaches the client as a documented error shape rather than a stack trace.

### Request preparation

#### 02 · MalformedRequestBodyMiddleware — dotkernel/api

Rejects a body that is not valid JSON up front, before any later stage tries to read it and fails in a less legible place.

#### 03 · BodyParamsMiddleware — mezzio/mezzio-helpers

Parses the raw request body into the parsed body according to its `Content-Type`.

#### 04 · ServerUrlMiddleware — mezzio/mezzio-helpers

Seeds the server URL helper with the current scheme and host, so anything generating a URL later produces an absolute one.

#### 05 · CorsMiddleware — mezzio/mezzio-cors

Recognises preflight requests and answers them, and attaches the configured CORS headers to everything else.
It sits before routing so a preflight never needs a matched route.

### Routing

#### 06 · RouteMiddleware

*mezzio/mezzio-router* — Route

Matches the request against the route table and registers the `RouteResult` attribute.
Everything below this line can ask which route was matched - and most of it does.

#### 07 · ImplicitHeadMiddleware — mezzio/mezzio-router

Answers `HEAD` for routes that only declare `GET`.

#### 08 · ImplicitOptionsMiddleware — mezzio/mezzio-router

Answers `OPTIONS` with the methods the matched path actually allows.

#### 09 · MethodNotAllowedMiddleware — mezzio/mezzio-router

Returns `405 Method Not Allowed` when the path matched but the method did not.
Order matters here: it has to sit after both implicit handlers, or it would answer the requests they exist to serve.

### Contract & headers

#### 10 · ContentNegotiationMiddleware — dotkernel/api

Reconciles the client's `Accept` and `Content-Type` with what the matched route is configured to serve, so diverse consumers share one API without custom glue.

#### 11 · DeprecationMiddleware — dotkernel/api

The mechanism behind API evolution.
It reads the `#[ResourceDeprecation]` attribute off the handler and, when one is present, sets the `sunset` and `link` response headers - the client learns an endpoint is going away from the endpoint itself, rather than from a migration guide.

#### 12 · ResponseHeaderMiddleware — dotkernel/dot-response-header

Applies the headers you configure application-wide to every outgoing response, in one place instead of per handler.

#### 13 · UrlHelperMiddleware — mezzio/mezzio-helpers

Seeds the URL helper with the routing result, so handlers and HAL links can be generated relative to the route that was actually matched.

### Identity

#### 14 · AuthenticationMiddleware

*dotkernel/api* — 401

Validates the OAuth 2.0 Bearer token.
Routes declared as authenticated answer `401 Unauthorized` without a valid one, and never reach the handler.

#### 15 · AuthorizationMiddleware

*dotkernel/api* — 403

Checks the authenticated identity's role against the permissions allocated to the matched route name, and returns `403 Forbidden` when it falls short.
The decision lives in configuration, not scattered through handlers.

### Dispatch & fallback

#### 16 · ResourceProviderMiddleware — dotkernel/api

Reads the `#[Resource]` attribute on the handler's `handle()` method and loads the named entity through Doctrine using the route placeholder.
The handler receives a resource rather than an identifier, and a record that does not exist becomes a `404` before your code runs.

#### 17 · DispatchMiddleware — mezzio/mezzio-router

Hands the request to the matched PSR-15 handler.
This is your code - everything above was getting the request into a state where your handler only has to do its own job.

#### 18 · ProblemDetailsNotFoundHandler — mezzio/mezzio-problem-details

Reached only when nothing above returned a response: emits `404` in problem details form.

#### 19 · GetNotFoundResourceHandler — dotkernel/api

The last stage in the pipeline, shaping the not-found response for API clients.

## The packages underneath, in the same order

The same journey seen one level down - which library owns each step.
The second column shows how little changes when a feature is added: sending an email inserts one package and leaves the rest of the flow alone.

### Default flow

A request that reads or writes data and returns a resource.

1. Request
2. mezzio/mezzio-problem-details
3. dotkernel/dot-errorhandler
4. mezzio/mezzio-cors
5. mezzio/mezzio-router
6. dotkernel/dot-response-header
7. mezzio/mezzio-helpers
8. mezzio/mezzio-authentication
9. mezzio/mezzio-authorization
10. mezzio/mezzio-router *dispatch*
11. laminas/laminas-diactoros *not found*
12. dotkernel/dot-dependency-injection
13. doctrine/orm
14. mezzio/mezzio-hal
15. laminas/laminas-diactoros
16. psr/http-message
17. Response

### Flow for sending an email

Identical until the domain layer, where `dot-mail` joins in.

1. Request
2. mezzio/mezzio-problem-details
3. dotkernel/dot-errorhandler
4. mezzio/mezzio-cors
5. mezzio/mezzio-router
6. dotkernel/dot-response-header
7. mezzio/mezzio-helpers
8. mezzio/mezzio-authentication
9. mezzio/mezzio-authorization
10. mezzio/mezzio-router *dispatch*
11. laminas/laminas-diactoros *not found*
12. dotkernel/dot-dependency-injection
13. doctrine/orm
14. dotkernel/dot-mail *added*
15. mezzio/mezzio-hal
16. laminas/laminas-diactoros
17. psr/http-message
18. Response

## Built on interfaces from the Framework Interop Group

Dotkernel applications are collections of PSR-7 middleware.
The standards below are not badges - they are the reason a package from one vendor drops into a pipeline built by another.

### [PSR-7](https://www.php-fig.org/psr/psr-7/)

HTTP messages are immutable value objects.
Every stage above receives a request and returns a response, and neither is a global you have to guard.

### [PSR-15](https://www.php-fig.org/psr/psr-15/)

Middleware and request handlers share one interface, which is what makes the pipeline a plain ordered list rather than a framework-specific event graph.

### [PSR-11](https://www.php-fig.org/psr/psr-11/)

The application is container-based, with each module declaring its dependencies in a `ConfigProvider`.

### [PSR-4](https://www.php-fig.org/psr/psr-4/)

Classes are located by autoloader from their namespace, so where a file lives is never a question of configuration.

### [PSR-3](https://www.php-fig.org/psr/psr-3/)

Errors are logged through `LoggerInterface` via `dot-errorhandler`, reached from the error boundary at stage one.

### Why it matters

You can delete any stage in the pipeline, or write your own and pipe it in, without asking a framework's permission.
That is the practical payoff of standardising on interfaces.

[All PSR standards →](https://www.php-fig.org/psr/)

## One pipeline shape, four applications

The stages differ where the job differs - Frontend adds sessions and template rendering, Queue swaps HTTP dispatch for a message consumer - but the pattern is constant, and all four declare the same `Core` namespaces underneath.

### Shared domain layer

`Core\App` `Core\Admin` `Core\User` `Core\Security` `Core\Setting`

Kept as its own Git repository and added as a submodule, so an entity means the same thing to the endpoint that creates it, the admin screen that moderates it and the worker that emails it.

### [API](https://www.dotkernel.com/api/)

The pipeline documented above, in full.

### [Admin](https://www.dotkernel.com/admin/)

Same shape, plus sessions, CSRF-protected forms and 2FA in the identity phase.

### [Frontend](https://www.dotkernel.com/frontend/)

Same shape, ending in server-rendered templates rather than HAL payloads.

### [Queue](https://www.dotkernel.com/queue/)

The same middleware thinking applied to messages instead of requests.

## The pipeline is one file

Everything on this page comes from `config/pipeline.php` - about ninety lines, most of them comments explaining where to add your own middleware.
There is no generated layer between that file and what runs.

- [Installation guide](https://docs.dotkernel.org/api-documentation/)
- [Try the demo](https://api.dotkernel.net/)

### php ./bin/cli.php route:list

Once the pipeline has routed a request, the route table is the other half of the picture.
This command lists every endpoint the application exposes, which is the fastest way to review an API you have just inherited.

Database migrations are generated and run the same way, through the Doctrine console commands registered by `dot-cli`.

## Open source, in production

No layer you are not allowed to read.

Dotkernel is developed and led by the dev team at Apidemia - built first as an internal tool for handling complex architectures, released under MIT as our way of giving back to the community.

[Talk to us →](https://www.dotkernel.com/contact/)
