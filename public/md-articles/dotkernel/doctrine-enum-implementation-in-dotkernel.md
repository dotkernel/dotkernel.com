---
title: "Doctrine enum implementation in Dotkernel"
description: "How Dotkernel adopted Doctrine ORM 3.2's EnumType support to replace loosely-enforced string-based status columns with PHP enums backed by a custom DBAL type."
author: "Florin Bidirean"
date_published: "2024-11-05"
canonical_url: "https://www.dotkernel.com/dotkernel/doctrine-enum-implementation-in-dotkernel/"
category: "Dotkernel"
language: "en"
---

# Doctrine enum implementation in Dotkernel

## TL;DR

Doctrine ORM 3.2.0 added EnumType columns, building on the enum type introduced in PHP 8.1, and Dotkernel now implements this on both the PHP and database sides.
The article contrasts Dotkernel's old string-based flag columns (like `User->Status`) with a new setup that uses custom PHP enums paired with a DBAL type extending `AbstractEnumType`.
The new approach creates an explicit, enforced link between the PHP code and the database column values, at the cost of needing to update both sides whenever the value set changes.

## Doctrine's Approach

The update introduces the detection of `enumType` and `options.values` from a property with `type: Types::ENUM`.
[This PR](https://github.com/doctrine/orm/pull/11666) discusses the update and links to several older relevant issues.

### Old Setup

```php
#
class Card
{
    #
    #
    #
    public int $id;

    #],
    )]
    public Suit $suit;
}
```

### New Setup

```php
#
class Card
{
    #
    #
    #
    public int $id;

    #
    public Suit $suit;
}
```

Note that the type `Types::ENUM` part is still required if we want to have an actual `enum` column in MySQL/MariaDB.
We still default to `Types::STRING` or `Types::INTEGER` for column types with a PHP enum, as this is the more portable solution and the safer default.

## Dotkernel's Approach

### Old Setup

Dotkernel uses flags for columns like `User->Status`, but we resorted to the simpler `string` type.
The obvious disadvantage is that you can't definitively enforce a set of values for a given column.
Sure, the PHP can be set up to only use the agreed-upon set of values, but the database is independent from it.
If you edit a value manually in the database, any string is accepted.

The issue is the same on the side of the PHP code.
If the developer adds a value with a typo, it's supported, but will not work as intended.

The only advantage this setup has is the ability to easily add more values in the value set.
This may be seen as a feature, but it invites bugs in the execution.

Our old implementation defined the values like below, for the `User` entity.

```php
public const STATUS_PENDING = 'pending';
public const STATUS_ACTIVE  = 'active';
public const STATUSES       = ;
```

The column for the ORM was defined like this, as a simple string, with `pending` as its default value:

```php
#
protected string $status = self::STATUS_PENDING;
```

Obviously, the `getStatus` and `setStatus` also work with strings:

```php
public function getStatus(): string
{
    return $this->status;
}

public function setStatus(string $status): self
{
    $this->status = $status;
}
```

### New Setup

Thanks to the update of `doctrine/orm` to version 3.2.0, Dotkernel can now have a proper link between the PHP code and database values.
Now the link between the PHP code and the database is explicit and enforced.

Any update to the value set must be on both the PHP code and the database.

Let's review how the update affects the `User` entity.

In the next example, we show how to implement a value set using a custom enum.

First, we define our custom value set in `src/User/src/Enum/UserStatusEnum.php`.

```php
namespace Api\User\Enum;

enum UserStatusEnum: string
{
    case Active  = 'active';
    case Pending = 'pending';
}
```

We need to create `src/User/src/DBAL/Types/UserStatusEnumType.php` to process the new values for the `status` column.

`AbstractEnumType` must be extended by any future custom enum type.

```php
namespace Api\User\DBAL\Types;

use Api\App\DBAL\Types\AbstractEnumType;
use Api\User\Enum\UserStatusEnum;

class UserStatusEnumType extends AbstractEnumType
{
    public const NAME = 'user_status_enum';

    protected function getEnumClass(): string
    {
        return UserStatusEnum::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
```

If you create your own enum types, make sure to update the `NAME` constant and the value returned by `getEnumClass`.

Let's register the custom type in `config/autoload/doctrine.global.php` under the `types` key:

```php
'types'         =>
    UserStatusEnumType::NAME => UserStatusEnumType::class,

],
```

The filtering is updated in `src/User/src/InputFilter/Input/StatusInput.php`:

```php
$this->getFilterChain()
    ->attachByName(StringTrim::class)
    ->attachByName(StripTags::class)
    ->attach(fn($value) => $value === null ? UserStatusEnum::Active : UserStatusEnum::from($value));

$this->getValidatorChain()
    ->attachByName(InArray::class, , true);
```

The above ensures that the new `UserStatusEnum` class is used for the `status` column updates.

The `User` entity uses the new `UserStatusEnum` class.

```php
#)]
protected UserStatusEnum $status = UserStatusEnum::Pending;
```

The `status` getter and setter are also updated:

```php
public function getStatus(): UserStatusEnum
{
    return $this->status;
}

public function setStatus(UserStatusEnum $status): self
{
    $this->status = $status;
}
```

Dotkernel checks the user status during login in `src/User/src/Repository/UserRepository.php`.
If the user is not activated, the login is rejected.

```php
if ($clientEntity->getName() === 'frontend' && $result !== UserStatusEnum::Active) {
    throw new OAuthServerException(Message::USER_NOT_ACTIVATED, 6, 'inactive_user', 401);
}
```

A new user is created using the `enum` type and `pending` as the default.

```php
$user = (new User())
    ->setDetail($detail)
    ->setIdentity($data)
    ->usePassword($data)
    ->setStatus($data ?? UserStatusEnum::Pending);
```

Note the `status` column in the migration query which now looks like this:

```php
$this->addSql('
CREATE TABLE user (
    uuid BINARY(16) NOT NULL,
    identity VARCHAR(191) NOT NULL,
    password VARCHAR(191) NOT NULL,
    status ENUM(\'active\', \'pending\') DEFAULT \'pending\' NOT NULL,
    isDeleted TINYINT(1) NOT NULL,
    hash VARCHAR(64) NOT NULL,
    created DATETIME NOT NULL,
    updated DATETIME DEFAULT NULL,
    UNIQUE INDEX UNIQ_8D93D6496A95E9C4 (identity), UNIQUE INDEX UNIQ_8D93D649D1B862B8 (hash),
    PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
```

The difference for the migration query is for the `status` column, highlighted below:

```
old setup: status VARCHAR(20) NOT NULL
new setup: status ENUM(\'active\', \'pending\') DEFAULT \'pending\' NOT NULL
```

## Conclusions

The old setup used in the Dotkernel applications worked fine, but the limitations were clear as day.
There was:

- No enforcement of the value set.
- No link between the PHP code and the database.

The new setup solves both issues, ensuring more consistent flag management for your classes.

## FAQ

**Q: What update triggered this change to Dotkernel's enum handling?**
A: The update of doctrine/orm to version 3.2.0 introduced EnumType columns, building on the enum type introduced in PHP 8.1. Dotkernel implemented this new data type on both the PHP side and the database side.

**Q: What was the limitation of Dotkernel's old approach to columns like User->Status?**
A: The old setup used a simple string type, so the value set couldn't be definitively enforced. A typo in a PHP value would still be accepted, and the database was independent of any values the PHP code allowed, so manually editing a value in the database would accept any string.

**Q: What was the one advantage of the old string-based setup?**
A: It made it easy to add more values to the value set, though the article notes this ease also invites bugs in the execution.

**Q: What do you need to create to add a new custom enum type?**
A: A PHP enum class (like UserStatusEnum) plus a DBAL type class extending AbstractEnumType, which must define a NAME constant and a getEnumClass() method; the new type is then registered under the types key in config/autoload/doctrine.global.php.

**Q: Does Types::ENUM still fall back to a string or integer database column?**
A: The article notes that Doctrine still defaults to Types::STRING or Types::INTEGER for columns backed by a PHP enum, as this is considered the more portable and safer default; Types::ENUM is required if you want an actual enum column in MySQL/MariaDB.

**Q: What must happen when the value set of an enum changes under the new setup?**
A: Any update to the value set must be made on both the PHP code and the database, since the new setup creates an explicit, enforced link between them.

## Resources

- [Dotkernel API Pull Request](https://github.com/dotkernel/api/pull/339/files)
- [Doctrine Pull Request](https://github.com/doctrine/orm/pull/11666)
- [PHP Enumerations](https://www.php.net/manual/en/language.enumerations.overview.php)
