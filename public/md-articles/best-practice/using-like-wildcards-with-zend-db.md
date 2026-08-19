---
title: "Using LIKE wildcards with Zend_Db"
description: "How to use the SQL LIKE condition and its _ and % wildcards, including NOT LIKE, with Zend_Db's quoteInto and quoteIdentifier methods."
author: "Teo"
date_published: "2010-09-10"
canonical_url: "https://www.dotkernel.com/best-practice/using-like-wildcards-with-zend-db/"
category: "Best Practice"
language: "en"
---

# Using LIKE wildcards with Zend_Db

## TL;DR

The LIKE condition allows pattern matching in the WHERE clause of SELECT, INSERT, UPDATE, or DELETE statements.
The `_` wildcard matches a single character, and `%` matches any string of any length (including zero).
This article shows how to use LIKE and NOT LIKE with both wildcards in Zend_Db.

## Connecting to the database

```php
$db = Zend_Db::factory('Pdo_Mysql', $dbConnect);
```

## LIKE _

Return all ids that start with '1' and whose second digit is between 0 and 9 (10, 11, 12, ..., 18, 19):

```sql
SELECT * FROM `table` WHERE (`id` LIKE '1_' )
```

```php
$col = $this->db->quoteIdentifier('id');
$where = $this->db->quoteInto("$col LIKE ? ", '1_');
$select = $this->db->select()
    ->from('table')
    ->where($where);
$result = $this->db->fetchAll($select);
```

Return all instances whose name is 4 characters long, starting with 'Fr' and ending with 'd' (Frad, Fred, Frod, etc.):

```sql
SELECT * FROM `table` WHERE (`name` LIKE 'Fr_d' )
```

```php
$col = $this->db->quoteIdentifier('name');
$where = $this->db->quoteInto("$col LIKE ? ", 'Fr_d');
$select = $this->db->select()
    ->from('table')
    ->where($where);
$result = $this->db->fetchAll($select);
```

## LIKE %

Returns all instances that have the 'gallery' string in the `source` field:

```sql
SELECT * FROM `table` WHERE (`source` LIKE '%gallery%' )
```

```php
$col = $this->db->quoteIdentifier('source');
$where = $this->db->quoteInto("$col LIKE ? ", '%gallery%');
$select = $this->db->select()
    ->from('table')
    ->where($where);
$result = $this->db->fetchAll($select);
```

Returns all instances that have the 'gallery' or 'folder' strings in the `source` field:

```sql
SELECT * FROM `table` WHERE (`source` LIKE '%gallery%' OR `source` LIKE ('%folder%') )
```

```php
$col = $this->db->quoteIdentifier('source');
$where = $this->db->quoteInto("$col LIKE ? ", '%gallery%');
$where .= $this->db->quoteInto("OR $col LIKE (?) ", '%folder%');
$select = $this->db->select()
    ->from('table')
    ->where($where);
$result = $this->db->fetchAll($select);
```

## NOT LIKE _

Returns all 2-digit ids that don't start with `1` (20->99) or that don't have exactly 2 digits (1, 2, ..., 8, 9, 100, 101, ...):

```sql
SELECT * FROM `table` WHERE (`id` NOT LIKE '1_' )
```

```php
$col = $this->db->quoteIdentifier('id');
$where = $this->db->quoteInto("$col NOT LIKE ? ", '1_');
$select = $this->db->select()
    ->from('table')
    ->where($where);
$result = $this->db->fetchAll($select);
```

## NOT LIKE %

Returns all instances that don't have 'gallery', 'folder', or 'file' in the `source` field:

```sql
SELECT * FROM `table` WHERE (`source` NOT LIKE ('%gallery%') AND `source` NOT LIKE ('%folder%') AND `source` NOT LIKE ('%file%') )
```

```php
$col = $this->db->quoteIdentifier('source');
$where = $this->db->quoteInto("$col NOT LIKE (?) ", '%gallery%');
$where .= $this->db->quoteInto("AND $col NOT LIKE (?) ", '%folder%');
$where .= $this->db->quoteInto("AND $col NOT LIKE (?) ", '%file%');
$select = $this->db->select()
    ->from('table')
    ->where($where);
$result = $this->db->fetchAll($select);
```

## Other example

```sql
SELECT * FROM `table` WHERE `number` LIKE '_6%'
```

```php
$col = $this->db->quoteIdentifier('number');
$where = $this->db->quoteInto("$col LIKE ? ", '_6%');
$select = $this->db->select()
    ->from('table')
    ->where($where);
$result = $this->db->fetchAll($select);
```

## FAQ

**Q: What do the LIKE wildcards _ and % mean?**
A: The _ wildcard matches a single character, while % matches any string of any length, including zero length.

**Q: Which SQL statements can use the LIKE condition?**
A: LIKE allows pattern matching in the WHERE clause and can be used in any valid SQL statement: SELECT, INSERT, UPDATE, or DELETE.

**Q: How do you build a LIKE query with Zend_Db?**
A: Quote the column with $this->db->quoteIdentifier(), build the condition with $this->db->quoteInto("$col LIKE ?
", $pattern), and pass the resulting $where string into ->where() on a select, then run it with $this->db->fetchAll($select).

**Q: How do you combine multiple LIKE conditions with OR?**
A: Build the first condition with quoteInto, then append further ones with quoteInto("OR $col LIKE (?)
", $pattern), as in the example matching 'gallery' or 'folder' in the source field.

**Q: How does NOT LIKE differ from LIKE?**
A: NOT LIKE negates the pattern match - for example, id NOT LIKE '1_' returns ids that don't start with 1 or don't have exactly 2 digits, and NOT LIKE conditions can be chained with AND to exclude several patterns at once.
