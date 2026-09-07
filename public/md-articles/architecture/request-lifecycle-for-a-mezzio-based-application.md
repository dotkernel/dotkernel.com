---
title: "Request Lifecycle for a Mezzio-Based Application"
description: "A step-by-step walkthrough of how Dotkernel Light, a Mezzio-based application, handles an HTTP request from bootstrap through to the emitted response."
author: "Florin Bidirean"
date_published: "2026-05-26"
canonical_url: "https://www.dotkernel.com/architecture/request-lifecycle-for-a-mezzio-based-application/"
category: "Architecture"
language: "en"
---

# Request Lifecycle for a Mezzio-Based Application

## TL;DR
The request lifecycle is the sequence of steps that happen from the moment a user makes an HTTP request until the server sends back a response.
This is illustrated using Dotkernel Light, one of the applications in the Dotkernel Headless Platform suite, walking through entry point setup, routing, handler execution, template rendering, response creation, and the response emitter.

## Seamlessly Interconnected Middleware for Enterprise-Level Solutions

The request lifecycle is the sequence of steps that happen from the moment a user makes an HTTP request until the server sends back a response.

The graph below shows how the request is handled by **Dotkernel Light** ([GitHub](https://github.com/dotkernel/light), [documentation](https://docs.dotkernel.org/light-documentation/)), one of the applications in the [Dotkernel Headless Platform suite](https://github.com/dotkernel).

> Hover over items for description

Entry Point

1. HTTP Request
[public/index.php]

2. Service Container

3. Route Registration

4. Middleware Pipeline
[config/pipeline.php]

5. Routing

6. Handler
Invocation

7. Custom Logic
Execution in Handler

8. Template
Rendering [twig]

9. Response
Creation

10. Response
Pipeline

asd

asd

asd

11. Response Emitter

HTTP 20x, 30x

HTTP 40x

HTTP 50x

## FAQ

**Q: What is the request lifecycle?**
A: The request lifecycle is the sequence of steps that happen from the moment a user makes an HTTP request until the server sends back a response.

**Q: What happens at the entry point of a request?**
A: The application bootstraps and loads configuration to create the Mezzio application instance, registers factories, aliases and delegators in the service container, reads all available routes with their allowed request methods and registers them (managed by FastRoute), and loads the predefined order of middleware in the pipeline.

**Q: How does routing work in a Mezzio-based application?**
A: FastRoute matches the incoming URL and method against the registered routes, for example matching a GET request to /page/about against the GetPageViewHandler handler under the route name page::about.

**Q: What happens during handler invocation?**
A: The matched route name is extracted from the request attribute and passed to the renderer, using code similar to $template = $request->getAttribute(RouteResult::class)->getMatchedRouteName();, after which the handler executes the custom business logic.

**Q: What happens during template rendering?**
A: Twig loads the matched template file, applies the layout it extends, renders its blocks, and includes any partials, producing the final HTML output.

**Q: How is the response created and returned to the browser?**
A: An HtmlResponse is created with a status code, headers, and the rendered HTML body. It then flows back through the middleware stack in reverse (the response pipeline), where middleware can modify headers, cookies, or compress content, before the response emitter sends the final response back to the browser as HTTP 20x/30x, 40x, or 50x.
