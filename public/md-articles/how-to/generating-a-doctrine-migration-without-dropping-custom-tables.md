---
title: "Generating a doctrine migration without dropping custom tables"
description: "How to run doctrine-migrations diff without generating DROP TABLE statements for unmapped custom tables, using the --filter-expression option."
author: "Alex Karajos"
date_published: "2021-07-29"
canonical_url: "https://www.dotkernel.com/how-to/generating-a-doctrine-migration-without-dropping-custom-tables/"
category: "How to's"
language: "en"
---

# Generating a doctrine migration without dropping custom tables

## TL;DR

When an application has custom, unmapped database tables, running `doctrine-migrations diff` will try to drop them, since no Doctrine entity describes them.
This article shows how to prevent that using the `--filter-expression` option, including how to filter multiple table prefixes at once.
It also flags a Windows PowerShell quirk where the caret in the regex gets stripped, and how to work around it.

## Usage

If your application needs to hold some custom (unmapped) tables in the database, then generating migrations with `doctrine-migrations diff` will try to drop the custom tables.
This article provides a solution on how to avoid dropping those tables.
Run the following command in your application's root directory:

```bash
vendor/bin/doctrine-migrations diff
```

If you have mapping modifications, this will create a new migration file under the `data/doctrine/migrations/` directory.
Opening the migration file, you will notice that it contains some queries that will drop your `oauth_*` tables because they are unmapped (there is no doctrine entity describing them).
You should delete your latest migration with the DROP queries in it, as we will create another one, without the DROP queries in it.
In order to avoid dropping these tables, you need to add a parameter called filter-expression.
The command to be executed without dropping these tables looks like this:

On Windows (use double quotes):

```bash
vendor/bin/doctrine-migrations diff --filter-expression="/^(?!oauth_)/"
```

On Linux/macOS (use single quotes):

```bash
vendor/bin/doctrine-migrations diff --filter-expression='/^(?!oauth_)/'
```

## Filtering Multiple Unmapped Table Patterns

If your database contains multiple unmapped table groups, then the pattern in `filter-expression` should hold all table prefixes concatenated by the pipe character (`|`).
For example, if you need to filter tables prefixed with `foo_` and `bar_`, then the command should look like this:

On Windows:

```bash
vendor/bin/doctrine-migrations diff --filter-expression="/^(?!foo_|bar_)/"
```

On Linux/macOS:

```bash
vendor/bin/doctrine-migrations diff --filter-expression='/^(?!foo_|bar_)/'
```

## Troubleshooting

On Windows, running the command in PowerShell might still add the `DROP TABLE oauth_*` queries to the migration file.
This happens because for PowerShell the caret (`^`) is a special character, so it gets dropped (`"/^(?!oauth_)/"` becomes `"/(?!oauth_)/"` when it reaches your command).
Escaping it will not help either.
In this case, we recommend running the command:

- directly from your IDE
- using Linux shell
- from the Command Prompt

## Help

You can get more help with this command by running:

```bash
vendor/bin/doctrine-migrations help diff
```

## FAQ

**Q: Why does doctrine-migrations diff try to drop custom tables?**
A: Because those tables are unmapped (there's no Doctrine entity describing them), so diff generates queries that drop them when it creates a new migration file.

**Q: How do I avoid dropping unmapped tables when generating a migration?**
A: Delete the migration containing the DROP queries, then re-run the diff command with a filter-expression parameter, e.g. `vendor/bin/doctrine-migrations diff --filter-expression='/^(?!oauth_)/'` on Linux/macOS (single quotes) or with double quotes on Windows.

**Q: How do I filter multiple unmapped table prefixes at once?**
A: Concatenate the table prefixes in the filter-expression pattern with a pipe character, for example `--filter-expression='/^(?!foo_|bar_)/'` to exclude tables prefixed with foo_ and bar_.

**Q: Why might Windows PowerShell still drop the tables even with a filter expression?**
A: PowerShell treats the caret (^) as a special character and strips it, turning "/^(?!oauth_)/" into "/(?!oauth_)/" by the time it reaches the command, and escaping it doesn't help.
The recommended workaround is to run the command from your IDE, a Linux shell, or the Command Prompt instead.
