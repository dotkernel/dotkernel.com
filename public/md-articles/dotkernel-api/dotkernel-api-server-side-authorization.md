---
title: "Dotkernel API Server Side Authorization"
description: "How to configure server-side authorization in Dotkernel API, covering no-auth, authentication and authorization access levels, role inheritance, and route permissions."
author: "Alex Karajos"
date_published: "2019-09-05"
canonical_url: "https://www.dotkernel.com/dotkernel-api/dotkernel-api-server-side-authorization/"
category: "Dotkernel API"
language: "en"
---

# Dotkernel API Server Side Authorization

## TL;DR

Dotkernel API endpoints can be protected at three levels: no-auth, authentication, and authorization.
Access is configured in `config/autoload/authorization.local.php` under the `zend-expressive-authorization-rbac` key, using a `roles` section for role inheritance and a `permissions` section for route access.
Authentication endpoints require a valid Bearer token and return `401 Unauthorized` if it's missing, while authorization endpoints additionally check role permissions and return `403 Forbidden`.

This article covers the basic authorization of a Server Side application built using [Dotkernel API](https://github.com/dotkernel/api).

## Protecting an Endpoint

- no-auth: the resource can be accessed without the need of authentication/authorization
- authentication: the resource can be accessed only by authenticated users
- authorization: the resource can be accessed only by authenticated AND authorized users

Configuring access to the endpoints is done by editing the following config file: `config/autoload/authorization.local.php`.

> Note: If this file is missing from your application, locate its dist file `config/autoload/authorization.local.php.dist` and copy it as the above-mentioned `config/autoload/authorization.local.php`.

You should look for the array inside this config key: `zend-expressive-authorization-rbac`.

```php
'zend-expressive-authorization-rbac' => ,
        'member' => ,
        'guest'  => ,
    ],
    'permissions' => ,
    ],
]
```

Under the key roles you can define role inheritance.
In the above example:

- admin inherits from no other role: `'admin' => []`
- member inherits from admin: `'member' =>`
- guest inherits from member: `'guest' =>`

Of course, this setup is just a model, you should not use it in live projects because guests will end up having the same rights as admins.

Under the key permissions you can define which routes are accessible to a role.
In the above example, a member has access to the routes named avatar, users and user.

### 1. No-Auth Endpoints

These endpoints can be accessed without authentication/authorization.
Examples could be: login, register, contact etc.
Creating a route for such an endpoint will use only the handler(s) responsible for returning the content:

```php
$app->get('/users', UserHandler::class, 'users');
```

### 2. Endpoints Requiring Authentication

These endpoints can be accessed only if a valid `Bearer token` is present in the request headers.
Else, the API will return a `401 Unauthorized` response.
Creating a route for such an endpoint will have a structure similar to the following example:

```php
$app->get('/users', , 'users');
```

### 3. Endpoints Requiring Authorization

These endpoints can be accessed only if a valid `Bearer token` is present in the request headers.
Else, the API will return a `403 Forbidden` response.
Creating a route for such an endpoint will have a structure similar to the following example:

```php
$app->get('/users', , 'users');
```

## FAQ

**Q: What are the three access levels for protecting an endpoint?**
A: no-auth, where the resource can be accessed without authentication/authorization; authentication, where only authenticated users can access the resource; and authorization, where only authenticated AND authorized users can access it.

**Q: Where do I configure access to the endpoints?**
A: In `config/autoload/authorization.local.php`.
If that file is missing from your application, locate its dist file `config/autoload/authorization.local.php.dist` and copy it as `config/autoload/authorization.local.php`, then look for the array under the `zend-expressive-authorization-rbac` config key.

**Q: How does role inheritance work under the roles key?**
A: In the article's example, admin inherits from no other role, member inherits from admin, and guest inherits from member.
The article warns this exact setup is just a model and should not be used in live projects, because guests would end up having the same rights as admins.

**Q: How do I control which routes a role can access?**
A: Under the `permissions` key you define which routes are accessible to a role.
In the article's example, a member has access to the routes named avatar, users, and user.

**Q: What response codes are returned for authentication and authorization endpoints?**
A: Endpoints requiring authentication return a 401 Unauthorized response if a valid Bearer token isn't present in the request headers.
Endpoints requiring authorization return a 403 Forbidden response instead under the same condition.
