---
title: "List available endpoints in DotKernel API using dot-cli"
description: "How to use dot-cli's route:list command to display, and filter, all available endpoints in a DotKernel API 3 application."
author: "Alex Karajos"
date_published: "2021-06-29"
canonical_url: "https://www.dotkernel.com/how-to/list-available-endpoints-in-dotkernel-api-using-dot-cli/"
category: "How to's"
language: "en"
---

# List available endpoints in DotKernel API using dot-cli

## TL;DR

Starting from version 3, DotKernel API uses the dot-cli package to list all of its available endpoints via the `route:list` command.
The command's output can be filtered by route name, path, or HTTP method, and filters are case-insensitive and combinable.

## Displaying DotKernel API Endpoints Using dot-cli

Starting from version 3, DotKernel API uses [dot-cli](https://github.com/dotkernel/dot-cli) to display a list of available endpoints.

## Usage

Run the following command in your application's root directory:

```bash
php ./bin/cli.php route:list
```

The command runs through all routes and extracts endpoint information in realtime.
The output should be similar to the following:

```
+--------+---------------------------------+--------------------------------+
| Method | Name                            | Path                           |
+--------+---------------------------------+--------------------------------+
| PATCH  | account.activate                | /account/activate/{hash}       |
| POST   | account.activate.request        | /account/activate              |
| PATCH  | account.modify-password         | /account/reset-password/{hash} |
| POST   | account.recover-identity        | /account/recover-identity      |
| POST   | account.register                | /account/register              |
| POST   | account.reset-password.request  | /account/reset-password        |
| GET    | account.reset-password.validate | /account/reset-password/{hash} |
| POST   | admin.create                    | /admin                         |
| DELETE | admin.delete                    | /admin/{uuid}                  |
| GET    | admin.list                      | /admin                         |
| PATCH  | admin.my-account.update         | /admin/my-account              |
| GET    | admin.my-account.view           | /admin/my-account              |
| GET    | admin.role.list                 | /admin/role                    |
| GET    | admin.role.view                 | /admin/role/{uuid}             |
| PATCH  | admin.update                    | /admin/{uuid}                  |
| GET    | admin.view                      | /admin/{uuid}                  |
| POST   | error.report                    | /error-report                  |
| GET    | home                            | /                              |
| POST   | security.generate-token         | /security/generate-token       |
| POST   | security.refresh-token          | /security/refresh-token        |
| POST   | user.activate                   | /user/{uuid}/activate          |
| POST   | user.avatar.create              | /user/{uuid}/avatar            |
| DELETE | user.avatar.delete              | /user/{uuid}/avatar            |
| GET    | user.avatar.view                | /user/{uuid}/avatar            |
| POST   | user.create                     | /user                          |
| DELETE | user.delete                     | /user/{uuid}                   |
| GET    | user.list                       | /user                          |
| DELETE | user.my-account.delete          | /user/my-account               |
| PATCH  | user.my-account.update          | /user/my-account               |
| GET    | user.my-account.view            | /user/my-account               |
| POST   | user.my-avatar.create           | /user/my-avatar                |
| DELETE | user.my-avatar.delete           | /user/my-avatar                |
| GET    | user.my-avatar.view             | /user/my-avatar                |
| GET    | user.role.list                  | /user/role                     |
| GET    | user.role.view                  | /user/role/{uuid}              |
| PATCH  | user.update                     | /user/{uuid}                   |
| GET    | user.view                       | /user/{uuid}                   |
+--------+---------------------------------+--------------------------------+
```

## Filtering Results

The following filters can be applied when displaying the routes list:

- Filter routes by name, using: `-i|--name`
- Filter routes by path, using: `-p|--path`
- Filter routes by method, using: `-m|--method`

The filters are case-insensitive and can be combined.

### Example

Let's find which path should one call to register their user account.
For this we will list routes where method is POST and name contains the string register:

```bash
php ./bin/cli.php route:list --method=post --name=register
```

The output is the following:

```
+--------+------------------+-------------------+
| Method | Name             | Path              |
+--------+------------------+-------------------+
| POST   | account.register | /account/register |
+--------+------------------+-------------------+
```

You can get more help with this command by running:

```bash
php ./bin/cli.php route:list --help
```

## FAQ

**Q: What tool does DotKernel API use to list available endpoints?**
A: Starting from version 3, DotKernel API uses dot-cli to display a list of available endpoints.

**Q: Which command lists all routes?**
A: Run `php ./bin/cli.php route:list` in your application's root directory.
It runs through all routes and extracts endpoint information in realtime, outputting a table with Method, Name and Path columns.

**Q: How can the route list be filtered?**
A: You can filter routes by name using `-i|--name`, by path using `-p|--path`, or by method using `-m|--method`.
The filters are case-insensitive and can be combined.

**Q: How would you find the endpoint used to register a user account?**
A: By combining the method and name filters: `php ./bin/cli.php route:list --method=post --name=register`, which returns the account.register route mapped to /account/register.

**Q: How do you get more help on this command?**
A: Run `php ./bin/cli.php route:list --help` to get more information about the command.
