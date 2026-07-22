---
title: "Creating admin accounts in DotKernel API"
description: "How to create admin accounts in DotKernel API v3 and later, using either the protected POST /admin endpoint or the admin:create terminal command."
author: "Alex Karajos"
date_published: "2021-07-09"
canonical_url: "https://www.dotkernel.com/how-to/creating-admin-accounts-in-dotkernel-api/"
category: "How to's"
language: "en"
---

# Creating admin accounts in DotKernel API

## TL;DR

Starting with version 3, DotKernel API supports dedicated admin accounts.
They can be created either through a protected API endpoint, which lets you assign one or more admin roles and optional names, or through a terminal command, which is quicker but always assigns the default admin role.
Both methods leave you with a ready-to-use admin account.

## Method 1: Using API Endpoint

This action can be performed only by an authenticated admin/superuser.
Once authenticated, call the protected endpoint `POST /admin` with the following JSON body:

```json
{
    "identity": "{IDENTITY}",
    "password": "{PASSWORD}",
    "passwordConfirm": "{PASSWORD}",
    "firstname": "{FIRSTNAME}",
    "lastname": "{LASTNAME}",
    "roles": ["{ROLE_UUID}"]
}
```

after replacing:

- {IDENTITY} with a valid username OR email address
- {PASSWORD} with a valid password
- {ROLE_UUID} with a valid admin role UUID (you can get a list of admin roles by calling the protected endpoint `GET /admin/role`)
- {FIRSTNAME} and {LASTNAME} are optional

Note: you can specify multiple admin roles under the roles key.
If the submitted data is valid, the response will be similar to the below:

```json
{
    "uuid": "d436b044-be36-11eb-9eb1-78f29ef45f43",
    "identity": "Letha_Runolfsson96",
    "firstName": "Oswald",
    "lastName": "Swift",
    "status": "active",
    "roles": ["{ROLE_UUID}"],
    "created": {
        "date": "2021-05-26 17:27:06.194613",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "updated": {
        "date": "2021-05-26 17:27:06.279705",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "_links": {
        "self": {
            "href": "http://localhost:8080/admin/d436b044-be36-11eb-9eb1-78f29ef45f43"
        }
    }
}
```

The new admin account is ready to use.

## Method 2: Using Terminal Command

Run the following command in your application's root directory:

```bash
php ./bin/cli.php admin:create -i {IDENTITY} -p {PASSWORD}
```

or

```bash
php ./bin/cli.php admin:create --identity {IDENTITY} --password {PASSWORD}
```

after replacing:

- {IDENTITY} with a valid username OR email address
- {PASSWORD} with a valid password

Note:

- if the specified identity or password contain special characters, make sure you surround them with double quote signs
- this method does not allow specifying an admin role - newly created accounts will have the role of admin

If the submitted data is valid, the outputted response is:

```
Admin account has been created.
```

The new admin account is ready to use.
You can get more help with this command by running:

```bash
php ./bin/cli.php help admin:create
```

## FAQ

**Q: What are the two ways to create an admin account in DotKernel API?**
A: You can either call the protected API endpoint `POST /admin` with a JSON body, or run the terminal command `php ./bin/cli.php admin:create`.

**Q: Who can create an admin account via the API endpoint?**
A: This action can be performed only by an authenticated admin/superuser, calling the protected `POST /admin` endpoint.

**Q: What data does the POST /admin request body need?**
A: identity, password, passwordConfirm, and roles (one or more valid admin role UUIDs, obtainable via the protected `GET /admin/role` endpoint).
firstname and lastname are optional.

**Q: What role does an admin get when created via the terminal command?**
A: The terminal command doesn't allow specifying an admin role, so newly created accounts are given the role of admin.

**Q: How do I get more help with the admin:create command?**
A: Run `php ./bin/cli.php help admin:create`.
Also note that if the identity or password contain special characters, they must be surrounded with double quote signs.
