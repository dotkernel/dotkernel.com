---
title: "Zend_Auth and Zend_Acl integrated in Dotkernel"
description: "How Zend_Auth and Zend_Acl were integrated into Dotkernel 1.5.0 through the Dot_Auth and Dot_Acl classes for user authentication and access control."
author: "Teo"
date_published: "2011-06-16"
canonical_url: "https://www.dotkernel.com/dotkernel/zend-auth-and-zend-acl-integrated-in-dotkernel/"
category: "Dotkernel"
language: "en"
---

# Zend_Auth and Zend_Acl integrated in Dotkernel

## TL;DR

[Zend_Auth](http://framework.zend.com/manual/en/zend.auth.html) and [Zend_Acl](http://framework.zend.com/manual/en/zend.acl.html) have been integrated into Dotkernel starting with version 1.5.0.
The User and Admin models were completely refactored using the new `Dot_Auth` and `Dot_Acl` classes for authentication and access control.

## Dot_Auth

The `Dot_Auth` class authenticates the user by checking the database, using `Zend_Auth_Adapter_DbTable`:

```php
private function _getAuthAdapter($who)
    {
        $dbAdapter = Zend_Registry::get('database');
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $authAdapter->setTableName($who)
            ->setIdentityColumn('username')
            ->setCredentialColumn('password');
        return $authAdapter;
    }
```

## ACL roles and resources

ACL roles (user types) and permissions are configured in the `configs/acl/role.xml` file.
In Dotkernel there are 3 roles: `guest`, `user`, and `admin`.
The resources for ACL are taken from `configs/router.xml` — see the `controllers` tag.

## Dot_Acl

The `Dot_Acl` class:

- Controls user access (ACL — Access Controller Layer).
- Is used for setting and checking the permissions of a user.
- Uses Zend_Acl for checking if a role (user) has access to a resource (controller).

```php
// instantiate Zend_Acl
$this->acl = new Zend_Acl();
```

```php
public function isAllowed($role)
    {
        $resource = $this->requestControllerProcessed;
        $privillege = $this->requestAction;
        if(!$this->acl->has($resource))
        {
            return FALSE;
        }
        else
        {
            return $this->acl->isAllowed($role, $resource, $privillege);
        }
    }
```

`Dot_Auth` calls the `isAllowed` method from `Dot_Acl` which authenticates the user.
`IndexController.php` calls `Dot_Auth`:

```php
$dotAuth = Dot_Auth::getInstance();
$dotAuth->checkIdentity('user');
```

## FAQ

**Q: When were Zend_Auth and Zend_Acl integrated into Dotkernel?**
A: Zend_Auth and Zend_Acl were integrated starting with Dotkernel version 1.5.0, as part of a refactor of the User and Admin models using the new Dot_Auth and Dot_Acl classes.

**Q: How does Dot_Auth authenticate a user?**
A: Dot_Auth authenticates the user by checking the database using Zend_Auth_Adapter_DbTable, setting the table name, the identity column to username, and the credential column to password.

**Q: What roles exist for ACL in Dotkernel?**
A: Dotkernel defines 3 ACL roles (user types): guest, user, and admin.
These roles and their permissions are configured in the configs/acl/role.xml file.

**Q: Where do the ACL resources come from?**
A: The resources for ACL are taken from configs/router.xml, specifically the controllers tag.

**Q: What does the Dot_Acl class do?**
A: Dot_Acl controls user access (Access Controller Layer), is used for setting and checking a user's permissions, and uses Zend_Acl to check whether a role has access to a resource.
Dot_Auth calls the isAllowed method from Dot_Acl to authenticate the user.

## Resources

- Zend_Auth manual: http://framework.zend.com/manual/en/zend.auth.html
- Zend_Acl manual: http://framework.zend.com/manual/en/zend.acl.html
