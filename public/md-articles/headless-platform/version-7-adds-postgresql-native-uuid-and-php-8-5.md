---
title: "Version 7 adds PostgreSQL, Native UUID and PHP 8.5"
description: "Overview of Dotkernel API and Admin's v7 release, which adds native UUID v7 support, PostgreSQL compatibility, PHP 8.5/8.4 support, and drops MySQL support."
author: "Florin Bidirean"
date_published: "2025-12-12"
canonical_url: "https://www.dotkernel.com/headless-platform/version-7-adds-postgresql-native-uuid-and-php-8-5/"
category: "Headless Platform"
language: "en"
---

# Version 7 adds PostgreSQL, Native UUID and PHP 8.5

## TL;DR

Dotkernel API v7 adds support for native UUID v7, PostgreSQL, PHP 8.5 (8.4 for Admin), database table prefixes, and improved database configuration, while replacing the `binary` data type for `id` columns with `uuid`.
It drops the Evolution pattern's Method Deprecation support and MySQL, since MySQL doesn't support the UUID data type.
UUIDs are generated with the ramsey/uuid package, previously `uuid`-named table columns are now called `id`, and PostgreSQL or MariaDB v10.7+ is required for UUID support.

The Dotkernel Headless Platform has seen new releases for both API and Admin.
The Admin codebase has received an overall facelift, as well as updates to retain compatibility with API v7.
The release for Dotkernel API v7 has several added features, as well as a few that are removed.

New features are:

- Support for `UUID` v7 using the native `UUID` data type.
- Support for PostgreSQL.
- Support for PHP 8.5 for API and 8.4 for Admin.
- Support for database table prefix (string prepended to the name of every table in a database).
- Improved database configuration.
- The replacement of the `binary` data type for `id` (index) columns, in favor of `uuid`.

And these are the features that have been removed:

- Evolution pattern: Support for Method Deprecation.
- Support for MySQL, primarily because it doesn't support UUID as a data type.

## Support for UUID v7

The most important change in v7 is the introduction of support for native UUID.
We use the package [ramsey/uuid](https://github.com/ramsey/uuid) to generate the uuid and then store it in the database.
In this way we have full control over the UUID version in use.
This solution means you don't depend on extensions or a particular version of the database.

> To ensure you have support for the `UUID` data type, you must use PostgreSQL or MariaDB v10.7 or later.

This also brings along a less-impactful change that still deserves mentioning: the table columns named `uuid` have been renamed to `id`.

## Database Configuration

Based on new scenarios in our own projects, we decided to clarify the instructions regarding multiple database connections.
With this update, these aspects should become more obvious:

- Which database connection is the default.
- How to switch to another database connection.

## FAQ

**Q: What new features does Dotkernel API v7 introduce?**
A: v7 adds support for UUID v7 using the native UUID data type, support for PostgreSQL, support for PHP 8.5 (API) and 8.4 (Admin), support for a database table prefix, improved database configuration, and replaces the binary data type for id (index) columns in favor of uuid.

**Q: What features were removed in v7?**
A: v7 removes the Evolution pattern's support for Method Deprecation, and drops support for MySQL, primarily because MySQL doesn't support UUID as a data type.

**Q: What package generates the UUIDs, and why?**
A: Dotkernel uses the ramsey/uuid package to generate the UUID before storing it in the database. This gives full control over the UUID version in use, so the application doesn't depend on extensions or a particular database version.

**Q: Which databases support the UUID data type required by v7?**
A: You must use PostgreSQL or MariaDB v10.7 or later to have support for the UUID data type.

**Q: What else changed alongside the move to native UUID?**
A: Table columns previously named `uuid` have been renamed to `id`. The database configuration was also clarified so it's more obvious which connection is the default and how to switch to another database connection.
