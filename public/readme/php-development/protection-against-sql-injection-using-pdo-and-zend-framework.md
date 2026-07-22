---
title: "Protection against SQL Injection using PDO and Zend Framework"
description: "An overview of what SQL injection is, what PDO provides, and how prepared statements in Zend Framework help - but don't fully guarantee - protection against SQL injection."
author: "admin"
date_published: "2010-06-16"
canonical_url: "https://www.dotkernel.com/php-development/protection-against-sql-injection-using-pdo-and-zend-framework/"
category: "PHP Development"
language: "en"
---

# Protection against SQL Injection using PDO and Zend Framework

## TL;DR

SQL injection exploits unfiltered user input passed into SQL statements. PDO (PHP Data Objects) is a standardized database access layer that provides a data-access abstraction (not a database abstraction) and offers several benefits, including help protecting against SQL injection. In Zend Framework, prepared statements are encouraged since they handle parameter escaping, but they are not a complete guarantee against SQL injection - especially with PDO_MySQL, and with WHERE IN / ORDER BY clauses.

## What is SQL injection?

SQL injection is a technique that exploits a security vulnerability occurring in the database layer of an application. Usually, user input is not filtered by the script and is passed into a SQL statement.

## What is PDO?

PDO - PHP Data Objects - is a database access layer providing a standardized method of access to multiple databases. PDO provides a data-access abstraction layer, meaning that regardless of which database you're using, you apply the same functions to issue queries and fetch data. PDO does not provide a database abstraction; it doesn't rewrite SQL or emulate missing features.

Among PDO's benefits are:

- Access methods that allow complete control over how attributes are read and written
- Validation on a per-record and per-attribute level
- Easier fetching of objects from related tables
- Reusable logic - the same codebase is much easier to maintain
- Cleaner code by using object-oriented code
- Fewer errors from SQL query generation
- Protection against SQL injection

## Prepared statements in Zend Framework

In Zend Framework, database access methods usually support prepared statements. Dynamic SQL queries are allowed, but all parameters must be escaped, otherwise SQL injection is possible. Because of this, prepared statements are encouraged, since they handle escaping parameters for you.

Most people believe that using prepared statements makes them 100% protected from SQL injection, but this is not true. Input data should always be validated and sanitized, and PDO should be seen as another line of defense. PDO does not protect against other security vulnerabilities like XSS (cross-site scripting), but it does help protect an application against SQL injection.

## The PDO_MySQL risk

A problem can occur in Zend Framework where SQL injection is possible even while using PDO_MySQL. PDO_MySQL is more dangerous than traditional MySQL applications: traditional MySQL allows only a single SQL query, while PDO_MySQL has no such limitation, so there is a risk of being injected with multiple queries. To avoid this, use the correct prepared statements provided by Zend Framework. Also pay attention when your SQL query has `WHERE IN` and `ORDER BY` clauses, since these cannot normally be handled correctly by prepared statements - in this case, escape the data instead.

Zend_Db provides two escaping methods for this purpose: `quote()` and `quoteIdentifier()`. Both handle strings by putting them between single quotes.

## FAQ

**Q: What is SQL injection?**
A: SQL injection is a technique that exploits a security vulnerability occurring in the database layer of an application. Usually, user input is not filtered by the script and is passed directly into a SQL statement.

**Q: What is PDO and what kind of abstraction does it provide?**
A: PDO (PHP Data Objects) is a database access layer providing a standardized method of access to multiple databases. It provides a data-access abstraction layer, meaning you apply the same functions to issue queries and fetch data regardless of which database you're using. However, PDO does not provide a database abstraction - it doesn't rewrite SQL or emulate missing database features.

**Q: What are the benefits of using PDO?**
A: Among the benefits are: access methods that allow complete control over how attributes are read and written, validation on a per-record and per-attribute level, easier fetching of objects from related tables, reusable logic that makes the codebase easier to maintain, cleaner code through object-oriented programming, fewer errors from SQL query generation, and protection against SQL injection.

**Q: Does using prepared statements in Zend Framework fully protect against SQL injection?**
A: Not entirely. Zend Framework's database access methods usually support prepared statements, which are encouraged because they handle escaping parameters for you, but dynamic SQL queries are still allowed and any parameters must be escaped manually or SQL injection becomes possible. Many people believe prepared statements offer 100% protection, but this isn't true - input data should always be validated and sanitized, with PDO treated as another line of defense. PDO also does not protect against other vulnerabilities such as XSS (cross-site scripting).

**Q: Why can PDO_MySQL be riskier than traditional MySQL usage, and how can this be avoided?**
A: Traditional MySQL allows only a single SQL query at a time, but PDO_MySQL has no such limitation, meaning there is more risk of being injected with multiple queries. To avoid this, you should use the correct prepared statements from Zend Framework, and pay particular attention to WHERE IN and ORDER BY clauses, since they aren't normally handled correctly by prepared statements - in these cases you should escape the data yourself.

**Q: What escaping methods does Zend_Db provide?**
A: Zend_Db has two escaping methods that can be used: quote() and quoteIdentifier(). Both of these methods handle strings by putting them between single quotes.

## Resources

- [SQL Injection Protection in PHP With PDO (ezinearticles.com)](http://ezinearticles.com/?SQL-Injection-Protection-in-PHP-With-PDO&id=1815110)
- [Zend Webinar: Secure Application Development with the ZF](http://www.zend.com/webinar/Framework/70170000000bEs9-webinar-secure-application-development-with-the-ZF-20100505.flv)
