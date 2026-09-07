---
title: "Using UTF8 charset in Dotkernel"
description: "How to enable UTF8 encoding in a Dotkernel-based system, covering both database collation and the application.ini charset setting."
author: "admin"
date_published: "2012-04-07"
canonical_url: "https://www.dotkernel.com/dotkernel/using-utf8-charset-in-dotkernel/"
category: "Dotkernel"
language: "en"
---

# Using UTF8 charset in Dotkernel

## TL;DR
To use UTF8 encoding in a Dotkernel-based system, changes are needed in both the database structure and the application.ini file.
These changes were committed into the Dotkernel 1.6.0 dev codebase.

In order to use UTF8 encoding in your Dotkernel based system, is needed to make some changes in both database structure and in the application.ini file.

> Those changes are commited into **Dotkernel 1.6.0 dev** codebase, which will be released in next days.

## Database

set for all tables and columns proper collation , either **utf8_general_ci** or **utf8_bin**

## Application.ini

add the line

```
database.params.charset = utf8
```

to your application.ini file, in the **[production]** area.

## Differences between utf8_general_ci and utf8_bin

**utf8_bin**: compare strings by the binary value of each character in the string **utf8_general_ci**: compare strings using general language rules and using case-insensitive comparisons

For example, the following will evaluate at true with  utf8_general_ci collation, but **not** with the utf8_bin collation:

Ä = A Ö = O Ü = U

Those differences happens only on **MySQL** level ( for instance in queries using LIKE operator) and **not** at **PHP** level ( for instance, in str_replace() calls )

## FAQ

**Q: What needs to change to use UTF8 encoding in a Dotkernel-based system?**
A: Both the database structure and the application.ini file need changes: all tables and columns must have a proper UTF8 collation (utf8_general_ci or utf8_bin), and application.ini must load the utf8 charset.

**Q: What line should I add to application.ini to enable UTF8?**
A: Add the line database.params.charset = utf8 to your application.ini file.

**Q: What is the difference between utf8_general_ci and utf8_bin collation?**
A: utf8_bin compares strings by the binary value of each character, while utf8_general_ci compares strings using general language rules with case-insensitive comparisons. For example, Ä = A, Ö = O, and Ü = U evaluate as true under utf8_general_ci but not under utf8_bin.

**Q: Does the collation choice affect PHP string functions too?**
A: No. These collation differences only happen at the MySQL level, for instance in queries using the LIKE operator, and not at the PHP level, for instance in str_replace() calls.
