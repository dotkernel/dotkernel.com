---
title: "Basic Security in Dotkernel Headless Platform"
description: "A practical overview of software security practices implemented across the Dotkernel Headless Platform, covering input validation, content negotiation, CORS, RBAC, OAuth2, sessions, dependencies, and more."
author: "Florin Bidirean"
date_published: "2025-10-14"
canonical_url: "https://www.dotkernel.com/best-practice/basic-security-in-dotkernel-headless-platform/"
category: "Best Practice"
language: "en"
---

# Basic Security in Dotkernel Headless Platform

## TL;DR

Software security should always be top of mind for a developer, since ignoring it can lead to major costs, data loss, GDPR fines, or the loss of client trust. The article surveys many facets of software security and walks through the practical measures Dotkernel Headless Platform takes for each: input validation, content negotiation, CORS, RBAC, demo credentials, error reporting, OpenAPI docs, PHP and JavaScript dependencies, OAuth2, session/cookie settings, and CI checks.

**Software security** should always be in the back of your mind as a developer. It may seem fine at first to deliver a feature sooner, only to find later on that you left a backdoor into your crisp new update. You ignore security at your own risk, with potentially major costs to your personal or your organization's image, and to your client's trust in your abilities. The costs to recover the damages caused by a lacking security are sometimes astronomical, enough to put a company out of business.

There are many facets of software security, meaning there are a lot of potential ways a hacker can access your code or your data fraudulently:

- Authentication and access control.
- Data protection.
- Input validation and injection.
- Web and API security.
- Dependency and supply chain risks.
- Configuration and deployment.
- Network and infrastructure security.
- Logging, monitoring and incident response.
- Secure software development lifecycle.
- Human and organizational factors.

To keep a platform safe, you must actively mitigate these risks with recommended coding practices that include tight security. You wouldn't build a nice house, only to leave the door unlocked, right?

## The Tenets of Software Security in Dotkernel Headless Platform

We at Dotkernel aim to:

- Create code that follows software security guidelines.
- Implement community recommendations related to software security.
- Use 3rd-party code and libraries from trusted sources.
- Constantly monitor software news related to security vulnerabilities and mitigate them as soon as possible.

We do all this to attempt to stay ahead of the vulnerabilities that can lead from otherwise useful, productive code, to data loss and a GDPR fine, or a loss of funds for our clients.

Let's take a practical view on software security in Dotkernel.

## Form Input Validation

You should never trust that the user inputs correct data by passing it directly into your business logic. By defining the configuration for an input filter, you ensure that a field is both present, and of the correct type.

[Dotkernel API](https://www.dotkernel.org) makes use of [laminas/laminas-inputfilter](https://github.com/laminas/laminas-inputfilter) for this purpose.

In addition to the above filtering, Dotkernel Admin also makes use of [laminas/laminas-form](https://github.com/laminas/laminas-form). laminas-form contains:

- A thin layer of objects representing form elements.
- An InputFilter for each input, like mentioned previously, or custom validators.
- Methods for binding data to and from the form.

laminas-form ensures that data validation, filtering, and rendering enforce strong security practices by design. It also has integration with the Laminas Security Ecosystem that contains laminas-escaper, laminas-validator, laminas-session, and laminas-filter.

## Content Negotiation

Content negotiation is used in RESTful APIs to ensure that systems work seamlessly together by having the client and server agree on the format and language of data they exchange.

Dotkernel API handles content negotiation via a middleware configured in the `config/autoload/content-negotiation.global.php` file. It handles client-side content negotiation via the use of two HTTP request headers: `Content-Type` and `Accept`, and returns `application/json`, `application/hal+json` data formats.

## Cross-Origin Resource Sharing

Cross-Origin Resource Sharing (or CORS) is a security mechanism implemented into web browsers to control how web pages can request resources from a different domain than the one where the request originated from.

In Dotkernel API, CORS is handled by [mezzio/mezzio-cors](https://github.com/mezzio/mezzio-cors) and configured in the `config/autoload/cors.local.php` file. mezzio-cors starts to detect the proper `cors` configuration whenever it detects a `cors preflight`. Cors validates the call using several configuration items: origins, headers, max age, credentials.

> When configuring your pipeline, make sure to add the CorsMiddleware BEFORE the RouteMiddleware.

## Role-Based Access Control

Role-Based Access Control (or RBAC) is a security model used in software systems to manage access to resources. It does this by assigning roles to user types which are in turn assigned to users who require a certain level of access.

Dotkernel API uses [mezzio/mezzio-authorization-rbac](https://github.com/mezzio/mezzio-authorization-rbac) for this purpose. There are several roles predefined, which you can configure to suit your project by editing the `config/autoload/authorization.global.php` file.

## Demo Credentials

Demo credentials are provided in Dotkernel API for your convenience, to allow you to test the installation easily.

> It is important to **update or remove** these accounts in your production environment.

## Error Reporting Endpoint and ErrorReportingTokens

The purpose for the error reporting endpoint is to have a reliable channel through which 3rd-party developers can report issues to you directly.

Dotkernel API has a dedicated endpoint `/error-report` for this purpose. It uses an `ErrorReportingToken` set up in the configuration file `config/autoload/error-handling.global.php`.

## OpenAPI Documentation

OpenAPI documentation (formerly known as Swagger) provides a standardized, machine-readable way to describe APIs, meaning their `requests` and `responses`. It's critical for:

- Developer efficiency - it streamlines communication between front and back end developers, and it allows developers to use mock servers before the backend is fully implemented.
- Reliability - documentation can be auto-generated, testing is easier.
- Integration - several tools support OpenAPI, like Postman and Codegen libraries for multiple libraries.

Dotkernel API implements [zircote/swagger-php](https://github.com/zircote/swagger-php) to provide an interactive documentation.

> Do **not** include sensitive information for your endpoints. Do **not** enable documentation in a production environment.

## PHP Dependencies

Modern PHP projects rely heavily on external packages via package managers like Composer. There is a tangible risk of exposing your application by using insecure dependencies.

Dotkernel API has regular checks for vulnerable and outdated packages. Often the dependencies used in projects have transient dependencies which must also be checked.

> Always use dependencies from reliable sources and keep them updated to their latest version.

## OAuth2 Security

OAuth 2.0 is a secure authorization framework that allows one application to access resources or data on behalf of a user, without requiring the user's password. It is considered an industry standard for secure authorization across web, mobile, and API-based systems.

Dotkernel API uses the [mezzio/mezzio-authentication-oauth2](https://github.com/mezzio/mezzio-authentication-oauth2) for the OAuth2 authentication service. The package itself is secure, but you still need to make sure you use it properly:

- Replace or update the default `admin` and `frontend` clients on your production environment.
- Update the `access` and `refresh` tokens to match your application's requirements. The defaults are one day for `access` and one month for `refresh`.
- Make sure to **not** commit any local keys generated by `./vendor/bin/generate-oauth2-keys`. They are used to verify the transmitted JWTs.

## Session and Cookie Settings

Sessions and cookies are used in web development to store data between HTTP requests. For example, they can be used to save login information or preferences, and to track user behavior.

Dotkernel configures cookies in the `config/autoload/session.global.php` file. It contains several parameters that you must revise and adapt to your application:

- session_config.cookie_httponly
- session_config.cookie_samesite
- session_config.cookie_secure

## JavaScript Dependencies

Very much like `composer` for PHP, JavaScript has its own dependencies, usually installed via `npm` or `yarn`. The JavaScript ecosystem has recently been attacked by hackers who targeted several widely used npm packages that have a total number of uses in the billions.

Dotkernel uses `npm` to handle JavaScript dependencies. We monitor the news to stay on top of these security issues and use npm packages from reliable sources. Even so, you should regularly use the `npm audit` to check for vulnerabilities among your installed npm libraries.

## Other Security Considerations

All components of **Dotkernel Headless Platform** have several configuration files with the name format `*.global.php`, `*.php.dist` and `*.local.php`. You must **only** include sensitive information in the `*.local.php` files, since they are, by default, ignored by the VCS.

The `development mode` is designed, as the name suggests, only for the development period. By enabling development mode, you enable features like debug mode, cache clear and show error details. These should be hidden from the production environment to avoid exposing sensitive data or code.

The GitHub Action [Laminas Continuous Integration](https://github.com/laminas/laminas-continuous-integration-action) is an integral component of Dotkernel API. It ensures code quality by streamlining the execution of PHP quality assurance (QA) tasks within continuous integration (CI) workflows. Most often triggered by commits to the repository, it builds a matrix of tests: static analysis, coding standards checks, and unit tests.

## Resources

- [Basic Security in Dotkernel Admin](https://docs.dotkernel.org/admin-documentation/v6/security/basic-security/)
- [Basic Security in Dotkernel API](https://docs.dotkernel.org/api-documentation/v6/security/basic-security/)
- [Content Negotiation in Dotkernel REST API](https://www.dotkernel.com/dotkernel-api/content-negotiation-in-dotkernel-rest-api/)
- [laminas-form Documentation](https://docs.laminas.dev/laminas-form/v3/intro/)
- [CORS in Dotkernel API](https://docs.dotkernel.org/api-documentation/v6/tutorials/cors/)
- [CORS Policy Setup in Dotkernel](https://www.dotkernel.com/how-to/mezzio-cors-implementation-in-dotkernel/)
- [Error Reporting Endpoint](https://docs.dotkernel.org/api-documentation/v6/core-features/error-reporting/)
- [OpenAPI Documentation](https://docs.dotkernel.org/api-documentation/v6/openapi/introduction/)
- [mezzio/mezzio-authentication-oauth2 Configuration](https://docs.mezzio.dev/mezzio-authentication-oauth2/v1/intro/#configuration)

## FAQ

**Q: What are the main facets of software security to consider?**
A: Software security spans many areas: authentication and access control, data protection, input validation and injection, web and API security, dependency and supply chain risks, configuration and deployment, network and infrastructure security, logging/monitoring and incident response, secure software development lifecycle, and human and organizational factors.

**Q: How does Dotkernel handle form input validation?**
A: Dotkernel API uses laminas/laminas-inputfilter to ensure a field is present and of the correct type.
Dotkernel Admin additionally uses laminas/laminas-form, which provides form element objects, an InputFilter for each input (or custom validators), and methods for binding data to and from the form, integrating with laminas-escaper, laminas-validator, laminas-session, and laminas-filter.

**Q: How does Dotkernel API handle content negotiation?**
A: Content negotiation is handled via a middleware configured in the `config/autoload/content-negotiation.global.php` file.
It uses the Content-Type and Accept HTTP request headers to negotiate with the client, returning application/json or application/hal+json data formats.

**Q: How is CORS handled and configured in Dotkernel API?**
A: CORS is handled by mezzio/mezzio-cors and configured in the `config/autoload/cors.local.php` file, validating calls using configuration items like origins, headers, max age, and credentials.
When configuring the pipeline, the CorsMiddleware must be added before the RouteMiddleware.

**Q: What should be done with the demo credentials before going to production?**
A: Demo credentials are provided for convenience during installation testing, but it is important to update or remove these accounts in your production environment.

**Q: What are the security recommendations around OpenAPI documentation?**
A: You should not include sensitive information for your endpoints in the OpenAPI documentation, and you should not enable the documentation in a production environment.
