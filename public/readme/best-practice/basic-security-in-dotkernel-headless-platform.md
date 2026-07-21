---
title: "Basic Security in Dotkernel Headless Platform"
description: "A practical overview of software security practices implemented across the Dotkernel Headless Platform, covering input validation, content negotiation, CORS, RBAC, OAuth2, sessions, dependencies, and more."
author: "Florin Bidirean"
date_published: "2025-10-14"
canonical_url: "https://new.dotkernel.com/best-practice/basic-security-in-dotkernel-headless-platform/"
category: "Best Practice"
language: "en"
---

# Basic Security in Dotkernel Headless Platform

## TL;DR

Software security should always be top of mind for a developer, since ignoring it can lead to major costs, data loss, GDPR fines, or the loss of client trust. The article surveys many facets of software security and walks through the practical measures Dotkernel Headless Platform takes for each: input validation, content negotiation, CORS, RBAC, demo credentials, error reporting, OpenAPI docs, PHP and JavaScript dependencies, OAuth2, session/cookie settings, and CI checks.

## Facets of Software Security

There are many potential ways a hacker can access code or data fraudulently:

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

## The Tenets of Software Security in Dotkernel Headless Platform

Dotkernel aims to:

- Create code that follows software security guidelines.
- Implement community recommendations related to software security.
- Use 3rd-party code and libraries from trusted sources.
- Constantly monitor software news related to security vulnerabilities and mitigate them as soon as possible.

## Form Input Validation

Never trust that user input is correct by passing it directly into business logic. By defining the configuration for an input filter, a field's presence and type are both ensured. Dotkernel API uses laminas/laminas-inputfilter for this purpose. Dotkernel Admin additionally uses laminas/laminas-form, which contains a thin layer of objects representing form elements, an InputFilter for each input (or custom validators), and methods for binding data to and from the form. laminas-form integrates with the Laminas Security Ecosystem: laminas-escaper, laminas-validator, laminas-session, and laminas-filter.

## Content Negotiation

Content negotiation is used in RESTful APIs so client and server agree on the format and language of exchanged data. Dotkernel API handles this via a middleware configured in `config/autoload/content-negotiation.global.php`, using the `Content-Type` and `Accept` HTTP request headers, and returning `application/json` or `application/hal+json` data formats.

## Cross-Origin Resource Sharing

CORS is a browser security mechanism controlling how web pages can request resources from a different domain. In Dotkernel API, CORS is handled by mezzio/mezzio-cors and configured in `config/autoload/cors.local.php`. It starts detecting the proper `cors` configuration whenever it detects a `cors preflight`, validating the call using configuration items: origins, headers, max age, and credentials.

> When configuring your pipeline, make sure to add the CorsMiddleware BEFORE the RouteMiddleware.

## Role-Based Access Control

RBAC manages access to resources by assigning roles to user types, which are in turn assigned to users requiring a certain level of access. Dotkernel API uses mezzio/mezzio-authorization-rbac for this purpose, with several predefined roles configurable in `config/autoload/authorization.global.php`.

## Demo Credentials

Demo credentials are provided in Dotkernel API for convenience, to allow easy testing of the installation.

> It is important to update or remove these accounts in your production environment.

## Error Reporting Endpoint and ErrorReportingTokens

The error reporting endpoint provides a reliable channel through which 3rd-party developers can report issues directly. Dotkernel API has a dedicated `/error-report` endpoint for this, using an `ErrorReportingToken` set up in `config/autoload/error-handling.global.php`.

## OpenAPI Documentation

OpenAPI documentation (formerly Swagger) provides a standardized, machine-readable way to describe API requests and responses. It's critical for developer efficiency (streamlines front/back-end communication, allows mock servers before the backend is implemented), reliability (auto-generated docs, easier testing), and integration (tools like Postman and Codegen libraries). Dotkernel API implements zircote/swagger-php to provide interactive documentation.

> Do not include sensitive information for your endpoints. Do not enable documentation in a production environment.

## PHP Dependencies

Modern PHP projects rely heavily on external packages via Composer, and there is a tangible risk of exposing an application through insecure dependencies. Dotkernel API has regular checks for vulnerable and outdated packages, including transient dependencies.

> Always use dependencies from reliable sources and keep them updated to their latest version.

## OAuth2 Security

OAuth 2.0 is a secure authorization framework letting one application access resources on behalf of a user without requiring the user's password, an industry standard for web, mobile, and API-based systems. Dotkernel API uses mezzio/mezzio-authentication-oauth2 for OAuth2 authentication. The package itself is secure, but it must be used properly:

- Replace or update the default `admin` and `frontend` clients on production.
- Update the `access` and `refresh` tokens to match your application's requirements (defaults are one day for access, one month for refresh).
- Never commit any local keys generated by `./vendor/bin/generate-oauth2-keys`, since they verify the transmitted JWTs.

## Session and Cookie Settings

Sessions and cookies store data between HTTP requests, such as login information, preferences, or user behavior tracking. Dotkernel configures cookies in `config/autoload/session.global.php`, which contains parameters that must be revised and adapted:

- `session_config.cookie_httponly`
- `session_config.cookie_samesite`
- `session_config.cookie_secure`

## JavaScript Dependencies

JavaScript has its own dependencies, usually installed via npm or yarn. The JavaScript ecosystem has recently been attacked by hackers targeting several widely used npm packages with billions of total uses. Dotkernel uses npm to handle JavaScript dependencies, monitors the news for security issues, and uses packages from reliable sources. `npm audit` should still be used regularly to check for vulnerabilities.

## Other Security Considerations

All components of Dotkernel Headless Platform have configuration files named `*.global.php`, `*.php.dist`, and `*.local.php`. Sensitive information must only go in `*.local.php` files, since they are ignored by the VCS by default. Development mode enables features like debug mode, cache clear, and error details, which should be hidden from production to avoid exposing sensitive data or code. The Laminas Continuous Integration GitHub Action is integral to Dotkernel API, running a matrix of static analysis, coding standards checks, and unit tests, most often triggered by commits.

## FAQ

**Q: What are the main facets of software security to consider?**
A: Software security spans many areas: authentication and access control, data protection, input validation and injection, web and API security, dependency and supply chain risks, configuration and deployment, network and infrastructure security, logging/monitoring and incident response, secure software development lifecycle, and human and organizational factors.

**Q: How does Dotkernel handle form input validation?**
A: Dotkernel API uses laminas/laminas-inputfilter to ensure a field is present and of the correct type. Dotkernel Admin additionally uses laminas/laminas-form, which provides form element objects, an InputFilter for each input (or custom validators), and methods for binding data to and from the form, integrating with laminas-escaper, laminas-validator, laminas-session, and laminas-filter.

**Q: How does Dotkernel API handle content negotiation?**
A: Content negotiation is handled via a middleware configured in the `config/autoload/content-negotiation.global.php` file. It uses the Content-Type and Accept HTTP request headers to negotiate with the client, returning application/json or application/hal+json data formats.

**Q: How is CORS handled and configured in Dotkernel API?**
A: CORS is handled by mezzio/mezzio-cors and configured in the `config/autoload/cors.local.php` file, validating calls using configuration items like origins, headers, max age, and credentials. When configuring the pipeline, the CorsMiddleware must be added before the RouteMiddleware.

**Q: What should be done with the demo credentials before going to production?**
A: Demo credentials are provided for convenience during installation testing, but it is important to update or remove these accounts in your production environment.

**Q: What are the security recommendations around OpenAPI documentation?**
A: You should not include sensitive information for your endpoints in the OpenAPI documentation, and you should not enable the documentation in a production environment.

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
