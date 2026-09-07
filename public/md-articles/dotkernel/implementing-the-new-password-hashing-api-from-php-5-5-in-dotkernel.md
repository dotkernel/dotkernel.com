---
title: "Implementing the new Password Hashing API from PHP 5.5 in Dotkernel"
description: "Dotkernel 1.8.0 refactors password handling to use PHP 5.5's Password Hashing API, using the Password Compat library for older PHP, with steps to upgrade existing systems."
author: "admin"
date_published: "2014-06-06"
canonical_url: "https://www.dotkernel.com/dotkernel/implementing-the-new-password-hashing-api-from-php-5-5-in-dotkernel/"
category: "Dotkernel"
language: "en"
---

# Implementing the new Password Hashing API from PHP 5.5 in Dotkernel

## TL;DR
To use the new [Password Hashing](http://www.php.net/manual/en/book.password.php) functions introduced in PHP 5.5 and unify password-related functions for both admin and users, Dotkernel's codebase was refactored in version 1.8.0 (starting from revision 799).
Because those functions require PHP 5.5+, the [Password Compat library](https://github.com/ircmaxell/password_compat) is used for compatibility, and the minimum PHP version to run Dotkernel was raised to 5.3.8.

In order to use the new [Password Hashing](http://www.php.net/manual/en/book.password.php) functions , introduced in PHP 5.5 , and unify all password related functions , used for both admin and users, we did a major refactor of Dotkernel codebase, in version 1.8.0 , starting from revision 799.

See more on that matter[here](http://www.brandonsavage.net/please-stop-hashing-passwords-yourself/)

Since those 4 new functions are available only since PHP 5.5 , we used the [Password Compatibility library.](https://github.com/ircmaxell/password_compat)

The minimum PHP version in order to run Dotkernel was raised to PHP 5.3.8 .

 

**How to apply this refactor to older Dotkernel systems**

```
ALTER TABLE `admin` CHANGE `password` `password` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
```

```
ALTER TABLE `user` CHANGE `password` `password` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
```

- Remove from application.ini the variable **settings.admin.salt = 5F6WQ9U3YT**
- apply the patch . [Download](http://www.dotkernel.com/download/?did=40)
- if you have trouble applying the patch, you can compare the files and see the log/diff in [websvn](http://websvn.dotkernel.net/comp.php?repname=Dotkernel&compare[]=/@796&compare[]=/@797)
- run the conversion script . You can find the details in the file Console/Controller.php , at line 47
- Hope that you will not break something :-)
- Admin passwords cannot be converted , so need to be recreated manually .

 

 

 

## FAQ

**Q: Why was Dotkernel refactored to use the new PHP 5.5 Password Hashing API?**
A: To use the new Password Hashing functions introduced in PHP 5.5 and unify all password related functions used for both admin and users, Dotkernel's codebase was refactored in version 1.8.0, starting from revision 799.

**Q: How can PHP versions older than 5.5 use these new hashing functions?**
A: Since the 4 new functions are only available since PHP 5.5, Dotkernel used the Password Compatibility library (ircmaxell/password_compat).

**Q: What is the minimum PHP version required after this change?**
A: The minimum PHP version required to run Dotkernel was raised to PHP 5.3.8.

**Q: What steps are needed to apply this refactor to an older Dotkernel system?**
A: Change the password column definition in the admin and user tables via ALTER TABLE, remove the settings.admin.salt entry from application.ini, apply the provided patch, and run the conversion script described in Console/Controller.php at line 47.

**Q: What happens to existing admin passwords during the upgrade?**
A: Admin passwords cannot be converted automatically, so they need to be recreated manually.
