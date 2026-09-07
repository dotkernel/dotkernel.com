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
DotMaker (`dotkernel/dot-maker`) programmatically generates project files and directories matching the Dotkernel file structure inspired by Mezzio.
It boosts productivity and enforces consistency and standardization compared to creating modules and files by hand, and it can tell the difference between Dotkernel applications (Api, Admin, Frontend) to create the files each one requires.

The `dotkernel/dot-maker` library, also named DotMaker, is designed to **programmatically generate project files and directories** that match the Dotkernel file structure inspired by Mezzio.

Handling the file creation and configuration task manually invites mistakes that nobody has time for. Enter DotMaker which provides a big **productivity boost** compared to doing everything manually by promoting **consistenty and standardization** for these common tasks.

DotMaker is inspired by [Symfony Maker Bundle](https://github.com/symfony/maker-bundle) in terms of functionality. Mezzio has its own [CLI Tooling](https://docs.mezzio.dev/mezzio/v3/reference/cli-tooling/) that performs similar actions, while DotMaker has a more opinionated naming pattern and is designed specifically for Dotkernel applications.

https://www.youtube.com/watch?v=CPDilXP2kAc

## The File Structure for a Module

Below is an example of the file structure for a `Book` module and the related files it uses from the `Core` module.

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

> The example above is for a Dotkernel API project. This means that it has a Collection item, but is missing Form and template files that Dotkernel Admin requires. DotMaker can tell the difference between Dotkernel applications and will create the required files for each.

## Why Use dot-maker?

Even this simple example showcases the relatively **large number of files and folders** needed to make a new module work. A developer can perform the task **manually**, but there are too many places where things can be left out which **generate errors and delay development**. The **solution** is to use DotMaker which was built to help with the heavy lifting here.

## How dot-maker Works

Let's say you want to **create a new module** in your application:

- After you enter the command `./vendor/bin/dot-maker module`, you are **prompted to go through a list of steps** that **create each item** relevant to your module, from entities and repositories, to handlers and services.
- DotMaker automatically creates the `ConfigProvider.php` that is vital for **configuring your module** and tells you how to **register** it in `config/config.php` and `composer.json`.
- The library also generates **documentation** in `OpenApi.php` file and tells you what command to run to generate **migrations** for your new module.
- Once the files are created, you should **manually configure the authorization** for the new module based on your platform's requirements.

> Authorization is configured in different files between Dotkernel applications:
> 
> - Dotkernel Api uses `config/autoload/authorization.global.php`. You can check [Api authorization](https://docs.dotkernel.org/api-documentation/v6/core-features/authorization/) for details.
> - Dotkernel Admin and Dotkernel Frontend use `config/autoload/authorization-guards.global.php`. Here are the links for [Admin authorization](https://docs.dotkernel.org/admin-documentation/v6/how-to/authorization/) and [Frontend authorization](https://docs.dotkernel.org/frontend-documentation/v5/how-to/authorization/).

DotMaker can also create **individual items** related to a module, like an entity or a form, as well as a new command or middleware. Below if a list of the available commands. Most of them are aimed at creating an item within existing modules, excepting the one that handles module creation, of course.

> The commands below work only after the basic command is added to `composer.json`.

- `composer make collection` or `./vendor/bin/dot-maker collection` creates a Collection for an existing module.
- `composer make command` or `./vendor/bin/dot-maker command` creates a Command for an existing module.
- `composer make entity` or `./vendor/bin/dot-maker entity` creates an Entity and its associated Repository (e.g. `Entity\Phone.php` and `Repository\PhoneRepository.php`) for an existing module. If the Repository is already created, dot-maker will skip its creation and will return a message stating that it exists.
- `composer make form` or `./vendor/bin/dot-maker form` creates a Form for an existing module.
- `composer make handler` or `./vendor/bin/dot-maker handler` creates a Handler for an existing module.
- `composer make input` or `./vendor/bin/dot-maker input` creates an Input for an existing module.
- `composer make input-filter` or `./vendor/bin/dot-maker input-filter` creates an InputFilter for an existing module. You will be prompted to choose between 3 types of InputFilters: create, edit, replace.
- `composer make middleware` or `./vendor/bin/dot-maker middleware` creates a Middleware for an existing module.
- `composer make module` or `./vendor/bin/dot-maker module` creates a Module and its predefined files like entity, repository, service, handler etc.
- `composer make repository` or `./vendor/bin/dot-maker repository` creates a Repository and its Entity (e.g. `Entity\Phone.php` and `Repository\PhoneRepository.php`) for an existing module. If the Entity is already created, dot-maker will skip its creation and will return a message stating that it exists.
- `composer make service` or `./vendor/bin/dot-maker service` creates a Service and its associated ServiceInterface (e.g. `Service\PhoneService.php` and `Service\PhoneServiceInterface.php`) for an existing module. If the ServiceInterface is already created, dot-maker will skip its creation and will return a message stating that it exists.
- `composer make service-interface` or `./vendor/bin/dot-maker service-interface` creates a ServiceInterface and its associated Service (e.g. `Service\PhoneServiceInterface.php` and `Service\PhoneService.php`) for an existing module. If the Service is already created, dot-maker will skip its creation and will return a message stating that it exists.

This tool won't do all the work for you, but after running through the maker process, you know you are **working on a solid foundation that can now accept your custom code**.

## Additional Resources

- [DotMaker full documentation](https://docs.dotkernel.org/dot-maker/v1/overview/)
- [Implementing a book module in Dotkernel API using DotMaker](https://docs.dotkernel.org/api-documentation/v6/tutorials/create-book-module-via-dot-maker/)
- [API file structure](https://docs.dotkernel.org/api-documentation/v6/introduction/file-structure/)
- [Symfony Maker Bundle Documentation](https://symfony.com/bundles/SymfonyMakerBundle/current/index.html)

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
