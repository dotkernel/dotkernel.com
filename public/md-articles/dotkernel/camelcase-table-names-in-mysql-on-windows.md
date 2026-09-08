---
title: "camelCase Table Names in MySQL on Windows"
description: "How to fix MySQL on WAMP/XAMPP lowercasing camelCase table names by setting lower_case_table_names=2 in my.cnf."
author: "admin"
date_published: "2010-03-12"
canonical_url: "https://www.dotkernel.com/dotkernel/camelcase-table-names-in-mysql-on-windows/"
category: "Dotkernel"
language: "en"
---

# camelCase Table Names in MySQL on Windows

If you are using a WAMP stack, like WAMP or XAMPP, and try to create a table in camelCase (example: `adminLogin`), you will notice that camelCase is not working - the table name will be lowercase: `adminlogin`.

In order to fix this, add the following line to your `my.cnf` file:

```
lower_case_table_names=2
```

and restart MySQL.

## FAQ

**Q: What happens when you create a camelCase table name on WAMP or XAMPP?**
A: A table created with a camelCase name, for example adminLogin, ends up stored as all lowercase, e.g. adminlogin, instead.

**Q: How do you fix it?**
A: Add the line `lower_case_table_names=2` to your my.cnf file and restart MySQL.

## Resources

- [MySQL lower_case_table_names documentation](http://dev.mysql.com/doc/refman/4.1/en/server-system-variables.html#sysvar_lower_case_table_names)
