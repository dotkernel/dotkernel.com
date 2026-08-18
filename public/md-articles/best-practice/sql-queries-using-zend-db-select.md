---
title: "SQL queries using Zend_Db – SELECT"
description: "How to write SELECT queries with JOINs and WHERE IN clauses using Zend_Db, alongside their equivalent raw SQL."
author: "Teo"
date_published: "2010-06-15"
canonical_url: "https://www.dotkernel.com/best-practice/sql-queries-using-zend-db-select/"
category: "Best Practice"
language: "en"
---

# SQL queries using Zend_Db – SELECT

## TL;DR

Zend_Db and its related classes provide a simple SQL database interface for Zend Framework.
This article shows how classical SELECT queries with JOINs and WHERE IN clauses are translated into Zend_Db's select() style, and how to debug the generated query.

## Connecting to the database

```php
$db = Zend_Db::factory('Pdo_Mysql', $dbConnect);
```

## SELECT query - WHERE clause

The following two classical SQL queries are equivalent — the first is a simple comma join, the second uses INNER JOIN — but the result is the same:

```sql
SELECT a.id, a.name, b.order_id
FROM users AS a, orders AS b
WHERE a.id = b.user_id
AND a.id = {$userId}
```

```sql
SELECT `a`.`id`, `a`.`name`, `b`.`order_id`
FROM `users` AS `a` INNER JOIN `orders` AS `b` ON a.id = b.user_id
WHERE (a.id = '{$userId}')
```

Translated into Zend_Db style:

```php
$select = $db->select()
             ->from(array('a'=>'users'),
                    array('a.id', 'a.name'))
             ->join(array('b'=>'orders'), 'a.id = b.user_id', array('b.order_id'))
             ->where('a.id = ?', $userId)
```

If no column should be selected from the second table, the 3rd parameter of join() should be an empty string:

```sql
SELECT a.id, a.name
FROM users AS a, orders AS b
WHERE a.id = b.user_id
AND a.id = {$userId}
```

```php
$select = $db->select()
             ->from(array('a'=>'users'),
                    array('a.id', 'a.name'))
             ->join(array('b'=>'orders'), 'a.id = b.user_id', '')
             ->where('a.id = ?', $userId)
```

Note: if the 3rd parameter is not written at all, it will select all the fields from that table:

```sql
SELECT a.id, a.name, b.*
FROM users AS a, orders AS b
WHERE a.id = b.user_id
AND a.id = {$user_id}
```

```php
$select = $db->select()
             ->from(array('a'=>'users'),
                    array('a.id', 'a.name'))
              ->join(array('b'=>'orders'), 'a.id = b.user_id')
              ->where('a.id = ?', $userId)
```

## SELECT query - WHERE IN clause

```sql
SELECT id
FROM users
WHERE aff_id IN ('1','2','3')
```

```php
$select = $db->select()
             ->from('users', array('id'))
             ->where('aff_id IN (?)', array(1,2,3));
```

## Debugging a query

If you are not sure the correct query is being generated, echo it before fetching:

```php
echo $select->__toString();exit;
```

## FAQ

**Q: What does Zend_Db provide?**
A: Zend_Db and its related classes provide a simple SQL database interface for Zend Framework.
To connect to a MySQL database, the Pdo_Mysql adapter is used via Zend_Db::factory('Pdo_Mysql', $dbConnect).

**Q: How do you write a SELECT with a JOIN and a WHERE clause in Zend_Db style?**
A: Use $db->select()->from(array('a'=>'users'), array('a.id','a.name'))->join(array('b'=>'orders'), 'a.id = b.user_id', array('b.order_id'))->where('a.id = ?', $userId), which is equivalent to a classical SQL query using INNER JOIN.

**Q: How do you join a table without selecting any of its columns?**
A: Pass an empty string as the 3rd parameter of the join() method, e.g. ->join(array('b'=>'orders'), 'a.id = b.user_id', '').

**Q: What happens if the 3rd parameter of join() is omitted entirely?**
A: If the 3rd parameter is not written, it will select all the fields from that joined table (equivalent to SELECT ..., b.* in SQL).

**Q: How do you write a WHERE IN clause with Zend_Db?**
A: Use ->where('aff_id IN (?)', array(1,2,3)) on the select object, equivalent to SQL's WHERE aff_id IN ('1','2','3').

**Q: How can you check that a Zend_Db select is generating the correct query?**
A: Before fetching it, echo the query to visualize it: echo $select->__toString();exit;

## Resources

- Zend_Db
- [What are returning the FETCH functions from Zend_Db](http://www.dotkernel.com/best-practice/sql-fetch-zend-db/)
- [Subqueries with Zend_Db](http://www.dotkernel.com/best-practice/subqueris-with-zend-db/)
- [INSERT, UPDATE, DELETE statements with Zend_Db](http://www.dotkernel.com/best-practice/iud-statements-with-zend-d/)
