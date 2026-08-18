---
title: "Dotkernel Admin V4"
description: "An overview of Dotkernel Admin V4, released 19 July 2022 on Mezzio, covering its demo credentials and key features: PHP 8.1 support, configurability, authorization guards, CLI commands, routing, and its Bootstrap-based frontend."
author: "kakapiciu"
date_published: "2022-07-27"
canonical_url: "https://www.dotkernel.com/dotkernel3/dotkernel-admin-v4/"
category: "Dotkernel 3"
language: "en"
---

# Dotkernel Admin V4

## TL;DR

Dotkernel Admin V4, officially released on 19 July 2022, is Dotkernel's PSR-7 Admin application built on Mezzio for managing and displaying tabular data from one or more databases.
It supports PHP 8.1 (minimum PHP 7.4), offers a config-driven module/middleware/route setup, RBAC-based authorization guards, a Symfony Console-based CLI with a file locker, per-module routing via RoutesDelegator, and a Bootstrap 4.5.0 / Fontawesome 5.0.6 frontend using Bootstrap Table for data listing.

## Getting Started with Dotkernel Admin V4

Dotkernel's PSR-7 Admin is an application based on Mezzio, with the main purpose of managing and displaying tabular data from one or more databases components.

On 19 July 2022, Dotkernel Admin V4 has been officially released.
Dotkernel Admin V4 comes with various interesting features and overall improvement of the core framework.

## Demo

Ready to try it out?

There should be an admin account with the following credentials:

- Username: admin
- Password: dotadmin

Head over to https://admin4.dotkernel.net/ and see for yourself.

## Dotkernel Admin V4 Features

### PHP 8.1

Dotkernel Admin V4 fully supports PHP 8.1 with a minimum requirement of PHP 7.4.

### Configurability

From managing middleware order to simply adding an API key to your application, the config directory is the way to go.

Want to register a new module? In `config.php` you will find the right place for it, registering its `ConfigProvider.php`.
Got a shiny new middleware? You can put it with the rest of them in `pipeline.php`, where you can even edit in which order they should run.

You can further customize your app within the autoload directory by changing the application name and URL in `app.global.php`, adding freshly created routes into `navigation.global.php`, and much more.

Example adding a simple route to the navigation bar:

```php
[
    'options' => [
        'label' => 'Dashboard',
        'route' => [
            'route_name' => 'dashboard',
        ],
        'icon' => 'fas fa-tachometer-alt',
    ]
]
```

Or a group of 2 or more routes:

```php
[
    'options' => [
        'label' => 'Manage admins',
        'route' => '',
        'icon' => 'fas fa-user-circle',
    ],
    'pages' => [
        [
            'options' => [
                'label' => 'Admins',
                'uri' => '/admin/manage',
                'icon' => 'fas fa-user-circle',
            ],
        ],
        [
            'options' => [
                'label' => 'Logins',
                'uri' => '/admin/logins',
                'icon' => 'fas fa-sign-in-alt',
            ],
        ]
    ]
]
```

### Authorization Guards

The packages responsible for restricting access to certain parts of the application are [dot-rbac-guard](https://github.com/dotkernel/dot-rbac-guard) and [dot-rbac](https://github.com/dotkernel/dot-rbac).
These packages work together to create an infrastructure that is customizable and diversified to manage user access to the platform by specifying the type of role the user has.

The `authorization.global.php` file provides multiple configurations specifying multiple roles as well as the types of permissions to which these roles have access.

```php
//example of a flat RBAC model that specifies two types of roles as well as their permission
'roles' => [
    'superuser' => [
        'permissions' => [
            'authenticated',
            'edit',
            'delete',
            //etc..
        ]
    ],
    'admin' => [
        'permissions' => [
            'authenticated',
            //etc..
        ]
    ]
]
```

The `authorization-guards.global.php` file provides configuration to restrict access to certain actions based on the permissions defined in `authorization.global.php`, so basically the permissions have to be added in the dot-rbac configuration file first to specify the action restriction permissions.

```php
//example of configuration to restrict certain actions of some routes based on the permissions specified in the dot-rbac configuration file
'rules' => [
    [
        'route' => 'account',
        'actions' => [
            //list of actions to apply, or empty array for all actions
            'unregister',
            'avatar',
            'details',
            'changePassword'
        ],
        'permissions' => [
            'authenticated'
        ]
    ],
    [
        'route' => 'admin',
        'actions' => [
            'deleteAccount'
        ],
        'permissions' => [
            'delete'
            //list of roles to allow
        ]
    ]
]
```

### CLI

For registering a new command, first make sure your command class extends `Symfony\Component\Console\Command\Command`, then you can enable the command by registering it in `config/autoload/cli.global.php`.

Here you will also find the brand new file locker configuration, so you can easily turn it on or off (by default: `'enabled' => true`).

Note: the File Locker System will create a `command-{command-default-name}.lock` file which will not let another instance of the same command run until the previous one has finished.

You can list the existing commands by running the following in a terminal:

```bash
php /bin/cli.php list
```

Note: you can take as example `Dot\Cli\Command\DemoCommand`.

### Routing

Each module gets a `RoutesDelegator.php` file for managing existing routes inside that specific module, providing an easy way of adding new ones by specifying the route path, the middleware that the route will use, an array of accepted methods, and the route name.

```php
$app->route(
    '/admin[/{action}[/{uuid}]]',
    AdminController::class,
    [
        RequestMethodInterface::METHOD_GET,
        RequestMethodInterface::METHOD_POST
    ],
    'admin'
 );
```

Note: the optional attributes on the route path are marked between `[]`.

### Frontend

As for the frontend toolkit, we chose to use Bootstrap 4.5.0 in combination with Fontawesome 5.0.6 for a minimalist but efficient design.

For assembling `app.js` and `app.css` along with handling packages from `package.json`, we recommend using npm 7 or up.

Our choice for listing raw data was Bootstrap Table because it is easy to implement and configurable in many ways.

Let's take the admin table as an example:

```html
<table data-toggle="table" data-url="/admin/list" data-click-to-select="true"
       data-mobile-responsive="true" data-min-width="800"
       data-check-on-init="true" data-id-field="uuid"
       data-show-refresh="true" data-show-toggle="true" data-show-columns="true"
       data-search="true" data-pagination="true" data-toolbar="#tableToolbar"
       data-silent-sort="false" data-pagination-loop="false"
       data-side-pagination="server" data-page-list="[10, 30, 50, 100, 200]"
       data-page-size="30" data-single-select="true"
       data-sort-name="created" data-sort-order="desc" id="bsTable">
           <thead>
                <tr>
                     <th data-field="checked" data-checkbox="true">Id</th>
                     <th data-field="identity" data-sortable="true">Identity</th>
                     <th data-field="firstName" data-sortable="true">First Name</th>
                     <th data-field="lastName" data-sortable="true">Last Name</th>
                     <th data-field="roles" data-sortable="false">Roles</th>
                     <th data-field="status" data-sortable="false">Status</th>
                     <th data-field="created" data-sortable="true">Created</th>
                </tr>
           </thead>
</table>
```

Here we can tweak directly in the table data how our table looks, arrange the page, and use a couple of other features like the API URL for pulling data, pagination, allowing multiple row selection, and more.

Note: you'll find in the `#tableToolbar` buttons that toggle a modal for adding/editing/deleting admins.

## FAQ

**Q: When was Dotkernel Admin V4 released, and what is it built on?**
A: Dotkernel Admin V4 was officially released on 19 July 2022. It's Dotkernel's PSR-7 Admin, an application based on Mezzio, mainly for managing and displaying tabular data from one or more database components.

**Q: What are the demo credentials for trying out Dotkernel Admin V4?**
A: Username admin and password dotadmin, on the demo at https://admin4.dotkernel.net/.

**Q: What PHP version does Dotkernel Admin V4 support?**
A: Full support for PHP 8.1, with a minimum requirement of PHP 7.4.

**Q: How do you register a new module or middleware?**
A: Register a module by adding its ConfigProvider.php in config.php. New middleware goes into pipeline.php, where you can also edit the order in which middleware runs.

**Q: What packages provide the authorization guards?**
A: dot-rbac-guard and dot-rbac work together to restrict access based on user role. authorization.global.php defines roles and their permissions, while authorization-guards.global.php restricts specific actions to those permissions.

**Q: What frontend toolkit does Dotkernel Admin V4 use for displaying tables?**
A: Bootstrap 4.5.0 combined with Fontawesome 5.0.6 for the overall design, and Bootstrap Table specifically for listing raw data because it's easy to implement and configurable in many ways.
