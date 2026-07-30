---
title: "Database seeding: Doctrine data fixtures vs Phinx"
description: "Why Dotkernel moved database seeding from Phinx to doctrine/data-fixtures, the CLI package built to fill Doctrine's missing command-line interface, and how to install, use, and order fixtures."
author: "MarioRadu"
date_published: "2022-08-31"
canonical_url: "https://www.dotkernel.com/php-development/database-seeding-doctrine-data-fixtures-vs-phinx/"
category: "PHP Development"
language: "en"
---

# Database seeding: Doctrine data fixtures vs Phinx

## TL;DR

Dotkernel 3 previously used cakephp/phinx for seeding the database, but the team wanted more flexibility and switched to doctrine/data-fixtures since Doctrine is already the ORM in use.
Because doctrine/data-fixtures has no CLI interface, Dotkernel built the dotkernel/dot-data-fixtures package to add one, and this article covers installing it, creating and executing fixtures, and ordering them by explicit order or by declared dependencies.

## Database Seeding: Doctrine Data Fixtures vs Phinx

Seeding the database means populating the database with initial values, it's commonly used for seeding the user roles and user accounts.
Seeding the database the right way is no easy feat, and we will see why.

Previous versions of Dotkernel 3 used [cakephp](https://github.com/cakephp)/[phinx](https://github.com/cakephp/phinx) for seeding the database.
While the package did a great job at populating the database, we wanted more.
We wanted more flexibility - so we started to search for alternatives.

Because we are using Doctrine as our database abstraction layer, the obvious choice was to give [doctrine/data-fixtures](https://github.com/doctrine/data-fixtures) a shot and use it as our database seeder, instead of phinx - but there's a catch.

## The Catch

Using the [doctrine/data-fixtures](https://github.com/doctrine/data-fixtures) package provides a concrete implementation of data fixtures, without a CLI interface.
We need a way to interact with the fixtures, so we created a package ([dotkernel/dot-data-fixtures](https://github.com/dotkernel/dot-data-fixtures)) to provide a CLI interface.
While there are alternatives that can achieve this out-of-the-box we wanted something slim (in terms of dependencies) and easy to use.

### Note

The package [dotkernel/dot-data-fixtures](https://github.com/dotkernel/dot-data-fixtures) does NOT depend on other Dotkernel packages.
The only dependency is Doctrine.

## Installation

Run the following command in your project directory:

```bash
composer require dotkernel/dot-data-fixtures
```

Register the package's `ConfigProvider.php` in `config/config.php`.

```php
\Dot\DataFixtures\ConfigProvider::class,
```

In `doctrine.global.php` (or your custom doctrine config file) add a new key `fixtures`, in the `doctrine` array, the value should be a valid path to a folder where your fixtures can be found.

```php
return [
    'dependencies' => [ ... ],
    'doctrine' => [
        ...,
        'fixtures' => getcwd() . '/data/doctrine/fixtures',
    ],
];
```

Make sure the path is valid before proceeding to the next step.
The fixtures can be found in the `/data/doctrine/fixtures` folder, but you can create a custom folder for them.
We choose this location because the migrations live in the `/data/doctrine/migrations` folder.

The last step is to register 2 commands.
We will register the commands to work with the doctrine default CLI but you can register them as normal commands also.
Create a new php file `bin/doctrine` if you don't have it already and paste the below code block.

```php
<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

require_once 'vendor/autoload.php';

$container = require getcwd() . '/config/container.php';

$entityManager = $container->get(\Doctrine\ORM\EntityManager::class);

$commands = [
    $container->get(Dot\DataFixtures\Command\ExecuteFixturesCommand::class),
    $container->get(Dot\DataFixtures\Command\ListFixturesCommand::class),
];

ConsoleRunner::run(
    new SingleManagerProvider($entityManager),
    $commands
);
```

The installation is complete, we can verify it by running the following command in our terminal.

```bash
php bin/console
```

It should print out all the doctrine CLI commands available, including our fixtures commands.

## Usage

### List All Available Fixtures, by Order of Execution

```bash
php bin/doctrine fixtures:list
```

By using this command you can check the execution order of your fixtures before executing them.

### Executing Fixtures

To execute all fixtures run: `php bin/doctrine fixtures:execute`
To run a specific fixture run: `php bin/doctrine fixtures:execute --class={FixtureClassName}`
Example:

```bash
php bin/console fixtures:execute --class=RoleLoader
```

## Creating Fixtures

When creating fixtures, we need to:

- Create the fixture in the configured folder (`/data/doctrine/fixtures`).
- Implement `Doctrine\Common\DataFixtures\FixtureInterface` interface.

Example:

```php
<?php

namespace Frontend\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Frontend\User\Entity\UserRole;

/**
 * Class RoleLoader
 * @package Frontend\Fixtures
 */
class RoleLoader implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $adminRole = new UserRole();
        $adminRole->setName('admin');

        $userRole = new UserRole();
        $userRole->setName('user');

        $guestRole = new UserRole();
        $guestRole->setName('guest');

        $manager->persist($adminRole);
        $manager->persist($userRole);
        $manager->persist($guestRole);

        $manager->flush();
    }
}
```

## Ordering Fixtures

We can order fixtures using 2 methods:

1. By Order - you can specify the order of execution, by implementing `OrderedFixtureInterface` interface.
2. By dependencies - lets you specify dependency fixtures, chaining fixtures and executing them in the right order.

Practical example:
Requirements: Seed the database with a new admin user.

We will use the second method to order fixtures and need 2 fixtures to achieve this, one of them will create a new user and the other will create a new admin role.
In this case the order matters, we can't create the admin user without having an admin role.

Create new php file in `data/doctrine/fixtures` with the name `RoleLoader.php`.
This fixture will be executed first and create our user roles.

```php
<?php

namespace Frontend\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Frontend\User\Entity\UserRole;

/**
 * Class RoleLoader
 * @package Frontend\Fixtures
 */
class RoleLoader implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $adminRole = new UserRole();
        $adminRole->setName('admin');

        $userRole = new UserRole();
        $userRole->setName('user');

        $guestRole = new UserRole();
        $guestRole->setName('guest');

        $manager->persist($adminRole);
        $manager->persist($userRole);
        $manager->persist($guestRole);

        $manager->flush();
    }
}
```

The second fixture is `UserLoader.php` and will contain the following code:

```php
<?php

namespace Frontend\Fixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserDetail;
use Frontend\User\Entity\UserRole;

class UserLoader implements FixtureInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setIdentity('admin@test.com');
        $user->setPassword(password_hash('admin', PASSWORD_DEFAULT));
        $user->setIsDeleted(false);
        $user->setHash('hash');

        $userDetail = new UserDetail();
        $userDetail->setUser($user);
        $userDetail->setFirstName('Admin');

        $user->setDetail($userDetail);

        $roleRepository = $manager->getRepository(UserRole::class);

        /** @var UserRole $adminRole */
        $adminRole = $roleRepository->findOneBy(['name' => UserRole::ROLE_ADMIN]);

        $user->addRole($adminRole);

        $manager->persist($user);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
           RoleLoader::class
        ];
    }
}
```

Notice how `UserLoader.php` implements 2 interfaces, `FixtureInterface` and `DependentFixtureInterface`.
The method `getDependencies()` returns an array containing the dependencies (fixtures) that need to be executed prior to the current one.
After running all the fixtures using `php bin/doctrine fixtures:execute` the output should look like this:
`RoleLoader` was executed before `UserLoader` because `UserLoader` had `RoleLoader` as a dependency.

To wrap things up, we implemented a database seeder and saw a practical example of how to use it.
More details about Fixtures in this blogpost: [https://matthiasnoback.nl/2018/07/about-fixtures/](https://matthiasnoback.nl/2018/07/about-fixtures/).

## FAQ

**Q: Why did Dotkernel move from Phinx to doctrine/data-fixtures for seeding?**
A: Previous versions of Dotkernel 3 used cakephp/phinx for seeding, but the team wanted more flexibility, so they evaluated doctrine/data-fixtures since Doctrine is already used as the database abstraction layer.

**Q: What's the catch with doctrine/data-fixtures?**
A: It provides a concrete implementation of data fixtures, but without a CLI interface, so Dotkernel built the dotkernel/dot-data-fixtures package to add one.

**Q: Does dotkernel/dot-data-fixtures depend on other Dotkernel packages?**
A: No, its only dependency is Doctrine.

**Q: How do you install dotkernel/dot-data-fixtures?**
A: Run `composer require dotkernel/dot-data-fixtures`, register its ConfigProvider in `config/config.php`, add a `fixtures` key pointing to your fixtures folder in your doctrine config, and register the ExecuteFixturesCommand and ListFixturesCommand in a `bin/doctrine` CLI file.

**Q: How do you list and execute fixtures?**
A: Use `php bin/doctrine fixtures:list` to see the execution order, `php bin/doctrine fixtures:execute` to run all fixtures, or `php bin/doctrine fixtures:execute --class={FixtureClassName}` to run a specific one.

**Q: How can fixtures be ordered?**
A: Either by implementing `OrderedFixtureInterface` to specify an explicit order, or by implementing `DependentFixtureInterface` and its `getDependencies()` method to declare which fixtures must run first.
