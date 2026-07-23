---
title: "Remote connections to MySQL server on Plesk based servers"
description: "How to fix remote MySQL connection failures on Plesk-based Linux servers caused by the default old_passwords flag, by creating a user with a modern password hash."
author: "admin"
date_published: "2011-10-18"
canonical_url: "https://www.dotkernel.com/php-development/remote-connections-to-mysql-server-on-plesk-based-servers/"
category: "PHP Development"
language: "en"
---

# Remote connections to MySQL server on Plesk based servers

## TL;DR

Plesk-based Linux servers default to `old_passwords=1`, which forces MySQL to use the old, pre-4.1 password storage style even on MySQL 5.5+, breaking remote PDO connections.
The fix is to create a new database user, grant it privileges, disable old_passwords, and reset its password so a newer, longer password hash is stored in mysql.user.

By default, MySql servers on Linux machines where Plesk is installed, have the old_passwords=1 or ON flag.
That mean even if you have MySQL 5.5+ version, it still use old style of password storage, pre-mysql 4.1+.
If you try to connect remote, from your local development machine, you will get an ugly error, like:

```
Fatal error: Uncaught exception 'PDOException' with message 'SQLSTATE  mysqlnd
cannot connect  to MySQL 4.1+ using the old insecure authentication. Please use an administration
tool to reset your password with the command SET PASSWORD = PASSWORD('your_existing_password').
This will store a new, and more secure, hash value in mysql.user.
If this user is used in other scripts executed by PHP 5.2 or earlier you might need to remove
the old-passwords flag from your my.cnf file' in
```

How to fix that:

1. Create a new database user, someuser for instance directly in command line.
2. Grant priviledges to that user to the database you want to connect to.
3. Now run the below SQL statements:

```sql
SET old_passwords = 0;
UPDATE mysql.user SET Password = PASSWORD('somepassword') WHERE User = 'someuser' limit 1;
SELECT LENGTH(Password) FROM mysql.user WHERE User = 'someuser';
FLUSH PRIVILEGES;
```

Now you can remotely connect to that server, using the user: someuser and password: somepassword.

NOTE: if you browse the table mysql.user, you will note that the password field contain many more characters for the user someuser compared to the others.

## FAQ

**Q: Why can't I connect remotely to MySQL on a Plesk-based server?**
A: By default, MySQL servers on Linux machines with Plesk installed have the old_passwords flag set to 1 (ON). Even on MySQL 5.5+, this means the old, pre-4.1 password storage style is still used, which causes a PDOException error when connecting remotely.

**Q: What error message indicates this old-passwords problem?**
A: You get a fatal error mentioning "cannot connect to MySQL 4.1+ using the old insecure authentication," suggesting you reset the password using SET PASSWORD = PASSWORD('your_existing_password') to store a newer, more secure hash.

**Q: How do you fix the old-passwords issue?**
A: Create a new database user (e.g. someuser) from the command line, grant it privileges on the target database, then run: SET old_passwords = 0; UPDATE mysql.user SET Password = PASSWORD('somepassword') WHERE User = 'someuser' limit 1; SELECT LENGTH(Password) FROM mysql.user WHERE User = 'someuser'; FLUSH PRIVILEGES;. You can then connect remotely using that user and password.

**Q: How can you tell the fix worked by looking at the mysql.user table?**
A: If you browse the mysql.user table, you'll notice the password field for the fixed user (someuser) contains many more characters than the password fields for the other, unfixed users.
