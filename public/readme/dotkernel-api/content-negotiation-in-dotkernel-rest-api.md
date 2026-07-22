---
title: "Content Negotiation in Dotkernel REST API"
description: "How content negotiation works in RESTful APIs, who decides the data format, and how Dotkernel API implements it."
author: "Florin Bidirean"
date_published: "2024-11-27"
canonical_url: "https://www.dotkernel.com/dotkernel-api/content-negotiation-in-dotkernel-rest-api/"
category: "Dotkernel API"
language: "en"
---

# Content Negotiation in Dotkernel REST API

## TL;DR

Content negotiation lets clients and servers agree on the format and language of exchanged data. It can be handled server-side or client-side (the latter being more versatile), communicated through HTTP headers or URL patterns, and Dotkernel API implements it out of the box using the `Content-Type` and `Accept` headers.

## What is the Purpose of Content Negotiation?

RESTful resources can support multiple representations, and efficient client-server communication depends on both sides agreeing on the exchanged data format - this agreement is content negotiation. It ensures:

- **Support for diverse clients**, e.g. `Accept: application/json` or `Accept: application/xml`.
- **Data format flexibility**, e.g. using `Accept: application/msgpack` (a binary serialization) instead of JSON for a smaller, easier-to-transfer response.
- **Language localization**, e.g. `Accept-Language: en-US`, to respond with content translated into the client's preferred language.

## Who Decides the Data Format?

Either the client or the server can decide:

- **Server-side negotiation**: the server decides the format based on various factors. This can introduce erroneous assumptions and a more complex server-side implementation, and forces the client to adhere to the server's rules.
- **Client-side negotiation**: the client tells the server what format it prefers. This approach is more versatile and makes more sense.

There are two ways to communicate the preferred data format: HTTP request headers, or resource URI patterns.

### HTTP Request Headers

The `Content-Type` and `Accept` headers determine the data format sent in the request and response. Examples of types include `text/plain`, `text/html`, `application/json`, `application/zip`, `image/gif`, and `image/jpeg`.

```shell
Content-Type: application/json, text/plain
Accept: application/json
```

If the `Accept` header is not present, the server decides the response format.

### Content Negotiation Using URL Patterns

A preferred format can also be communicated via the URL extension:

```shell
https://www.example-api.com/record/47.xml
https://www.example-api.com/record/47.json
```

or via an extra query parameter:

```shell
https://www.example-api.com/record/47?format=xml
https://www.example-api.com/record/47?format=json
```

## Defining Preferences via a Quality Factor

The `Accept` header can hold multiple values with an added quality value (`q`, between 0 and 1) that defines preference or priority:

```shell
Accept: application/json,application/xml;q=0.9,*/*;q=0.8
```

In this example, the client accepts both JSON and XML, with JSON preferred. If the server can only satisfy XML, it responds with that; if it can satisfy neither, it responds with whatever it can.

## How Does Dotkernel API Handle Content Negotiation?

Out of the box, Dotkernel API uses the `Content-Type` and `Accept` HTTP request headers to handle client-side content negotiation, supporting both `application/json` and `application/hal+json`. These can be changed as development progresses, and per-route content negotiation is also supported. Configuration lives in its own configuration file, validation is automatic, and several explicit errors are handled based on the supported format.

## FAQ

**Q: What is content negotiation?**
A: It's the act of a client and server agreeing on the format and language of the data they exchange, which is important for RESTful APIs since resources can support multiple representations.

**Q: What does content negotiation ensure?**
A: It ensures support for diverse clients (e.g. `Accept: application/json` or `Accept: application/xml`), data format flexibility for smaller responses (e.g. `Accept: application/msgpack`, a binary serialization), and language localization via headers like `Accept-Language: en-US`.

**Q: Who decides the data format, the client or the server?**
A: Either side technically can. In server-side negotiation, the server decides based on various factors, which can introduce erroneous assumptions and more complex implementation, forcing the client to adhere to server rules. In client-side negotiation, the client tells the server what format it prefers, which is more versatile and makes more sense.

**Q: How can the preferred data format be communicated?**
A: Via HTTP request headers (`Content-Type` and `Accept`) or via resource URI patterns, such as a file extension in the URL (e.g. `/record/47.json`) or an extra query parameter (e.g. `/record/47?format=json`). If the `Accept` header is not present, the server decides the response format.

**Q: How does the quality factor (q) work in the Accept header?**
A: The `Accept` header can list multiple accepted formats with a `q` value between 0 and 1 to express preference, e.g. `Accept: application/json,application/xml;q=0.9,*/*;q=0.8`. The server responds with the most preferred format it can satisfy, falling back further down the list if needed.

**Q: How does Dotkernel API handle content negotiation?**
A: Out of the box, Dotkernel API uses the `Content-Type` and `Accept` HTTP request headers to handle client-side content negotiation, supporting both `application/json` and `application/hal+json`. These can be changed as needed, and per-route content negotiation is also supported.

## Resources

- [Content Negotiation in Dotkernel API](https://docs.dotkernel.org/api-documentation/v5/core-features/content-validation/)
- [Content types on iana.org](https://www.iana.org/assignments/media-types/media-types.xhtml)
