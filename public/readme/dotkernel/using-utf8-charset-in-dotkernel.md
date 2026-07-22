---
title: "Using UTF8 charset in DotKernel"
description: "How to enable UTF8 encoding in a DotKernel-based system, covering both database collation and the application.ini charset setting."
author: "admin"
date_published: "2012-04-07"
canonical_url: "https://www.dotkernel.com/dotkernel/using-utf8-charset-in-dotkernel/"
category: "Dotkernel"
language: "en"
---

# Using UTF8 charset in DotKernel

## TL;DR

To use UTF8 encoding in a DotKernel-based system, changes are needed in both the database structure and the application.ini file. These changes were committed into the DotKernel 1.6.0 dev codebase.

## Database

Set a proper collation for all tables and columns, either **utf8_general_ci** or **utf8_bin**.

## application.ini

Add the following line to your application.ini file, in the database area:

```ini
database.params.charset = utf8
```

## Differences between utf8_general_ci and utf8_bin

| Collation | Behavior |
|---|---|
| utf8_bin | Compares strings by the binary value of each character in the string |
| utf8_general_ci | Compares strings using general language rules and case-insensitive comparisons |

For example, the following evaluates as true with utf8_general_ci collation, but **not** with utf8_bin collation:

- Ä = A
- Ö = O
- Ü = U

These differences only happen at the **MySQL** level (for instance, in queries using the LIKE operator) and **not** at the **PHP** level (for instance, in `str_replace()` calls).

## FAQ

**Q: What needs to change to use UTF8 encoding in a DotKernel-based system?**
A: Both the database structure and the application.ini file need changes: all tables and columns must have a proper UTF8 collation (utf8_general_ci or utf8_bin), and application.ini must load the utf8 charset.

**Q: What line should I add to application.ini to enable UTF8?**
A: Add the line `database.params.charset = utf8` to your application.ini file.

**Q: What is the difference between utf8_general_ci and utf8_bin collation?**
A: utf8_bin compares strings by the binary value of each character, while utf8_general_ci compares strings using general language rules with case-insensitive comparisons. For example, Ä = A, Ö = O, and Ü = U evaluate as true under utf8_general_ci but not under utf8_bin.

**Q: Does the collation choice affect PHP string functions too?**
A: No. These collation differences only happen at the MySQL level, for instance in queries using the LIKE operator, and not at the PHP level, for instance in str_replace() calls.
