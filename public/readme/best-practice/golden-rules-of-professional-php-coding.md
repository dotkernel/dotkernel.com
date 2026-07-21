---
title: "Golden Rules of Professional PHP Coding"
description: "A short list of practical rules for professional PHP development: error reporting settings, fixing warnings, marking hacks, single-responsibility functions, version control, and IDE usage."
author: "admin"
date_published: "2011-06-12"
canonical_url: "https://new.dotkernel.com/best-practice/golden-rules-of-professional-php-coding/"
category: "Best Practice"
language: "en"
---

# Golden Rules of Professional PHP Coding

## The Rules

1. Always use, in development and in staging, the highest error reporting level, and display_errors ON:
   ```php
   error_reporting(-1);
   ini_set('display_errors', 1);
   ```
2. Fix every warning or notice that occurs.
3. Check regularly the server's error_log for notices/warnings.
4. Identify any temporary hack with a special mark, for example:
   ```php
   #@TODO masterpiece by @smartguy, to quick fix the division by zero
   ```
5. Each function must do a single task. If it logs in the user and records the login in a stats table, create a separate function for the "record the login" part - maybe even a distinct class for stats.
6. Use a version control system. SVN is NOT dead.
7. Use an IDE, such as Aptana 2, Aptana 3, Eclipse, or Zend Studio.
8. Know your IDE: code snippets, code assist, integration with Zend Framework, SVN integration, bug tracker integration, and so on.

## FAQ

**Q: What error reporting settings should be used in development and staging?**
A: Always use the highest error reporting level and turn display_errors ON, for example with `error_reporting(-1);` and `ini_set('display_errors', 1);`.

**Q: What should you do about warnings and notices?**
A: Fix every warning or notice that occurs, and regularly check the server's error_log for notices and warnings.

**Q: How should temporary hacks or quick fixes be marked in code?**
A: Identify any temporary hack with a special mark, such as a `#@TODO` comment noting who added it and why.

**Q: What is the rule about what a function should do?**
A: Each function must do a single task. For example, if you're logging in a user and also recording that login in a stats table, create a separate function (or even a distinct class) for the stats recording, rather than combining both tasks in one function.

**Q: What tools does the article recommend for professional PHP development?**
A: It recommends using a version control system (noting that SVN is not dead) and using an IDE such as Aptana 2, Aptana 3, Eclipse, or Zend Studio, and knowing your IDE's code snippets, code assist, Zend Framework integration, SVN integration, and bug tracker integration.

## Resources

- [Integrated development environment (Wikipedia)](http://en.wikipedia.org/wiki/Integrated_development_environment)
- [Aptana 2 download](http://www.aptana.com/products/studio2/download)
- [Aptana 3 download](http://www.aptana.com/products/studio3/download)
- [Zend Studio](http://www.zend.com/en/products/studio/)
