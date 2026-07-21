---
title: "Protection against SQL Injection using PDO and Zend Framework - part 2"
description: "A closer look at Zend_Db's query, fetchAll, insert, update, and delete methods, and where SQL injection can still slip through even with prepared statements."
author: "Teo"
date_published: "2010-06-18"
canonical_url: "https://new.dotkernel.com/php-development/protection-against-sql-injection-using-pdo-and-zend-framework-part-2/"
category: "PHP Development"
language: "en"
---

# Protection against SQL Injection using PDO and Zend Framework - part 2

## TL;DR

Following up on the earlier SQL Injection article, this part digs into the specific methods of Zend_Db (and related classes Zend_Db_Statement, Zend_Db_Select, Zend_Db_Tables) to show exactly when their use of prepared statements does, and does not, protect against SQL injection - and offers a quick type-casting tip for WHERE clauses.

## Zend_Db and related classes

Zend_Db is the primary class used for accessing the database, but there is more: Zend_Db_Statement, Zend_Db_Select and Zend_Db_Tables.

## What you should know about their methods

| Method | Behavior |
| --- | --- |
| `query(mixed $sql, ...)` | Uses prepared statements internally, but SQL Injection is still possible if `$sql` is dynamically created. |
| `fetchAll(string\|Zend_Db_Select $sql, ...)` | All the fetch methods use prepared statements internally, but SQL Injection is still possible if `$sql` is dynamically created. |
| `insert(mixed $table, $bind)` | Uses prepared statements internally, so SQL Injection is not possible. |
| `update(mixed $table, $bind, ...)` | Uses prepared statements internally, but SQL Injection may be possible if `$where` is created dynamically. |
| `delete(mixed $table, ...)` | SQL Injection may be possible if `$where` is created dynamically. |

**Note:** even if you use prepared statements via Zend_Db methods, SQL Injection is still possible if the `WHERE` and `ORDER BY` clauses are wrongly written, so pay attention to them.

## A quick tip for WHERE clauses

A short tip: you can use type casting to avoid SQL Injection in a WHERE clause where possible.

```php
$sql = 'SELECT * FROM table WHERE id = ' . (int)$_POST;
```

## FAQ

**Q: What is the focus of this article?**
A: Following the earlier article about SQL Injection, this article makes a stronger argument for using Zend Framework to handle database access. Zend_Db is the primary class used for accessing the database, but there is more to it: Zend_Db_Statement, Zend_Db_Select, and Zend_Db_Tables.

**Q: Does Zend_Db's query() method protect against SQL injection?**
A: query() uses prepared statements internally, but SQL Injection is still possible if the $sql parameter passed to it is dynamically created.

**Q: What about fetchAll() and the other fetch methods?**
A: All of the fetch methods use prepared statements internally, but SQL Injection is still possible if the $sql is dynamically created.

**Q: Is insert() safe from SQL injection?**
A: Yes. insert() uses prepared statements internally, so SQL Injection is not possible with it.

**Q: What about update() and delete()?**
A: update() uses prepared statements internally, but SQL Injection may be possible if the $where clause is created dynamically. Likewise, with delete(), SQL Injection may be possible if $where is created dynamically.

**Q: Even with prepared statements, when can SQL injection still happen, and what tip helps avoid it in WHERE clauses?**
A: Even when using prepared statements via Zend_Db methods, SQL Injection is still possible if the WHERE and ORDER BY clauses are wrongly written, so these deserve special attention. A short tip mentioned is to use type casting to avoid SQL Injection in a WHERE clause where possible, for example: `$sql = 'SELECT * FROM table WHERE id = ' . (int)$_POST;`

## Resources

- [Protection against SQL Injection using PDO and Zend Framework (part 1)](http://www.dotkernel.com/php-development/protection-against-sql-injection-using-pdo-and-zend-framework/)
- [Secure Programming with the Zend Framework (Stefan Esser slides)](http://www.suspekt.org/downloads/DPC_Secure_Programming_With_The_Zend_Framework.pdf)
- [Type casting in PHP - what's the point?](http://www.dustinweber.com/main-page/type-casting-in-php-whats-the-point/)
