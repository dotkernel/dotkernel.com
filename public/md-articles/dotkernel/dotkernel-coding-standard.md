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

**Dotkernel** will be a "skeleton"of [**Zend Framework**](http://framework.zend.com/). Dotkernel borrowed the coding standard from Zend Framework: **[ZF Coding Standard](http://framework.zend.com/manual/en/coding-standard.php-file-formatting.html)** with some exceptions.

In what follows, we will make remarks only on those features that are slightly different in the coding standards of Dotkernel.

[**B.2. PHP File Formatting**](http://framework.zend.com/manual/en/coding-standard.php-file-formatting.html)

- [**B.2.2. Indentation**](http://framework.zend.com/manual/en/coding-standard.php-file-formatting.html#coding-standard.php-file-formatting.indentation)

[**B.3. Naming Conventions**](http://framework.zend.com/manual/en/coding-standard.naming-conventions.html)

Camel naming convention

- [**B.3.1. Classes**](http://framework.zend.com/manual/en/coding-standard.naming-conventions.html#coding-standard.naming-conventions.classes)
- **[B.3.2. Interfaces](http://framework.zend.com/manual/en/coding-standard.naming-conventions.html#coding-standard.naming-conventions.interfaces)**
- **[B.3.3. Filenames](http://framework.zend.com/manual/en/coding-standard.naming-conventions.html#coding-standard.naming-conventions.filenames)**

[**B.4.6. Control Statements**](http://framework.zend.com/manual/en/coding-standard.coding-style.html#coding-standard.coding-style.control-statements) every starting curly brace **}** after a statement starts on a new line, end it's closing curly brace **}** will be on a new line too. The start and end braces must be on the same column (for better indentation of the code) e.g:

```
if ($a != 2)
{
   $a = 2;
}
```

```
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
