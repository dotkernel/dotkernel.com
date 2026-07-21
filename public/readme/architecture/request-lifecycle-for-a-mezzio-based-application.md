---
title: "Request Lifecycle for a Mezzio-Based Application"
description: "A step-by-step walkthrough of how Dotkernel Light, a Mezzio-based application, handles an HTTP request from bootstrap through to the emitted response."
author: "Florin Bidirean"
date_published: "2026-05-26"
canonical_url: "https://new.dotkernel.com/architecture/request-lifecycle-for-a-mezzio-based-application/"
category: "Architecture"
language: "en"
---

# Request Lifecycle for a Mezzio-Based Application

## TL;DR

The request lifecycle is the sequence of steps that happen from the moment a user makes an HTTP request until the server sends back a response. This is illustrated using Dotkernel Light, one of the applications in the Dotkernel Headless Platform suite, walking through entry point setup, routing, handler execution, template rendering, response creation, and the response emitter.

## The Request Lifecycle, Step by Step

### Entry Point

1. **HTTP Request** - Bootstrap the application, load configuration and create the Mezzio application instance.
2. **Service Container** - Register factories, aliases and delegators. All services are configured and ready to use.
3. **Route Registration** - Read all available routes with their allowed request methods and dynamically register them in the application. Routes are managed by FastRoute. Example: `/page/about` -> `GetPageViewHandler`, Method: `GET`, Route name: `page::about`.
4. **Middleware Pipeline** - Loads the predefined order of middleware. It defines how incoming HTTP requests move through the application and how responses are generated.

### Processing

5. **Routing** - FastRoute matches the URL and method against registered routes. Match: `GET /page/about`, Handler: `GetPageViewHandler`, Route name: `page::about`.
6. **Handler Invocation** - Extract the matched route name from the request and pass it to the renderer:
   ```php
   $template = $request->getAttribute(RouteResult::class)->getMatchedRouteName();
   // $template = 'page::about';
   ```
7. **Custom Logic Execution in Handler** - Execute the business logic in the handler. The process can involve services and any custom logic.
8. **Template Rendering** - Twig loads the template, applies the layout, renders blocks and includes partials. Load: `src/Page/templates/page/about.html.twig`, Extends: `@layout/default.html.twig`, Render blocks: `title`, `content`, Include partials: `alerts.html.twig`, etc., Output: Final HTML.
9. **Response Creation** - An `HtmlResponse` is created with status, headers and the rendered HTML body. Status: `200 OK`, Content-Type: `text/html; charset=utf-8`, Body: Rendered HTML.
10. **Response Pipeline** - The response flows back through the middleware stack. Middleware can modify headers, cookies, compress content, etc.

### Exit Point

11. **Response Emitter** - The final response is sent back to the browser. The page is rendered and sent to the user, as one of `HTTP 20x/30x`, `HTTP 40x`, or `HTTP 50x`.

## FAQ

**Q: What is the request lifecycle?**
A: The request lifecycle is the sequence of steps that happen from the moment a user makes an HTTP request until the server sends back a response.

**Q: What happens at the entry point of a request?**
A: The application bootstraps and loads configuration to create the Mezzio application instance, registers factories, aliases and delegators in the service container, reads all available routes with their allowed request methods and registers them (managed by FastRoute), and loads the predefined order of middleware in the pipeline.

**Q: How does routing work in a Mezzio-based application?**
A: FastRoute matches the incoming URL and method against the registered routes, for example matching a GET request to `/page/about` against the `GetPageViewHandler` handler under the route name `page::about`.

**Q: What happens during handler invocation?**
A: The matched route name is extracted from the request attribute and passed to the renderer, using code similar to `$template = $request->getAttribute(RouteResult::class)->getMatchedRouteName();`, after which the handler executes the custom business logic.

**Q: What happens during template rendering?**
A: Twig loads the matched template file, applies the layout it extends, renders its blocks, and includes any partials, producing the final HTML output.

**Q: How is the response created and returned to the browser?**
A: An `HtmlResponse` is created with a status code, headers, and the rendered HTML body. It then flows back through the middleware stack in reverse (the response pipeline), where middleware can modify headers, cookies, or compress content, before the response emitter sends the final response back to the browser as HTTP 20x/30x, 40x, or 50x.

## Resources

- [Dotkernel Light on GitHub](https://github.com/dotkernel/light)
- [Dotkernel Light documentation](https://docs.dotkernel.org/light-documentation/)
- [Dotkernel Headless Platform suite on GitHub](https://github.com/dotkernel)
