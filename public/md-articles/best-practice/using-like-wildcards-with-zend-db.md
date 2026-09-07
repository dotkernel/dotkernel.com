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

Continuing the Zend_Db article [series](http://www.dotkernel.com/dotkernel/sql-queries-using-zend-db-select/), let's discuss the LIKE condition.

The **LIKE** condition allows you to use wildcards in the *WHERE* clause of an SQL statement. This allows pattern matching. It can be used in any valid SQL statement (*SELECT*, *INSERT*, *UPDATE* or *DELETE*).

**LIKE wildcards:**

- ***_*** allows you to match a single character
- ***%*** allows you to match any string of any length (including zero length)

*Note*:*

```
$db = Zend_Db::factory('Pdo_Mysql', $dbConnect);
```

**LIKE _**

- Return all ids which start with '1' and second digit is between 0 and 9 (10, 11, 12, ..., 18, 19):

```
SELECT * FROM `table` WHERE (`id` LIKE '1_' )
```

```
$col = $this->db->quoteIdentifier('id');
$where = $this->db->quoteInto("$col LIKE ? ", '1_');
$select = $this->db->select()
    ->from('table')
    ->where($where);
$result = $this->db->fetchAll($select);
```

- Return all instances whose name is 4 characters long, where the first two characters are 'Fr' and the last character is 'd' (Frad, Fred, Frod, etc.) :

```
SELECT * FROM `table` WHERE (`name` LIKE 'Fr_d' )
```

```
$col = $this->db->quoteIdentifier('name');
$where = $this->db->quoteInto("$col LIKE ? ", 'Fr_d');
$select = $this->db->select()
    ->from('table')
    ->where($where);
$result = $this->db->fetchAll($select);
```

**LIKE %**

- Returns all instances that have the 'gallery' string in the *source* field:

```
SELECT * FROM `table` WHERE (`source` LIKE '%gallery%' )
```

```
$col = $this->db->quoteIdentifier('source');
$where = $this->db->quoteInto("$col LIKE ? ", '%gallery%');
$select = $this->db->select()
    ->from('table')
    ->where($where);
$result = $this->db->fetchAll($select);
```

- Returns all instances that have the 'gallery' or 'folder' strings in the *source* field:

```
SELECT * FROM `table` WHERE (`source` LIKE '%gallery%' OR `source` LIKE ('%folder%') )
```

```
$col = $this->db->quoteIdentifier('source');
$where = $this->db->quoteInto("$col LIKE ? ", '%gallery%');
$where .= $this->db->quoteInto("OR $col LIKE (?) ", '%folder%');
$select = $this->db->select()
    ->from('table')
    ->where($where);
$result = $this->db->fetchAll($select);
```

**NOT LIKE _**

- Returns all 2-digit ids that don't start with *1* (20->99 ) or have a different number of digits than 2 (1, 2, ..., 8, 9, 100, 101, ...):

```
SELECT * FROM `table` WHERE (`id` NOT LIKE '1_' )
```

```
$col = $this->db->quoteIdentifier('id');
$where = $this->db->quoteInto("$col NOT LIKE ? ", '1_');
$select = $this->db->select()
    ->from('table')
    ->where($where);
$result = $this->db->fetchAll($select);
```

**NOT LIKE %**

- Returns all instances that don't have 'gallery', 'folder' or 'file' strings in the *source* field:

```
SELECT * FROM `table` WHERE (`source` NOT LIKE ('%gallery%') AND `source` NOT LIKE ('%folder%') AND `source` NOT LIKE ('%file%') )
```

```
$col = $this->db->quoteIdentifier('source');
$where = $this->db->quoteInto("$col NOT LIKE (?) ", '%gallery%');
$where .= $this->db->quoteInto("AND $col NOT LIKE (?) ", '%folder%');
$where .= $this->db->quoteInto("AND $col NOT LIKE (?) ", '%file%');
$select = $this->db->select()
    ->from('table')
    ->where($where);
$result = $this->db->fetchAll($select);
```

**OTHER Example**

```
SELECT * FROM `table` WHERE `number` LIKE '_6%' 
```

```
$col = $this->db->quoteIdentifier('number');
$where = $this->db->quoteInto("$col LIKE ? ", '_6%');
$select = $this->db->select()
    ->from('table')
    ->where($where);
$result = $this->db->fetchAll($select);
```

- The *number* column starts with a digit between 4 and 6 (*[4-6]*)
- The second character in the *number* column can be anything (*_*)
- The third character in the *number* column is 6 (*6*)
- The rest of the *number* column can be any string, of any length (*%*)

## FAQ

**Q: What do the LIKE wildcards _ and % mean?**
A: The _ wildcard matches a single character, while % matches any string of any length, including zero length.

**Q: Which SQL statements can use the LIKE condition?**
A: LIKE allows pattern matching in the WHERE clause and can be used in any valid SQL statement: SELECT, INSERT, UPDATE, or DELETE.

**Q: How do you build a LIKE query with Zend_Db?**
A: Quote the column with $this->db->quoteIdentifier(), build the condition with $this->db->quoteInto("$col LIKE ? ", $pattern), and pass the resulting $where string into ->where() on a select, then run it with $this->db->fetchAll($select).

**Q: How do you combine multiple LIKE conditions with OR?**
A: Build the first condition with quoteInto, then append further ones with quoteInto("OR $col LIKE (?) ", $pattern), as in the example matching 'gallery' or 'folder' in the source field.

**Q: How does NOT LIKE differ from LIKE?**
A: NOT LIKE negates the pattern match — for example, `id` NOT LIKE '1_' returns ids that don't start with 1 or don't have exactly 2 digits, and NOT LIKE conditions can be chained with AND to exclude several patterns at once.
