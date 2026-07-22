---
title: "INSERT, UPDATE, DELETE statements with Zend_Db"
description: "How to write INSERT, UPDATE, and DELETE (DML) statements using Zend_Db, alongside their equivalent raw SQL."
author: "Teo"
date_published: "2010-06-16"
canonical_url: "https://www.dotkernel.com/best-practice/insert-update-delete-statements-with-zend-db/"
category: "Best Practice"
language: "en"
---

# INSERT, UPDATE, DELETE statements with Zend_Db

## TL;DR

DML (Data Manipulation Language) statements change data values in database tables. This article, continuing the Zend_Db series, shows how the three primary DML statements — INSERT, UPDATE, and DELETE — are written in raw SQL and translated into Zend_Db method calls.

## Connecting to the database

```php
$db = Zend_Db::factory('Pdo_Mysql', $dbConnect);
```

## INSERT

SQL:

```sql
INSERT INTO user(email, password, firstName, lastName, active)
       VALUES ('$email', '$password', '$firstName', '$lastName', 1);
```

Zend_Db:

```php
$data = array( 'email' => $email,
            'password' => $password,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'active' => '1');
$db->insert('user', $data);
```

## UPDATE

SQL:

```sql
UPDATE user
   SET password = '$password',
       firstName = '$firstName',
       lastName = '$lastName',
       accountUpdate = (accountUpdate +1)
 WHERE id = '$id'
```

Zend_Db:

```php
$data = array('password' => $password,
              'firstName' => $firstName,
              'lastName' => $vlastname,
              'accountUpdate' => new Zend_Db_Expr('accountUpdate+1'));
$db->update('user', $data, 'id = '.$id);
```

## DELETE

SQL:

```sql
DELETE FROM user WHERE id = '$id'
```

Zend_Db:

```php
$db->delete('user', 'id = '.$id);
```

## FAQ

**Q: What are DML statements?**
A: DML (Data Manipulation Language) statements are statements that change data values in database tables. There are 3 primary DML statements: INSERT, UPDATE, and DELETE.

**Q: How do you insert a new row with Zend_Db?**
A: Build an associative array of column names to values (e.g. email, password, firstName, lastName, active) and pass it to $db->insert('user', $data), which corresponds to an SQL INSERT INTO ... VALUES statement.

**Q: How do you update rows with Zend_Db, including incrementing a column?**
A: Build a $data array of the columns to update, using a Zend_Db_Expr for expressions such as incrementing accountUpdate (new Zend_Db_Expr('accountUpdate+1')), then call $db->update('user', $data, 'id = '.$id).

**Q: How do you delete a row with Zend_Db?**
A: Call $db->delete('user', 'id = '.$id), which is equivalent to the SQL statement DELETE FROM user WHERE id = '$id'.

## Resources

- [Zend_Db series](http://www.dotkernel.com/dotkernel/sql-select-zend-db/)
