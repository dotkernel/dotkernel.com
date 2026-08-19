---
title: "Why use CURRENT_TIMESTAMP on a field that record date/time?"
description: "Why a TIMESTAMP column should default to CURRENT_TIMESTAMP on insert, how ON UPDATE CURRENT_TIMESTAMP keeps it fresh on every update, and how the DEFAULT/ON UPDATE clause combinations behave."
author: "Teo"
date_published: "2010-06-29"
canonical_url: "https://www.dotkernel.com/best-practice/why-use-current-timestamp-on-a-field-that-record-date-time/"
category: "Best Practice"
language: "en"
---

# Why use CURRENT_TIMESTAMP on a field that record date/time?

## TL;DR

On a TIMESTAMP field that records date and time when inserting a new record, it's encouraged to use the CURRENT_TIMESTAMP constant as its DEFAULT value.
This removes the need to set the value manually from PHP or with MySQL's NOW() function, and the ON UPDATE CURRENT_TIMESTAMP clause can additionally keep the field updated automatically on every row update.
Only one TIMESTAMP field per table can be DEFAULT CURRENT_TIMESTAMP.

## Why Use CURRENT_TIMESTAMP as a Default

On a TIMESTAMP field that records date and time when inserting a new record, it is encouraged to use the CURRENT_TIMESTAMP constant as a DEFAULT value.
Because when inserting a new row in the table, there is no need to specifically add the value for the date and time field, either by creating it from PHP code with the Date/Time functions or with MySQL's NOW() function:

```sql
ALTER TABLE `user` CHANGE `dateCreated` `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
```

## Automatically Updating with ON UPDATE CURRENT_TIMESTAMP

CURRENT_TIMESTAMP is also a solution for updating date and time fields.
Use the `ON UPDATE CURRENT_TIMESTAMP` clause if you want the value of the field to be changed automatically each time the row is updated:

```sql
ALTER TABLE `user` CHANGE `dateLogin` `dateLogin` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
```

## DEFAULT and ON UPDATE Clause Combinations

DEFAULT and ON UPDATE clauses can be used together or separately, depending on your needs:

- With both `DEFAULT CURRENT_TIMESTAMP` and `ON UPDATE CURRENT_TIMESTAMP` clauses, the column has the current timestamp for its default value and is automatically updated.
- With neither `DEFAULT` nor `ON UPDATE` clauses, it is the same as `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` (only for the first TIMESTAMP field in the table).
- With a `DEFAULT CURRENT_TIMESTAMP` clause and no `ON UPDATE` clause, the column has the current timestamp for its default value but is not automatically updated.
- With no `DEFAULT` clause and with an `ON UPDATE CURRENT_TIMESTAMP` clause, the column has a default of 0 and is automatically updated.
- With a constant `DEFAULT` value, the column has the given default and is not automatically initialized to the current timestamp.
If the column also has an `ON UPDATE CURRENT_TIMESTAMP` clause, it is automatically updated; otherwise, it has a constant default and is not automatically updated.

For more details, check out the [MySQL Manual](https://dev.mysql.com/doc/refman/9.7/en/datetime.html).

Note: only one timestamp field can be `DEFAULT CURRENT_TIMESTAMP` in a table.

## FAQ

**Q: Why use CURRENT_TIMESTAMP as a DEFAULT value for a date/time field?**
A: Because when inserting a new row, there is no need to specifically set the date/time value yourself, either from PHP Date/Time functions or with MySQL's NOW() function.

**Q: How do you make a field update its timestamp automatically on every UPDATE?**
A: Add the ON UPDATE CURRENT_TIMESTAMP clause, for example: `ALTER TABLE `user` CHANGE `dateLogin` `dateLogin` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP`.

**Q: What happens if a TIMESTAMP column has neither a DEFAULT nor an ON UPDATE clause?**
A: For the first TIMESTAMP field in the table, having neither clause is the same as DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP.

**Q: What happens with a DEFAULT CURRENT_TIMESTAMP clause but no ON UPDATE clause?**
A: The column gets the current timestamp as its default value but is not automatically updated afterward.

**Q: Can more than one TIMESTAMP column default to CURRENT_TIMESTAMP in the same table?**
A: No. Only one timestamp field in a table can be DEFAULT CURRENT_TIMESTAMP.
