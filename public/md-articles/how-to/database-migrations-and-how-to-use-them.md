---
title: "Database migrations and how to use them"
description: "An overview of using database migrations and seeders in the Dotkernel stack via the 'php dot' command, covering setup, workflow best practices, and the difference between migrations and seeders."
author: "Jesper"
date_published: "2017-08-28"
canonical_url: "https://www.dotkernel.com/how-to/database-migrations-and-how-to-use-them/"
category: "How to's"
language: "en"
---

# Database migrations and how to use them

## TL;DR

Database migrations track schema changes so teams can collaborate without ad hoc, convoluted database change messages and can keep column types consistent across the team.
A package for the Dotkernel stack adds migrations and seeders to the application via a new `php dot` command.
The article walks through adopting migrations in an existing project, running and naming them, and explains how seeders differ from migrations by adding data rather than changing schema.

## Migrations, the Superhero Your Database Deserves

Migrations ease the process of working together on projects, as well as deploying the database changes.
A newly released [package](https://github.com/JapSeyz/Dot-migrations) for the Dotkernel stack integrates migrations and seeders into the application.
This is all done via the newly introduced "php dot" command that's available in the Dotkernel stack.
This article assumes that you're familiar with the basic concepts of migrations.
If you're not, there's an excellent article available [here](https://medium.com/@JapSeyz/database-migrations-and-why-you-should-use-them-11bfde52d7c2).
To recap the article above, a database migration is a change in the database schema.

## Benefits

By using migrations and seeders, you ensure a better workflow for teams, as changes to the database no longer require convoluted messages.
It also ensures that all team members use the same types with the same limits on each column, and someone doesn't have VARCHAR(50) while another has VARCHAR(150).
It keeps the database synchronised throughout the team with minimal effort.
When pushing to production, the deployment script will automatically run the migrations and keep the database schema in sync with the codebase.
It requires no manual SSH'ing into production servers and no risky manual changes to production databases.

## Getting Started

To setup migrations, simply follow the installation instructions in the package provided for Dotkernel.
After the package has been successfully setup, you're ready to go.
To see the available commands, simply write `php dot` in a console that's located in your project root.

## How to Use Migrations in My Project?

Migrations are very easily adapted to any project.

- Existing projects should start by creating a new migration, and mirror the current database design in that file.
  - Backup the database, in case anything unforeseen should happen.
  - After the design has been mirrored, the existing tables should be deleted, and the migrations can be run.

1. Whenever you would normally manually touch the database, make a migration for doing it instead (`php dot make:migration <name>`).
2. Give the migrations reasonable names, so it's easy to see their action, and the DB progression, in the project tree.
3. Run the migrate command, and watch the database automatically generate and run the SQL to update the tables and columns as configured.
4. Enjoy a safe and worry free database that won't suffer from manually touching it.

Notice: migrations are stored in version control, so whenever you pull an update, quickly run `php dot migrate`, as it will only ever migrate new files, and leave out the ones that have been run already.

## Seeders

If you need to add some data to a table, e.g. adding a default admin profile on a fresh installation, this can be done via seeders.
Migrations manage the database layouts, seeders add data to the database.
Seeders can be configured to run every time a certain migration is run, so no matter when you or how you reset your database, it'll make sure that there's always a default admin profile when the CreateAdminTableMigration is run.
Seeders are created the same way as migrations, and also benefit hugely from being given reasonable names that explain what they do.

1. Create the required seeders, one for each table is the best practice, so you can have a "UserTableSeeder" and a "CarTableSeeder" etc.
2. Setup when these seeders should run:
   - When running a migration
   - Via a console command, this is used to seed x amount of rows of (random) data to the database for testing.

## FAQ

**Q: What is a database migration?**
A: A database migration is a change to the database schema.
The Dotkernel stack integrates migrations (and seeders) via a package that adds the "php dot" command to the application.

**Q: What are the benefits of using migrations and seeders?**
A: They keep the database synchronised across the team with minimal effort, so column types and limits stay consistent instead of drifting (e.g. one dev using VARCHAR(50) and another VARCHAR(150)).
Deployment scripts can also run migrations automatically in production, avoiding manual SSH access and risky manual database changes.

**Q: How do I create a new migration?**
A: Run `php dot make:migration <name>`, giving the migration a reasonable name so its action and the database's progression are easy to follow in the project tree.

**Q: What's the difference between migrations and seeders?**
A: Migrations manage the database layout/schema, while seeders add data to the database, such as adding a default admin profile on a fresh installation.

**Q: What happens when I run php dot migrate more than once?**
A: Since migrations are stored in version control, running `php dot migrate` will only ever run new migration files, leaving out the ones that have already been run.

## Acknowledgements

The images in the article show screenshots from [MySQL Workbench](https://www.mysql.com/products/workbench/), and the migration package used is [Phinx](https://phinx.org/).
