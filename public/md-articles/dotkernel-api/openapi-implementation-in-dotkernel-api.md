---
title: "OpenAPI implementation in Dotkernel API"
description: "An overview of the OpenAPI Specification, why it complements tools like Postman, and how Dotkernel API implements OpenAPI documentation across its modules."
author: "Florin Bidirean"
date_published: "2024-07-30"
canonical_url: "https://www.dotkernel.com/dotkernel-api/openapi-implementation-in-dotkernel-api/"
category: "Dotkernel API"
language: "en"
---

# OpenAPI implementation in Dotkernel API

## TL;DR

OpenAPI is a specification for describing an API's structure in a language-agnostic, machine-readable way, offering benefits like standardization, automatic documentation, upfront design, and better collaboration compared to a tool like Postman.
Dotkernel API has full OpenAPI support: each module (Admin, App, User) documents its endpoints in an `OpenAPI.php` file, which `zircote/swagger-php` turns into documentation rendered via Swagger UI or Redoc.
Testing protected endpoints in Swagger UI requires generating an authentication token that matches the endpoint's required privileges.

## What Is OpenAPI?

The OpenAPI Specification provides a consistent way to develop and interact with an API.
It defines API structure and syntax in a universal way, regardless of the programming language used in the API's development.
API specifications typically use YAML or JSON to share and use the specification.
They allow users of the API to quickly discover how it works by describing its elements, e.g. endpoints, request and response formats, security mechanisms and more.

While not mutually exclusive, OpenAPI has several benefits over Postman:

- API standardization: this offers a standard way to describe and document endpoints, request/response models, and other details of your API that enforces design best practices.
Postman has no focus on this topic.
- Automatic generation of API documentation: create comprehensive, machine-readable documentation that helps developers understand how to interact with your API.
Postman is not designed to explain the API's components.
- API design and development: define your API specification, most commonly using YAML and JSON formats, before starting development.
Postman is used only to test an existing, completed endpoint.
- Improved collaboration: this benefits frontend and backend developers, as well as operations teams.
Postman's free tier is aimed more towards individual or small team development.
- API gateways and management: a wide range of tools and platforms that support OpenAPI allow more streamlined monitoring and management of APIs.
Postman has environment management, but primarily on the developer's machine.

Other benefits from using OpenAPI:

- Code generation: automatically generate client code, server stubs, API documentation and even test cases to ensure consistency between the API documentation and implementation.
- Interoperability: standardization using OpenAPI ensures that the API can interface with other systems.
- Testing and validation: the specification can generate tests to catch bugs early on and ensure correct functionality.
- Versioning and change management: keeps track of changes and ensures backward compatibility.

## The Importance of API Documentation

API documentation, in general, is crucial for several reasons.
It serves multiple stakeholders that use the API for development, integration and maintenance.

- Faster developer onboarding, adoption and integration: helps developers understand the API better and reduces the learning curve for adopting and integrating the API into other systems.
The API documentation should be publicly available, especially if the API is public.
It's even more beneficial if the documentation is integrated with a developer portal.
- Better collaboration: promotes consistency and reduces misunderstandings between developers and users.
- Better API quality and maintenance: includes details on how to properly use the API, from its data types and required parameters, to error handling procedures.
This helps maintain existing functionality when changes are implemented.
- Helps troubleshooting: it defines the correct functionality that helps developers and maintainers find and fix bugs more effectively.

## OpenAPI in Dotkernel API

Dotkernel API has full support for OpenAPI, from describing the endpoints and generating the documentation, to rendering and testing the endpoints.

Each module (Admin, App, User) in Dotkernel API contains a file named `OpenAPI.php`.
In this file you must document all of the endpoints from `RoutesDelegator.php`.
The entries in `OpenAPI.php` have several descriptive items, the most important being method, request and response.
These are used to generate a documentation file from the command line.
The static documentation file is rendered using Swagger UI or Redoc in a user-friendly way.
You can read more about this [starting here](https://docs.dotkernel.org/api-documentation/v5/openapi/introduction/) and its subsequent pages.

### Describing OpenAPI Components

All OpenAPI components require a handful of components that are universally valid for a given project.
These are below:

- OA\Info contains basic information on your project, like version and name.
- OA\Server has one or more urls to a target host.
- OA\SecurityScheme describes the protection for the endpoint.
- OA\ExternalDocumentation has a url and description for extended documentation related to an item.
- OA\Schema describes a object (e.g. entity) or collection of objects in your project.

Read more details about the above [here](https://docs.dotkernel.org/api-documentation/v5/openapi/initialized-components/).

Once you have your basic components defined, you can begin work on the endpoints.
The endpoints already made available in Dotkernel API are documented, so you must do the same for the new endpoints you create in your project.
This is done by defining these items:

- the request object (Get, Post, Patch, Put, Delete)
- the path to the resource
- the endpoint's summary and description
- the query/path parameters, if required
- the request body, if required
- the security scheme, if required
- the response

Wherever it's appropriate, schemas should be used to ensure consistency.
The optional 'tags' item can be used to group operations together.
Read more [here](https://docs.dotkernel.org/api-documentation/v5/openapi/initialized-components/).

### Generating the Documentation

The documentation is generated using [zircote/swagger-php](https://github.com/zircote/swagger-php).
It uses the descriptions you added in the `OpenAPI.php` files to build the documentation file.
The documentation contents can be listed in the command line or saved to a file in yaml of json format.
You can read more [here](https://docs.dotkernel.org/api-documentation/v5/openapi/generate-documentation/).

### Alternatives for Rendering the Documentation

Once you have the documentation generated, it can be rendered in two ways:

- Swagger UI allows you to visualize and interact with the API's resources without worrying about the implementation logic.
- Redoc lists the documentation in read-only mode, detailing example requests and responses.

### Handling Authentication for Swagger UI

Most endpoints for your API should be protected, so to access them you are required to generate an authentication token (AuthToken).
The token is related to the user type, so make sure to check the privileges required for the endpoint you are testing.
After you submit the token, you can test the endpoints as an authenticated user.
Clicking on the 'Try it out' button will activate the required parameter input fields and the textarea for the request body.
The 'Execute' button will send the request and return the response, along with its HTTP status code.
You can read more details [here](https://docs.dotkernel.org/api-documentation/v5/openapi/use-documentation/).

## FAQ

**Q: What is the OpenAPI Specification?**
A: A consistent way to develop and interact with an API. It defines API structure and syntax in a universal way, regardless of the programming language used, typically described in YAML or JSON so users can quickly discover endpoints, request/response formats, security mechanisms and more.

**Q: How does OpenAPI compare to Postman?**
A: OpenAPI standardizes how endpoints and request/response models are described, automatically generates machine-readable documentation, lets you define the API specification before development starts, and improves collaboration across teams.
Postman, by contrast, is used mainly to test an already-completed endpoint and has no real focus on standardized documentation or upfront design.

**Q: Where do you document endpoints in Dotkernel API?**
A: Each module (Admin, App, User) contains an OpenAPI.php file, where all endpoints from that module's RoutesDelegator.php must be documented, primarily describing the method, request and response.

**Q: What core components does every OpenAPI description need?**
A: OA\Info (basic project info like version and name), OA\Server (one or more target host URLs), OA\SecurityScheme (endpoint protection), OA\ExternalDocumentation (link and description for extended docs), and OA\Schema (describing an object or collection of objects).

**Q: What generates the documentation file from the OpenAPI.php descriptions?**
A: zircote/swagger-php, which uses the descriptions added in the OpenAPI.php files to build the documentation. The result can be listed in the command line or saved to a file in YAML or JSON format.

**Q: How is the generated documentation rendered, and how do you test protected endpoints?**
A: It can be rendered with Swagger UI, which lets you visualize and interact with the API's resources, or with Redoc, which lists the documentation in read-only mode. Because most endpoints are protected, testing them in Swagger UI requires generating an authentication token (AuthToken) matching the required privileges, then using the 'Try it out' button to fill in parameters/body and 'Execute' to send the request and see the response with its HTTP status code.
