---
title: "Forcing UTF8 connections and character set in MySQL"
description: "How to force the MySQL server's character set and collation to UTF8 via my.cnf, so every connecting script uses it regardless of client."
author: "admin"
date_published: "2012-05-09"
canonical_url: "https://www.dotkernel.com/dotkernel/forcing-utf8-connections-and-character-set-in-mysql/"
category: "Dotkernel"
language: "en"
---

# Forcing UTF8 connections and character set in MySQL

## TL;DR

In some situations it may be necessary to force the MySQL server's collation and character set to UTF8, since you can't control all the scripts connecting to your database (for instance the mysql command line or mysqldump).
This is done by editing `my.cnf`.

## Steps

1. Open the `my.cnf` file.
2. Add the following lines to force UTF8 for all connections:

```shell
character_set_server=utf8
skip-character-set-client-handshake
```

3. If you're interested in better performance, also add:

```shell
collation_server=utf8_general_ci
```

4. If you're interested in better sorting order instead, add this line rather than the one above:

```shell
collation_server=utf8_unicode_ci
```

## FAQ

**Q: Why force UTF8 at the MySQL server level instead of relying on each client?**
A: Because you can't control all the scripts that connect to your database (for instance the mysql command line or mysqldump), so forcing the server's collation and character set to UTF8 in my.cnf guarantees it regardless of the connecting client.

**Q: What two lines enable UTF8 for all connections in my.cnf?**
A: `character_set_server=utf8` and `skip-character-set-client-handshake`.

**Q: What's the difference between the two suggested collation settings?**
A: `collation_server=utf8_general_ci` is recommended for better performance, while `collation_server=utf8_unicode_ci` is recommended instead if better sorting order matters more.
