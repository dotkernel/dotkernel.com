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

In some situations, it may be neccesar to force MySQL server collation and character set to UTF8. As you can't control all scripts that are connecting to your database( for instance: mysql command line, or mysqldump)

For that , open the my.cnf file and add the below lines:

```
character_set_server=utf8
skip-character-set-client-handshake
```

If you are interested in better performance, add the below line:

```
collation_server=utf8_general_ci
```

If you are interested in better sorting order, add the below line instead:

```
collation_server=utf8_unicode_ci
```

## FAQ

**Q: Why force UTF8 at the MySQL server level instead of relying on each client?**
A: Because you can't control all the scripts that connect to your database (for instance the mysql command line or mysqldump), so forcing the server's collation and character set to UTF8 in my.cnf guarantees it regardless of the connecting client.

**Q: What two lines enable UTF8 for all connections in my.cnf?**
A: character_set_server=utf8 and skip-character-set-client-handshake.

**Q: What's the difference between the two suggested collation settings?**
A: collation_server=utf8_general_ci is recommended for better performance, while collation_server=utf8_unicode_ci is recommended instead if better sorting order matters more.
