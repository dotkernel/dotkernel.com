---
title: "What are returning the FETCH functions from Zend_Db"
description: "A side-by-side comparison of the legacy query()/next_record()/f() row-fetching style with the fetchAll, fetchAssoc, fetchCol, fetchOne, fetchPairs, and fetchRow methods of Zend_Db_Adapter_Abstract."
author: "Teo"
date_published: "2010-06-15"
canonical_url: "https://www.dotkernel.com/best-practice/what-are-returning-the-fetch-functions-from-zend-db/"
category: "Best Practice"
language: "en"
---

# What are returning the FETCH functions from Zend_Db

## TL;DR

Continuing the Zend_Db article series, this article walks through the FETCH methods available on Zend_Db_Adapter_Abstract: fetchAll, fetchAssoc, fetchCol, fetchOne, fetchPairs, and fetchRow.
Each method is shown next to the equivalent old-style code built on query(), next_record(), and f(), so the two approaches can be compared side by side.

## Available FETCH Methods

Continuing the Zend_Db article series, this article stops at the FETCH methods found in Zend_Db_Adapter_Abstract:

```php
array  fetchAll   (string|Zend_Db_Select $sql, ...)
array  fetchAssoc (string|Zend_Db_Select $sql, ...)
array  fetchCol   (string|Zend_Db_Select $sql, ...)
string fetchOne   (string|Zend_Db_Select $sql, ...)
array  fetchPairs (string|Zend_Db_Select $sql, ...)
array  fetchRow   (string|Zend_Db_Select $sql, ...)
```

To make it easier to follow, each example below shows the classical, old-style query first, followed by the equivalent query written in Zend_Db style.

## Connecting to the Database

Initialize the connection to the MySQL database:

```php
$db = Zend_Db::factory('Pdo_Mysql', $dbConnect);
```

## Setting Up the Query

Here is a SQL query that we want to fetch:

```sql
$sql = "SELECT id, title FROM files";
$db->query($sql)
```

Here is the same query written in Zend_Db style:

```php
$select = $db->select()
             ->from('files', array('id', 'title'))
```

Note: the old style of fetching shown below uses an older class.
Here's what you need to know about its methods:

- `query()` is similar to `mysqli_query()` from the Mysqli PHP extension
- `next_record()` is similar to `mysqli_next_result()` from the Mysqli PHP extension
- `f()` retrieves the value of the column specified as a parameter

## fetchAll

Old style:

```php
while($db->next_record())
{
    $a[] = array(
                 'id' => $db->f('id'),
                 'title' => $db->f('title')
                 );
}
```

Zend_Db style:

```php
$a = $db->fetchAll($select);
```

## fetchAssoc

Old style:

```php
while($db->next_record())
{
    $a = array(
                             'id' => $db->f('id'),
                             'title' => $db->f('title')
                            );
}
```

Zend_Db style:

```php
$a = $db->fetchAssoc($select);
```

## fetchCol

Old style:

```php
while($db->next_record())
{
    $a[] = $db->f('id');
}
```

Zend_Db style:

```php
$a = $db->fetchCol($select);
```

## fetchOne

Old style:

```php
$db->next_record();
$a = $db->f('id');
```

Zend_Db style:

```php
$a = $db->fetchOne($select);
```

## fetchPairs

Old style:

```php
while($db->next_record())
{
    $a = $db->f('title');
}
```

Zend_Db style:

```php
$a = $db->fetchPairs($select);
```

## fetchRow

Old style:

```php
$db->next_record();
$a = array(
           'id' => $db->f('id'),
           'title' => $db->f('title')
          );
```

Zend_Db style:

```php
$a = $db->fetchRow($select);
```

## FAQ

**Q: What FETCH methods are available in Zend_Db_Adapter_Abstract?**
A: The article covers fetchAll, fetchAssoc, fetchCol, fetchOne, fetchPairs, and fetchRow.

**Q: What does fetchAll do compared to the old query style?**
A: `$a = $db->fetchAll($select)` replaces the old-style loop that calls `next_record()` repeatedly and builds an array of associative rows using `f()` for each column.

**Q: What does fetchRow return?**
A: `$a = $db->fetchRow($select)` returns a single row as an associative array, replacing a single `next_record()` call followed by `f()` calls for each column.

**Q: What does fetchOne return?**
A: `$a = $db->fetchOne($select)` returns a single value, replacing a single `next_record()` call followed by one `f()` call.

**Q: How do the old-style query(), next_record(), and f() methods relate to Mysqli?**
A: `query()` is similar to `mysqli_query()`, `next_record()` is similar to `mysqli_next_result()`, and `f()` retrieves the value of the column specified as a parameter.
