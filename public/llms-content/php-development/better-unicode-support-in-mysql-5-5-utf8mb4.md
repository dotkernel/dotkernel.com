---
title: "Better Unicode Support in MySQL 5.5 UTF8MB4"
description: "How Dotkernel adopted MySQL 5.5's utf8mb4 character set for fuller Unicode support, and the config and column-length changes that switch requires."
author: "admin"
date_published: "2014-05-06"
canonical_url: "https://www.dotkernel.com/php-development/better-unicode-support-in-mysql-5-5-utf8mb4/"
category: "PHP Development"
language: "en"
---

# Better Unicode Support in MySQL 5.5 UTF8MB4

## TL;DR

MySQL 5.5 introduced the utf8mb4 character set for fuller Unicode support, and Dotkernel's sample dk.sql file was updated to use it.
Switching to utf8mb4 means VARCHAR(255) columns can hit MySQL's 767-byte max key length error, so VARCHAR(150) is used instead, and the connection charset must be updated in both the application config and my.cnf.

Beginning with version 5.5 of MySQL, utf8mb4 character set was introduced, in order to better support Unicode.
Further reading: [MySQL and Unicode](http://mzsanford.com/blog/mysql-and-unicode/), also directly related to [PHP](http://www.phptherightway.com/#php_and_utf8).

Sample dk.sql file, part of Dotkernel framework, was updated in [revision 793](http://websvn.dotkernel.net/filedetails.php?repname=Dotkernel&path=%2Ftrunk%2Fdot_kernel.sql&rev=793&peg=793).

Only one downside, is not possible to use anymore VARCHAR(255), as you will get the error:

```
#1071 - Specified key was too long; max key length is 767 bytes
```

Explanation [here](http://stackoverflow.com/questions/1814532/1071-specified-key-was-too-long-max-key-length-is-767-bytes); so we use instead VARCHAR(150).

Now we should change the connection charset.
In config/application.ini file, edit the below line:

```ini
database.params.charset = utf8mb4
```

Other changes that can be made in my.cnf file are all related to replacing the string utf8_* with utf8mb4_*.

```ini
character_set_server=utf8mb4
```

```ini
collation_server=utf8mb4_general_ci
```

```ini
collation_server=utf8mb4_unicode_ci
```

## FAQ

**Q: What is utf8mb4 and why was it introduced?**
A: utf8mb4 is a character set introduced in MySQL 5.5 to provide better support for Unicode.

**Q: What error do I get if I keep VARCHAR(255) columns after switching to utf8mb4?**
A: You get `#1071 - Specified key was too long; max key length is 767 bytes`, so VARCHAR(150) is used instead.

**Q: How do I change the connection charset for the application?**
A: Edit the config/application.ini file and set `database.params.charset = utf8mb4`.

**Q: What changes are needed in my.cnf?**
A: Replace the string utf8_* with utf8mb4_*, for example `character_set_server=utf8mb4` and `collation_server=utf8mb4_general_ci` or `collation_server=utf8mb4_unicode_ci`.

**Q: Was Dotkernel's own sample SQL updated for utf8mb4?**
A: Yes, the sample dk.sql file, part of the Dotkernel framework, was updated for utf8mb4 in revision 793.
