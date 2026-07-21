---
title: "DotKernel Database Naming Conventions for MySQL"
description: "DotKernel borrows its database naming conventions from FaZend, covering singular table names, auto-incrementing id columns, foreign key and constraint naming patterns, and camelLetter casing."
author: "admin"
date_published: "2010-03-10"
canonical_url: "https://new.dotkernel.com/dotkernel/dotkernel-database-naming-conventions-for-mysql/"
category: "Dotkernel"
language: "en"
---

# DotKernel Database Naming Conventions for MySQL

## TL;DR

DotKernel's database naming conventions are borrowed from FaZend's "Rules of naming of database tables and columns." Tables use singular, camelLetter names, every table has an auto-increment id, foreign keys are named after the referenced table and column, and SQL keywords are capitalized.

## Database naming conventions for tables and columns

- Singular table names only (e.g. `user`, `category`, `product`, `order`, `orderProduct`)
- Every table must have an auto-incrementing integer column `id`
- ZF-like names of columns and tables (e.g. `user::isAdmin`, `orderProduct::product`)
- Foreign keys must have the same name as the referenced table plus the name of the referenced column.
  Example: table referenced is `admin`, column name `Id`, so the foreign key column will be `adminId`.
- Pattern for CONSTRAINT name: `FK_referencedTableName_tableName`.
  Example: `CONSTRAINT FK_admin_adminLogin`.
- SQL keywords are capitalized (e.g. `SELECT`, `INT`)

## Example of proper SQL file formatting and naming

```sql
CREATE TABLE IF NOT EXISTS `user`
 (
   `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
   `username` VARCHAR(255) NOT NULL,
   `password` VARCHAR(25) NOT NULL,
   `email` VARCHAR(100) NOT NULL,
   `firstName` VARCHAR(255) NOT NULL,
   `lastName` VARCHAR(255) NOT NULL,
   `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   `userType` INT(11) NOT NULL AUTO_INCREMENT
   `isActive` ENUM('0','1') NOT NULL DEFAULT '1',
   PRIMARY KEY  (`id`),
   UNIQUE KEY `username` (`username`),
   UNIQUE KEY `email` (`email`)
   CONSTRAINT `FK_user_userType` FOREIGN KEY(`userTypeId`) REFERENCES `userType`(`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
 )
 ENGINE=InnoDB
 DEFAULT CHARSET=latin1
 AUTO_INCREMENT=1 ;
```

## Conclusion

The names of database tables and columns must follow camelLetter naming conventions.

## FAQ

**Q: Where do DotKernel's database naming conventions come from?**
A: They are borrowed from FaZend's "Rules of naming of database tables and columns," an open-source PHP framework based on Zend Framework.

**Q: Should table names be singular or plural?**
A: Singular table names only, for example user, category, product, order, orderProduct.

**Q: How should foreign key columns be named?**
A: A foreign key column takes the name of the referenced table plus the name of the referenced column. For example, referencing table admin's Id column produces a column named adminId.

**Q: What naming pattern is used for CONSTRAINT names?**
A: The pattern is FK_referencedTableName_tableName, for example CONSTRAINT `FK_admin_adminLogin`.

**Q: What casing convention applies to table/column names and to SQL keywords?**
A: Table and column names must follow camelLetter naming conventions, while SQL keywords such as SELECT and INT are capitalized.

## Resources

- FaZend: Rules of naming of database tables and columns: http://fazend.com/a/2009-11-DataNaming.html
