---
title: "Dotkernel Coding Standard"
description: "Dotkernel borrows the Zend Framework coding standard with a few exceptions, covering indentation, class/interface/file naming, and curly brace placement for control statements."
author: "admin"
date_published: "2008-03-28"
canonical_url: "https://www.dotkernel.com/dotkernel/dotkernel-coding-standard/"
category: "Dotkernel"
language: "en"
---

# Dotkernel Coding Standard

## TL;DR

Dotkernel is a "skeleton" of Zend Framework and borrows its coding standard from the ZF Coding Standard, with a small number of exceptions covering indentation, naming conventions, and brace placement.

## Indentation

Indentation is made with tabs, not spaces (per section B.2.2 of the Zend Framework Coding Standard).

## Naming conventions

Dotkernel uses camel naming conventions, with these Dotkernel-specific rules:

| Element | Convention | Example |
|---|---|---|
| Classes | Start with `Dot_` | `Dot_Templates` |
| Interfaces | End with the string "Interface" | `Dot_Db_Interface` |
| Filenames | Always use the `.php` extension, no fancy extensions | `.php`, not `.inc` |

## Control statements — brace placement

Every opening curly brace `{` starts on its own new line after the statement, and its matching closing brace `}` is also placed on its own new line, aligned in the same column as the opening brace, for better indentation of the code.

Example:

```php
if ($a != 2)
{
   $a = 2;
}
```

```php
if ($a != 2)
{
    $a = 2;
    if($a == 2)
    {
       $c = 3;
    }
}
```

## FAQ

**Q: What coding standard does Dotkernel follow?**
A: Dotkernel borrows its coding standard from the Zend Framework Coding Standard, with some exceptions described in this article.

**Q: Tabs or spaces for indentation?**
A: Dotkernel indents with tabs, not spaces.

**Q: How should classes, interfaces, and filenames be named?**
A: Classes start with the prefix Dot_ (e.g. Dot_Templates), interfaces end with the string "Interface" (e.g. Dot_Db_Interface), and all PHP files use the ".php" extension, with no fancy extensions like ".inc".

**Q: How should curly braces be placed for control statements?**
A: Every opening curly brace starts on its own new line after the statement, and its matching closing brace also goes on a new line, aligned in the same column as the opening brace, for better indentation of the code.

## Resources

- Zend Framework: http://framework.zend.com/
- ZF Coding Standard: http://framework.zend.com/manual/en/coding-standard.php-file-formatting.html
- ZF Coding Standard — Indentation: http://framework.zend.com/manual/en/coding-standard.php-file-formatting.html#coding-standard.php-file-formatting.indentation
- ZF Coding Standard — Naming Conventions: http://framework.zend.com/manual/en/coding-standard.naming-conventions.html
- ZF Coding Standard — Classes: http://framework.zend.com/manual/en/coding-standard.naming-conventions.html#coding-standard.naming-conventions.classes
- ZF Coding Standard — Interfaces: http://framework.zend.com/manual/en/coding-standard.naming-conventions.html#coding-standard.naming-conventions.interfaces
- ZF Coding Standard — Filenames: http://framework.zend.com/manual/en/coding-standard.naming-conventions.html#coding-standard.naming-conventions.filenames
- ZF Coding Standard — Control Statements: http://framework.zend.com/manual/en/coding-standard.coding-style.html#coding-standard.coding-style.control-statements
