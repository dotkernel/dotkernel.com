---
title: "How to implement MailChimp in Dotkernel API"
description: "A step-by-step guide to integrating MailChimp into a Dotkernel API instance using the drewm/mailchimp-api library, from installation to wiring up a factory in the ConfigProvider."
author: "Alex Karajos"
date_published: "2020-01-04"
canonical_url: "https://www.dotkernel.com/dotkernel-api/how-to-implement-mailchimp-in-dotkernel-api/"
category: "Dotkernel API"
language: "en"
---

# How to implement MailChimp in Dotkernel API

## TL;DR

This is a step-by-step guide to adding MailChimp support to a Dotkernel API instance using the `drewm/mailchimp-api` library.
It covers installing the library, creating a MailChimp config file, building a factory that returns a `DrewM\MailChimp\MailChimp` instance, and registering that factory in `ConfigProvider.php` so it can be injected wherever needed.

This article will walk you through the process of implementing MailChimp into your instance of [Dotkernel API](https://github.com/dotkernel/api) using [drewm/mailchimp-api](https://github.com/drewm/mailchimp-api).

Step 1: Add the library to your application using the following command:

```bash
composer require drewm/mailchimp-api
```

Step 2: Create configuration file `config/autoload/mailchimp.global.php` and paste the following content inside of it:

```php
<?php

declare(strict_types=1);

return
];
```

Step 3: Create factory `src/App/src/MailChimp/Factory/MailChimpFactory.php` which will return an instance of `DrewM\MailChimp`.
Paste the following content inside this file:

```php
<?php

declare(strict_types=1);

namespace Api\App\MailChimp\Factory;

use DrewM\MailChimp\MailChimp;
use Psr\Container\ContainerInterface;

/**
 * Class MailChimpFactory
 * @package Api\App\MailChimp\Factory
 */
class MailChimpFactory
{
    /**
     * @param ContainerInterface $container
     * @return MailChimp
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container) : MailChimp
    {
        $config = $container->get('config') ?? [];

        return new MailChimp($config ?? '');
    }
}
```

Step 4: Let your application use this factory by adding it to the main ConfigProvider.
To do this, open file `src/App/src/ConfigProvider.php` and locate the method called `getDependencies()`.
Inside this method, locate the key `factories` which points to an array.
Inside this array add the following line:

```php
MailChimp::class => MailChimpFactory::class,
```

Make sure you add the corresponding uses:

```php
use Api\App\MailChimp\Factory\MailChimpFactory;
use DrewM\MailChimp\MailChimp;
```

After this, you can start using the library by @Injecting `MailChimp::class` where it's needed.

## FAQ

**Q: Which library does this tutorial use to add MailChimp to Dotkernel API?**
A: The tutorial uses drewm/mailchimp-api, installed with the command composer require drewm/mailchimp-api.

**Q: Where do you place the MailChimp configuration file?**
A: In config/autoload/mailchimp.global.php, a new configuration file created as part of Step 2.

**Q: What does the MailChimpFactory class do?**
A: It's a factory, created at src/App/src/MailChimp/Factory/MailChimpFactory.php, that reads the config from the container and returns an instance of DrewM\MailChimp\MailChimp.

**Q: Where do you register the MailChimp factory so the application can use it?**
A: In src/App/src/ConfigProvider.php, inside the getDependencies() method's factories array, by mapping MailChimp::class to MailChimpFactory::class, plus adding the corresponding use statements for MailChimp and MailChimpFactory.

**Q: How do you use MailChimp once it's wired up?**
A: By injecting MailChimp::class wherever it's needed, using @Inject.
