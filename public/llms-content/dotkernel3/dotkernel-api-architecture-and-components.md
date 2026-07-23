---
title: "Dotkernel API: architecture and components"
description: "An overview of Dotkernel API's architecture and core components, covering Doctrine ORM, documentation, HAL, CORS, OAuth 2.0, email, configuration, routing, commands, the file locker, PSR standards, and testing."
author: "kakapiciu"
date_published: "2023-03-29"
canonical_url: "https://www.dotkernel.com/dotkernel3/dotkernel-api-architecture-and-components/"
category: "Dotkernel 3"
language: "en"
---

# Dotkernel API: architecture and components

## TL;DR

Dotkernel API is built on the Mezzio microframework and Laminas components, based on Enrico Zimuel's Zend Expressive API skeleton and implementing PSR-3, PSR-4, PSR-7, PSR-11, and PSR-15.
Its core components include Doctrine ORM for persistence, mezzio-hal for API payloads, mezzio-cors for CORS handling, and mezzio-authentication-oauth2 for OAuth 2.0 authentication, alongside Postman-based documentation, configurable routing and commands, a file locker system, and a factory-made test suite.

> This article refers to Dotkernel API v5. Checkout out the new additions for [Dotkernel API v6](https://www.dotkernel.com/headless-platform/dotkernel-api-v6-the-root-of-dotkernel-headless-platform/) to stay up-to-date.

Based on Enrico Zimuel's [Zend Expressive API - Skeleton example](https://github.com/ezimuel/zend-expressive-api), Dotkernel API runs on [Mezzio](https://github.com/mezzio) microframework and [Laminas](https://github.com/laminas) components and implements standards like PSR-3, PSR-4, PSR-7, PSR-11 and PSR-15.
Here is a list of the core components:

- Mezzio Microframework ([mezzio/mezzio](https://github.com/mezzio/mezzio))
- Error Handler ([dotkernel/dot-errorhandler](https://packagist.org/packages/dotkernel/dot-errorhandler))
- Problem Details ([mezzio/mezzio-problem-details](https://docs.mezzio.dev/mezzio-problem-details/))
- CORS ([mezzio/mezzio-cors](https://docs.mezzio.dev/mezzio-cors/))
- Routing ([mezzio/mezzio-fastroute](https://github.com/mezzio/mezzio-fastroute))
- Authentication ([mezzio/mezzio-authentication](https://docs.mezzio.dev/mezzio-authentication/))
- Authorization ([mezzio/mezzio-authorization](https://docs.mezzio.dev/mezzio-authorization/))
- Config Aggregator ([laminas/laminas-config-aggregator](https://github.com/laminas/laminas-config-aggregator))
- Container ([roave/psr-container-doctrine](https://github.com/Roave/psr-container-doctrine))
- Annotations ([dotkernel/dot-annotated-services](https://packagist.org/packages/dotkernel/dot-annotated-services))
- Input Filter ([laminas/laminas-inputfilter](https://github.com/laminas/laminas-inputfilter))
- Doctrine 2 ORM ([doctrine/orm](https://github.com/doctrine/orm))
- Hydrator ([laminas/laminas-hydrator](https://github.com/laminas/laminas-hydrator))
- Paginator ([laminas/laminas-paginator](https://github.com/laminas/laminas-paginator))
- HAL ([mezzio/mezzio-hal](https://docs.mezzio.dev/mezzio-hal/))
- CLI ([dotkernel/dot-cli](https://packagist.org/packages/dotkernel/dot-cli))
- TwigRenderer ([mezzio/mezzio-twigrenderer](https://github.com/mezzio/mezzio-twigrenderer))
- Fixtures ([dotkernel/dot-data-fixtures](https://packagist.org/packages/dotkernel/dot-data-fixtures))
- UUID ([ramsey/uuid-doctrine](https://github.com/mezzio/mezzio-twigrenderer))

## Doctrine 2 ORM

For the persistence in a relational database management system we chose [Doctrine ORM](https://www.doctrine-project.org/projects/doctrine-orm/en/2.14/index.html) (object-relational mapper).
The benefit of Doctrine for the programmer is the ability to focus on the object-oriented business logic and worry about persistence only as a secondary priority.

## Documentation

Our documentation is [Postman](https://www.postman.com/) based.
We use the following files in which we store information about every available endpoint ready to be tested:

- `documentation/DotKernel_API.postman_collection.json`
- `documentation/DotKernel_API.postman_environment.json`

## Hypertext Application Language

For our API payloads (a value object for describing the API resource, its relational links and any embedded/child resources related to it) we chose [mezzio-hal](https://docs.mezzio.dev/mezzio-hal/).

## CORS

By using `MezzioCorsMiddlewareCorsMiddleware`, the CORS preflight will be recognized and the middleware will start to detect the proper CORS configuration.
The Router is used to detect every allowed request method by executing a route match with all possible request methods.
Therefore, for every preflight request, there is at least one Router request.

## OAuth 2.0

OAuth 2.0 is an authorization framework that enables applications to obtain limited access to user accounts on your Dotkernel API.
We are using [mezzio/mezzio-authentication-oauth2](https://docs.mezzio.dev/mezzio-authentication-oauth2/) which provides OAuth 2.0 authentication for Mezzio and PSR-7/PSR-15 applications by using the [league/oauth2-server](https://oauth2.thephpleague.com/) package.

## Email

It is not unlikely for an API to send emails depending on the use case.
Here is another area where Dotkernel API shines.
Using `DotMailServiceMailService` provided by [dotkernel/dot-mail](https://packagist.org/packages/dotkernel/dot-mail) you can easily send custom email templates.

## Configuration

From authorization at request route level to API keys for your application, you can find every configuration variable in the config directory.
Registering a new module can be done by including its `ConfigProvider.php` in `config.php`.
Brand new middlewares should go into `pipeline.php`.
Here you can edit the order in which they run and find more info about the currently included ones.
You can further customize your API within the autoload directory where each configuration category has its own file.

## Routing

Each module has a `RoutesDelegator.php` file for managing existing routes inside that specific module.
It also allows a quick way of adding new routes by providing the route path, Middlewares that the route will use and the route name.
You can allocate permissions per route name in order to restrict access for a user role to a specific route in `config/autoload/authorization.global.php`.

## Commands

For registering new commands first make sure your command class extends `SymfonyComponentConsoleCommandCommand`.
Then you can enable it by registering it in `config/autoload/cli.global.php`.

## File Locker

Here you will also find our brand new file locker configuration so you can easily turn it on or off (by default: `'enabled' => true`).
Note: The File Locker System will create a `command-{command-default-name}.lock` file which will not let another instance of the same command run until the previous one has finished.

## PSR Standards

- [PSR-3: Logger Interface](https://www.php-fig.org/psr/psr-3/) - the application uses `LoggerInterface` for error logging
- [PSR-4: Autoloader](https://www.php-fig.org/psr/psr-4/) - the application locates classes using an autoloader
- [PSR-7: HTTP message interfaces](https://www.php-fig.org/psr/psr-7/) - the handlers return `ResponseInterface`
- [PSR-11: Container interface](https://www.php-fig.org/psr/psr-11/) - the application is container-based
- [PSR-15: HTTP Server Request Handlers](https://www.php-fig.org/psr/psr-15/) - the handlers implement `RequestHandlerInterface`

## Tests

One of the best ways to ensure the quality of your product is to create and run functional and unit tests.
You can find factory-made tests in the `tests/AppTest/` folder, and you can also register your own.

## FAQ

**Q: What is Dotkernel API based on?**
A: It's based on Enrico Zimuel's Zend Expressive API - Skeleton example, and runs on the Mezzio microframework and Laminas components, implementing PSR-3, PSR-4, PSR-7, PSR-11 and PSR-15.

**Q: What ORM does Dotkernel API use for persistence, and why?**
A: Doctrine ORM (object-relational mapper), chosen because it lets the programmer focus on the object-oriented business logic and treat persistence only as a secondary priority.

**Q: How is Dotkernel API documented?**
A: The documentation is Postman-based, using two files that store information about every available endpoint ready to be tested: documentation/DotKernel_API.postman_collection.json and documentation/DotKernel_API.postman_environment.json.

**Q: How does CORS handling work in Dotkernel API?**
A: MezzioCorsMiddlewareCorsMiddleware recognizes the CORS preflight request and detects the proper CORS configuration. The Router is used to detect every allowed request method by executing a route match with all possible request methods, so there's at least one Router request for every preflight request.

**Q: How do you register a new module or add new middleware?**
A: Registering a new module is done by including its ConfigProvider.php in config.php. Brand new middleware should go into pipeline.php, where you can also edit the order in which middleware runs.

**Q: What does the File Locker System do?**
A: It's enabled by default and creates a command-{command-default-name}.lock file, which prevents another instance of the same command from running until the previous one has finished.
