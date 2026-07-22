---
title: "DotMaker - Generate common code in Dotkernel"
description: "Overview of dotkernel/dot-maker, the CLI tool that generates project files and directories matching the Dotkernel file structure, and how to use it to scaffold modules and individual code items."
author: "Florin Bidirean"
date_published: "2025-09-12"
canonical_url: "https://www.dotkernel.com/headless-platform/dotmaker-generate-common-code-in-dotkernel/"
category: "Headless Platform"
language: "en"
---

# DotMaker - Generate common code in Dotkernel

## TL;DR

DotMaker (`dotkernel/dot-maker`) programmatically generates project files and directories matching the Dotkernel file structure inspired by Mezzio. It boosts productivity and enforces consistency and standardization compared to creating modules and files by hand, and it can tell the difference between Dotkernel applications (Api, Admin, Frontend) to create the files each one requires.

## Why use dot-maker?

Creating a new module manually requires a relatively large number of files and folders. Doing this by hand leaves too many opportunities for mistakes that generate errors and delay development. DotMaker was built to handle this heavy lifting.

DotMaker is inspired by the [Symfony Maker Bundle](https://github.com/symfony/maker-bundle) in terms of functionality. Mezzio has its own [CLI Tooling](https://docs.mezzio.dev/mezzio/v3/reference/cli-tooling/) that performs similar actions, but DotMaker uses a more opinionated naming pattern and is designed specifically for Dotkernel applications.

## The file structure for a module

Example file structure for a `Book` module and the related files it uses from the `Core` module:

```
.
└── src/
    ├── Book/
    │   └── src/
    │       ├── Collection/
    │       │   └── BookCollection.php
    │       ├── Handler/
    │       │   ├── GetBookCollectionHandler.php
    │       │   ├── GetBookResourceHandler.php
    │       │   └── PostBookResourceHandler.php
    │       ├── InputFilter/
    │       │   ├── Input/
    │       │   │   ├── AuthorInput.php
    │       │   │   ├── NameInput.php
    │       │   │   └── ReleaseDateInput.php
    │       │   └── CreateBookInputFilter.php
    │       ├── Service/
    │       │   ├── BookService.php
    │       │   └── BookServiceInterface.php
    │       ├── ConfigProvider.php
    │       └── RoutesDelegator.php
    └── Core/
        └── src/
            └── Book/
                └── src/
                    ├── Entity/
                    │   └── Book.php
                    ├── Repository/
                    │   └── BookRepository.php
                    └── ConfigProvider.php
```

This example is for a Dotkernel API project, so it has a Collection but is missing the Form and template files that Dotkernel Admin requires. DotMaker can tell the difference between Dotkernel applications and will create the required files for each.

## How dot-maker works

To create a new module:

1. Run `./vendor/bin/dot-maker module`.
2. Follow the prompted list of steps that create each relevant item, from entities and repositories to handlers and services.
3. DotMaker automatically creates the module's `ConfigProvider.php` and tells you how to register it in `config/config.php` and `composer.json`.
4. DotMaker generates documentation in an `OpenApi.php` file and tells you what command to run to generate migrations for the new module.
5. Once the files are created, manually configure the authorization for the new module based on the platform's requirements.

Authorization is configured in different files between Dotkernel applications:

| Application | Authorization file |
|---|---|
| Dotkernel Api | `config/autoload/authorization.global.php` ([docs](https://docs.dotkernel.org/api-documentation/v6/core-features/authorization/)) |
| Dotkernel Admin | `config/autoload/authorization-guards.global.php` ([docs](https://docs.dotkernel.org/admin-documentation/v6/how-to/authorization/)) |
| Dotkernel Frontend | `config/autoload/authorization-guards.global.php` ([docs](https://docs.dotkernel.org/frontend-documentation/v5/how-to/authorization/)) |

### Generating individual items

DotMaker can also create individual items related to a module, like an entity, a form, a new command, or a middleware, instead of a whole module. These commands work only after the basic command is added to `composer.json`.

| Command | Effect |
|---|---|
| `composer make collection` / `./vendor/bin/dot-maker collection` | Creates a Collection for an existing module |
| `composer make command` / `./vendor/bin/dot-maker command` | Creates a Command for an existing module |
| `composer make entity` / `./vendor/bin/dot-maker entity` | Creates an Entity and its associated Repository (skips the Repository if it already exists) |
| `composer make form` / `./vendor/bin/dot-maker form` | Creates a Form for an existing module |
| `composer make handler` / `./vendor/bin/dot-maker handler` | Creates a Handler for an existing module |
| `composer make input` / `./vendor/bin/dot-maker input` | Creates an Input for an existing module |
| `composer make input-filter` / `./vendor/bin/dot-maker input-filter` | Creates an InputFilter for an existing module (prompts for create, edit, or replace type) |
| `composer make middleware` / `./vendor/bin/dot-maker middleware` | Creates a Middleware for an existing module |
| `composer make module` / `./vendor/bin/dot-maker module` | Creates a Module and its predefined files (entity, repository, service, handler, etc.) |
| `composer make repository` / `./vendor/bin/dot-maker repository` | Creates a Repository and its Entity (skips the Entity if it already exists) |
| `composer make service` / `./vendor/bin/dot-maker service` | Creates a Service and its associated ServiceInterface (skips the ServiceInterface if it already exists) |
| `composer make service-interface` / `./vendor/bin/dot-maker service-interface` | Creates a ServiceInterface and its associated Service (skips the Service if it already exists) |

DotMaker won't do all the work, but after running through the maker process you have a solid foundation ready to accept custom code.

## FAQ

**Q: What does the DotMaker library do?**
A: DotMaker (dotkernel/dot-maker) programmatically generates project files and directories that match the Dotkernel file structure inspired by Mezzio, giving a productivity boost and promoting consistency and standardization compared to creating everything manually.

**Q: What inspired DotMaker and how is it different from Mezzio's own CLI tooling?**
A: DotMaker is inspired by the Symfony Maker Bundle in terms of functionality. Mezzio has its own CLI Tooling that performs similar actions, but DotMaker uses a more opinionated naming pattern and is designed specifically for Dotkernel applications.

**Q: How do I create a new module with DotMaker?**
A: Run ./vendor/bin/dot-maker module and follow the prompts that walk you through creating each relevant item, from entities and repositories to handlers and services. DotMaker also automatically creates the module's ConfigProvider.php, tells you how to register it in config/config.php and composer.json, generates documentation in an OpenApi.php file, and tells you what command to run to generate migrations.

**Q: Does DotMaker configure authorization for the new module?**
A: No, authorization must be configured manually after the files are created, and the location differs between Dotkernel applications: Dotkernel Api uses config/autoload/authorization.global.php, while Dotkernel Admin and Dotkernel Frontend use config/autoload/authorization-guards.global.php.

**Q: Can DotMaker generate individual items instead of a whole module?**
A: Yes. DotMaker provides individual commands to create items such as a collection, command, entity, form, handler, input, input-filter, middleware, repository, service, or service-interface for an existing module. For example, ./vendor/bin/dot-maker entity creates an Entity and its associated Repository, skipping the Repository if it already exists.

**Q: Do the composer make shortcuts work right away?**
A: No, commands like composer make entity only work after the basic command has been added to composer.json; the equivalent ./vendor/bin/dot-maker form is available regardless.

## Resources

- [DotMaker full documentation](https://docs.dotkernel.org/dot-maker/v1/overview/)
- [Implementing a book module in Dotkernel API using DotMaker](https://docs.dotkernel.org/api-documentation/v6/tutorials/create-book-module-via-dot-maker/)
- [API file structure](https://docs.dotkernel.org/api-documentation/v6/introduction/file-structure/)
- [Symfony Maker Bundle Documentation](https://symfony.com/bundles/SymfonyMakerBundle/current/index.html)
