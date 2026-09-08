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

Continuing the Zend_DB article [series](http://www.dotkernel.com/dotkernel/sql-queries-using-zend-db-select/), we are stopping now at *FETCH* methods that are in [Zend_Db_Adapter_Abstract](https://docs.laminas.dev/laminas-db/adapter/):

```php
array  fetchAll   (string|Zend_Db_Select $sql, [mixed $bind = array()])
array  fetchAssoc (string|Zend_Db_Select $sql, [mixed $bind = array()])
array  fetchCol   (string|Zend_Db_Select $sql, [mixed $bind = array()])
string fetchOne   (string|Zend_Db_Select $sql, [mixed $bind = array()])
array  fetchPairs (string|Zend_Db_Select $sql, [mixed $bind = array()])
array  fetchRow   (string|Zend_Db_Select $sql, [mixed $bind = array()])
```

To be more easily to follow, in green box is the classical SQL statement, and in blue box is the query written in Zend_Db style.

Initialize the connection to the MySQL database:

```php
$db = Zend_Db::factory('Pdo_Mysql', $dbConnect);
```

Here is a SQL query that we want to fetch:

```sql
$sql = "SELECT id, title FROM files";
$db->query($sql)
```

```php
$select = $db->select()
             ->from('files', array('id', 'title'))
```

Note: for the old style of fetching we used an old class. What you need to know is:

- *query()* method is similar with mysqli_query() from *Mysqli* PHP extension
- *next_record()* method is similar with mysqli_next_result() from *Mysqli* PHP extension
- *f()* method retrieve the value of the column specified as parameter

**fetchAll**

```php
while($db->next_record())
{
    $a[] = array(
                 'id' => $db->f('id'),
                 'title' => $db->f('title')
                 );
}
```

```php
$a = $db->fetchAll($select);
```

**fetchAssoc**

```php
while($db->next_record())
{
    $a[$db->f('id')] = array(
                             'id' => $db->f('id'),
                             'title' => $db->f('title')
                            );
}
```

```php
$a = $db->fetchAssoc($select);
```

**fetchCol**

```php
while($db->next_record())
{
    $a[] = $db->f('id');
}
```

```php
$a = $db->fetchCol($select);
```

**fetchOne**

```php
$db->next_record();
$a = $db->f('id');
```

```php
$a = $db->fetchOne($select);
```

**fetchPairs**

```php
while($db->next_record())
{
    $a[$db->f('id')] = $db->f('title');
}
```

```php
$a = $db->fetchPairs($select);
```

**fetchRow**

```php
$db->next_record();
$a = array(
           'id' => $db->f('id'),
           'title' => $db->f('title')
          );
```

```php
$a = $db->fetchRow($select);
```

## FAQ

**Q: What FETCH methods are available in Zend_Db_Adapter_Abstract?**
A: The article covers fetchAll, fetchAssoc, fetchCol, fetchOne, fetchPairs, and fetchRow.

**Q: What does fetchAll do compared to the old query style?**
A: $a = $db->fetchAll($select) replaces the old-style loop that calls next_record() repeatedly and builds an array of associative rows using f() for each column.

**Q: What does fetchRow return?**
A: $a = $db->fetchRow($select) returns a single row as an associative array, replacing a single next_record() call followed by f() calls for each column.

**Q: What does fetchOne return?**
A: $a = $db->fetchOne($select) returns a single value, replacing a single next_record() call followed by one f() call.

**Q: How do the old-style query(), next_record(), and f() methods relate to Mysqli?**
A: query() is similar to mysqli_query(), next_record() is similar to mysqli_next_result(), and f() retrieves the value of the column specified as a parameter.
