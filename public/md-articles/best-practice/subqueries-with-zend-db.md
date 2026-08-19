---
title: "Subqueries with Zend_Db"
description: "How to build a query combining COUNT, LEFT JOIN, and GROUP BY across multiple tables using Zend_Db, including a subquery embedded as a column."
author: "Teo"
date_published: "2010-06-15"
canonical_url: "https://www.dotkernel.com/best-practice/subqueries-with-zend-db/"
category: "Best Practice"
language: "en"
---

# Subqueries with Zend_Db

## TL;DR

Continuing the Zend_Db series, this article shows a more complex query - combining COUNT(), LEFT JOIN, and GROUP BY across 3 tables, with a count taken from 2 different tables - and how to build it, including a nested subquery, using Zend_Db.

## The SQL query

```sql
SELECT a.id,
       a.title,
       (SELECT COUNT(c.track_id)
        FROM track_files AS c
        WHERE c.track_id = a.id
       ) AS `count_files`,
       COUNT(b.track_id) AS count_courses
FROM tracks AS a
LEFT JOIN track_courses AS b ON (a.id = b.track_id)
GROUP BY a.id
```

## Connecting to the database

```php
$db = Zend_Db::factory('Pdo_Mysql', $dbConnect);
```

## Building the query in Zend_Db

```php
$db->select()
   ->from(array('a'=>'tracks'),
         array('id',
              'title',
             'count_files' => new Zend_Db_Expr(
                        '('.$db->select()
                           ->from(array('c'=>'track_files'),
                                     array(new Zend_Db_Expr('COUNT(c.track_id)')))
                           ->where('c.track_id = a.id').')' )
               )
          )
   ->joinLeft(array('b'=>'track_courses'),
         'a.id = b.track_id',
         array('count_courses' => 'COUNT(b.track_id)')
         )
   ->group('a.id');
```

The `count_files` column is built by wrapping a nested `$db->select()` call inside a `Zend_Db_Expr`, correlated back to the outer table via `c.track_id = a.id`.

## FAQ

**Q: What SQL techniques does this subquery example combine?**
A: The example combines COUNT(), LEFT JOIN, and GROUP BY, selecting from 3 tables and counting rows from 2 different tables.

**Q: How do you embed a subquery as a selected column in a Zend_Db select?**
A: Wrap a nested $db->select() call inside a Zend_Db_Expr, building the subquery string with the outer table's correlated WHERE condition (e.g. c.track_id = a.id), as shown for the count_files column.

**Q: How is the LEFT JOIN with a COUNT expressed in Zend_Db?**
A: Use ->joinLeft(array('b'=>'track_courses'), 'a.id = b.track_id', array('count_courses' => 'COUNT(b.track_id)')) followed by ->group('a.id').

## Resources

- [Zend_Db series](http://www.dotkernel.com/dotkernel/sql-select-zend-db/)
