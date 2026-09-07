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
Content negotiation lets clients and servers agree on the format and language of exchanged data.
It can be handled server-side or client-side (the latter being more versatile), communicated through HTTP headers or URL patterns, and Dotkernel API implements it out of the box using the `Content-Type` and `Accept` headers.

Content negotiation is an important aspect of RESTful APIs to make it possible for diverse systems to work seamlessly together. It's based on enabling clients and servers to agree on the format and language of data they exchange.

## What is the purpose of Content Negotiation?

RESTful resources can support multiple representations. Each team of developers implements one of more ways (e.g. data formats) to receive requests and return responses on the server side. Efficient communication between client and server can only be guaranteed if the both sides agree on how the exchange takes place. This is especially valid if the request and response support multiple formats. The act of agreeing on a way to represent the exchanged data format is content negotiation.

Content negotiation ensures the following:

- **Support for diverse clients** is useful when the requested representation differs.
  - e.g. `Accept: application/json` or `Accept: application/xml`.
- **Data format flexibility** can come into play when a client prefers a smaller response.
  - e.g. Instead of `Accept: application/json`, use `Accept: application/msgpack` which is a binary serialization, making the response smaller and easier to transfer.
- **Language localization** will respond with content translated into the client's preferred language
  - e.g. `Accept-Language: en-US`.

## Who decides the data format?

There are two sides to the exchange:

- The client
- The server

Technically, either side can decide on how the data is transferred between the two.

For **server-side negotiation** the server must decide based on various factors what the most appropriate format should be. This incurs assumptions that can be erroneous and the server-side implementation can also be more complex. This forces the client to adhere to the rules set up on the server-side.

For **client-side negotiation** the client lets the server know what format it prefers. This approach is more versatile and thus makes more sense.

There are two ways to communicate the data format:

- **HTTP request headers**
- or **resource URI patterns**.

### HTTP Request Headers

The HTTP request headers `Content-Type` and `Accept` are used to determine the data format that will be sent in the request and the response. There are several types to choose from. Here are some examples:

- text/plain
- text/html
- application/json
- application/zip
- image/gif
- image/jpeg

Below is an example of what the keys look like in the content package. Note the `Accept` type

```
Content-Type: application/json, text/plain
Accept: application/json
```

If the `Accept` header is not present, the server gets to decide the format of the response.

### Content Negotiation using URL Patterns

Below are a couple of ways to communicate a preferred data format.

Via the extension on the URL:

```
https://www.example-api.com/record/47.xml
https://www.example-api.com/record/47.json
```

or via an extra parameter:

```
https://www.example-api.com/record/47?format=xml
https://www.example-api.com/record/47?format=json
```

## Defining preferences via a quality factor

The `Accept` header may hold multiple values with an added value that defines preference or priority.

In this example, the client declares it accpets both json and xml formats, with json being preferred over xml, as defined by the numeric value in `q` which can be between 0 and 1. If the server can only satisfy the xml format, it will respond with that. The final alternative is if the server can't respond with either json, or xml, so it responds with what it can.

```
Accept: application/json,application/xml;q=0.9,*/*;q=0.8
```

## How does Dotkernel API handle Content Negotiation?

Out of the box, **Dotkernel API** uses **HTTP request headers** `Content-Type` and `Accept` to handle **client-side** content negotiation. It has both `application/json`, `application/hal+json` included. Of course, you can change these as development progresses for your project. There is also support for per-route content negotiation, if you should need it.

The configuration is done is its own configuration file. The validation is automatic and several explicit errors are handled, based on what format is supported.

Check out the relevant links below for exact details on the Dotkernel implementation of content negotiation.

## Relevant Links

[Content Negotiation in Dotkernel API](https://docs.dotkernel.org/api-documentation/v5/core-features/content-validation/)

[Content types on iana.org](https://www.iana.org/assignments/media-types/media-types.xhtml)

## FAQ

**Q: What is content negotiation?**
A: It's the act of a client and server agreeing on the format and language of the data they exchange, which is important for RESTful APIs since resources can support multiple representations.

**Q: What does content negotiation ensure?**
A: It ensures support for diverse clients (e.g. Accept: application/json or Accept: application/xml), data format flexibility for smaller responses (e.g. Accept: application/msgpack, a binary serialization), and language localization via headers like Accept-Language: en-US.

**Q: Who decides the data format, the client or the server?**
A: Either side technically can. In server-side negotiation, the server decides based on various factors, which can introduce erroneous assumptions and more complex implementation, forcing the client to adhere to server rules. In client-side negotiation, the client tells the server what format it prefers, which is more versatile and makes more sense.

**Q: How can the preferred data format be communicated?**
A: Via HTTP request headers (Content-Type and Accept) or via resource URI patterns, such as a file extension in the URL (e.g. /record/47.json) or an extra query parameter (e.g. /record/47?format=json). If the Accept header is not present, the server decides the response format.

**Q: How does the quality factor (q) work in the Accept header?**
A: The Accept header can list multiple accepted formats with a q value between 0 and 1 to express preference, e.g. Accept: application/json,application/xml;q=0.9,*/*;q=0.8. The server responds with the most preferred format it can satisfy, falling back further down the list if needed.

**Q: How does Dotkernel API handle content negotiation?**
A: Out of the box, Dotkernel API uses the Content-Type and Accept HTTP request headers to handle client-side content negotiation, supporting both application/json and application/hal+json. These can be changed as needed, and per-route content negotiation is also supported.
