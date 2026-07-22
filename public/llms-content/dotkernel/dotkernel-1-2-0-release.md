---
title: "DotKernel 1.2.0 release"
description: "Release notes for DotKernel 1.2.0, covering database naming convention changes, the new 'dots' submodule concept, new and updated library classes, and the use of prepared statements for all SQL queries."
author: "Teo"
date_published: "2010-07-05"
canonical_url: "https://www.dotkernel.com/dotkernel/dotkernel-1-2-0-release/"
category: "Dotkernel"
language: "en"
---

# DotKernel 1.2.0 release

## TL;DR

DotKernel 1.2.0 has been released, bringing changes since the previous 1.1.2 release.
The database tables were renamed and restructured to follow database naming conventions, and configuration for each "dots" (submodule) now lives in XML files instead of being hard-coded in PHP.
The release also adds new library classes (Dot_Geoip, Dot_Seo), updates existing ones (Dot_Curl, Dot_Session), and confirms that all SQL queries are written as prepared statements.

## Database Naming Conventions

On database, we changed the names and structure of tables to respect database naming convention.
See [http://www.dotkernel.com/dotkernel/dotkernel-database-naming-conventions-for-mysql/](http://www.dotkernel.com/dotkernel/dotkernel-database-naming-conventions-for-mysql/) for details.

## The "Dots" Concept

A new word came into our DotKernel discussions: dots.
We use this term when talking about a submodule and all its component files.
For example, "user" is a submodule of the frontend module.
Note that one dots can be part of multiple modules (for example, "user" dots belong to both the frontend and admin module).
For each dots, the configuration values have been added to XML files which are stored in the configs/dots folder.
In the previous versions, these values were hard-coded in the PHP files.

Another change made in the configs folder is resource.xml, which contains the configuration values for the controllers of each module.

To be easier to start an application from DotKernel, in the admin module, there are now the following dots: admin, user and system.

## Library Class Updates

New library classes have been implemented: Dot_Geoip and Dot_Seo, and some of the existing ones have been updated: Dot_Curl and Dot_Session (each module has its own session).

## SQL Prepared Statements

In DotKernel, all SQL queries are written as prepared statements.
We strongly encourage this practice: [http://www.dotkernel.com/php-development/protection-against-sql-injection-using-pdo-and-zend-framework/](http://www.dotkernel.com/php-development/protection-against-sql-injection-using-pdo-and-zend-framework/)

For more details, see [ChangeLog 1.2.0](http://www.dotkernel.com/changelog/1-2-0/).

## FAQ

**Q: What is a "dots" in DotKernel, a term introduced in this release?**
A: A term for a submodule and all its component files. For example, "user" is a dots of the frontend module, and one dots can belong to multiple modules, such as "user" belonging to both frontend and admin.

**Q: Where are dots configuration values stored, compared to earlier versions?**
A: They're stored in XML files inside the configs/dots folder. In previous versions, these values were hard-coded in the PHP files.

**Q: What dots does the admin module include by default?**
A: admin, user, and system, to make it easier to start an application from DotKernel.

**Q: What library classes were added or updated in 1.2.0?**
A: Dot_Geoip and Dot_Seo were newly implemented, while Dot_Curl and Dot_Session were updated, with each module now having its own session.

**Q: How are SQL queries written in DotKernel?**
A: All SQL queries are written as prepared statements, a practice the article strongly encourages.
