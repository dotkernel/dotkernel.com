---
title: "Shared Core Submodule in Dotkernel Headless Platform"
description: "An explanation of the Core submodule pattern in Dotkernel's Headless Platform, and step-by-step instructions for extracting it into a shared Git submodule."
author: "Florin Bidirean"
date_published: "2025-07-04"
canonical_url: "https://www.dotkernel.com/headless-platform/shared-core-submodule-in-dotkernel-headless-platform/"
category: "Headless Platform"
language: "en"
---

# Shared Core Submodule in Dotkernel Headless Platform

## TL;DR

Dotkernel's Headless Platform is composed of Dotkernel API, Admin, and Queue, and can share a common Core submodule that holds the database entities and services used consistently across all of them.
The article walks through creating the Core submodule with `git submodule add`, committing changes from within the Core folder, and initializing/updating it with `git submodule init` and `git submodule update`.
Sharing a Core module brings design flexibility, scalability, and easier bugfixes and onboarding as the platform grows.

Dotkernel has implemented a Headless solution made up of these applications:

- [Dotkernel API](https://github.com/dotkernel/api) - a REST API, the root of the platform.
- [Dotkernel Admin](https://github.com/dotkernel/admin) - (optional) complementary backend management.
- [Dotkernel Queue](https://github.com/dotkernel/queue) - (optional) queue management microservice.

This is effectively a Headless CMS architecture where the Dotkernel Admin manages the data via the database.
The same data is independently exposed by Dotkernel API and used by Dotkernel Queue.

## What Is the Core Submodule?

The Core submodule is a common codebase set up to be used by the applications you added to your project.
It can just as well work for any project setup, e.g. two APIs, one Admin, 3 Frontends.
By having a common module in your Dotkernel applications, you ensure that each of them uses entities and services in the same way.
Thus, rather than making e.g. an update in each application's services, you only update the relevant service in the Core once and sync it in each application.

> The golden rule for the Core codebase is that it is the only place which manages the database entities.
> As much as possible, all Doctrine entities must reside in Core.
> The current location of the Core submodule is `src/Core`.

## How Do I Create a Core Submodule?

The full steps for creating a submodule are described in [Git Tools - Submodules](https://git-scm.com/book/en/v2/Git-Tools-Submodules).

> There is already a Core module in some of the Dotkernel applications, but it works like any other module (App, Page or User). The Core modules are designed to be a starting point for the module's transformation into a Git submodule.

First create a new Git repository that will contain the Core code.
To create the submodule in an application, you need to have Git create the `.gitmodules` file in the root of the main repository by running the command below.
Use the url from the new repository you just created instead of `<url>`:

```bash
git submodule add <url>
```

> You can have multiple submodules, but for this article we will only create the Core submodule.

The `.gitmodules` file maps the submodule and its corresponding local directory within the main project (e.g. src/Core).
This allows Git to manage the submodule correctly, from cloning, to updating, to tracking its changes.

> None of the Dotkernel applications have the `.gitmodules` file out of the box. Only after isolating the Core into a Git submodule and pushing it to a separate Git repository does it become available to be included into any Dotkernel application.

From now on, any changes to the Core submodule must be committed from within the Core folder, like for any other Git repository, using these commands (simplified version, provided as an example):

```bash
cd <path/to/submodule>
git add .
git commit -m "comment"
git push
```

Whenever you clone the project, you simply need to init and update the submodule with these commands:

```bash
git submodule init
git submodule update
```

> Do not forget to delete the existing Core module before adding the submodule to other applications.

## How Do I Use the Core Submodule?

Once the shared Core submodule is separated and imported into each application, your platform will look something like this:

- API + Core
- Admin + Core
- Queue + Core

> Each box in the image is a different Git repository.

Whenever work begins on a new feature or update, the devs should normally have the most recent Core in their development environment.
So in total you have four code bases which will be kept in four separate repositories.
The Dotkernel applications include various entities to get you started quickly.
This is not a complete list, but it should help you understand what each application is aimed toward:

- The admin has admins, admin logins and settings entities.
- The api has both users and admins, as well as authentication entities.

There are already shared entities which are identical, so the best place for them is within the Core submodule.
Whenever you create new shared code, you should add it in the Core submodule and make sure to keep it updated in all your applications.

> This does not mean that all new code should be in Core, as there are plenty of instances when certain functionality is designed to only be used by one application.

## Why the Core Submodule Is Effective

This design pattern ensures:

- Design flexibility.
- Scalability based on future requirements.
- Consistent, enterprise-level growth, while also being suited for smaller applications.
- The ability to split the work to multiple developers.
- Easier bugfixes and onboarding.

As your platform expands, each new application connects to the Dotkernel Headless Platform via the central API which services everything the other applications require.
This ensures consistency throughout your platform, while allowing any number of outside connections as requirements arise.

## FAQ

**Q: What is the Core submodule in Dotkernel's Headless Platform?**
A: The Core submodule is a common codebase shared by the applications in your project (for example API, Admin and Queue). It ensures each application uses entities and services in the same way, so rather than updating each application separately, you update the relevant service in Core once and sync it across applications.

**Q: What is the golden rule for the Core codebase?**
A: The Core codebase is the only place which manages the database entities. As much as possible, all Doctrine entities must reside in Core, whose current location is `src/Core`.

**Q: How do I create a Core submodule?**
A: First create a new Git repository to hold the Core code. Then, in the main application repository, run `git submodule add <url>` using that new repository's URL. This generates the `.gitmodules` file, which maps the submodule to a local directory such as `src/Core`. From then on, changes to Core must be committed from within the Core folder like any other Git repository.

**Q: How do I get the Core submodule when cloning a project?**
A: Run `git submodule init` followed by `git submodule update`. Remember to delete the existing Core module before adding the submodule to other applications.

**Q: Which entities live in Admin and API versus the shared Core?**
A: The admin application includes admins, admin logins and settings entities, while the API includes both users and admins, as well as authentication entities. Entities that are identical across applications should instead be kept in the shared Core submodule and synced whenever they change.

## Resources

- [Dotkernel Git Repositories](https://github.com/dotkernel)
- [Core in Dotkernel Headless Platform](https://docs.dotkernel.org/headless-documentation/v1/core/introduction/)
