---
title: "Implementation of SEO friendly URL in an generic Laminas Mezzio app"
description: "How to add human-readable, SEO-friendly URL slugs to a Mezzio/Doctrine application using the gedmo/doctrine-extensions package."
author: "MarioRadu"
date_published: "2023-06-30"
canonical_url: "https://www.dotkernel.com/how-to/implementation-of-seo-friendly-url-in-an-generic-laminas-mezzio-app/"
category: "How to's"
language: "en"
---

# Implementation of SEO friendly URL in an generic Laminas Mezzio app

## TL;DR

Human-readable URL slugs improve readability, SEO, and shareability compared to raw numeric IDs in URLs.
This article shows how to add slug support to a Mezzio application using the gedmo/doctrine-extensions package.
It covers installing the package via Composer, registering its SluggableListener with Doctrine, and adding a slug column generated from an existing field (such as identity) via the `@Gedmo\Slug` annotation.

Prerequisites:

- [Mezzio App](https://docs.mezzio.dev/)
- [Doctrine](https://www.doctrine-project.org/)

In the vast digital landscape of the internet, where websites compete for attention, having a well-crafted URL can make a significant difference.
By incorporating human-readable slugs into website URLs, we can enhance user experience, improve search engine optimization (SEO), and foster better engagement.
In this article, we discuss the importance, benefits and how to implement human-readable URLs.
The first 3 reasons why we should consider implementing slugs:

- Readability and User-Friendly Experience
- Search Engine Optimization (SEO)
- Shareability and Trustworthiness

## How to Implement Slugs into Dotkernel, a Practical Example

Let's consider we want to implement a feature that gives users the possibility to view other users' profiles.
When viewing another user's profile we have this URL: website.com/user/11, and this doesn't look so appealing, am I right?
So, the solution is to format the URL in a human-readable way, like this: website.com/user/john-doe.
To implement slugs into our codebase we are going to use a popular package, [gedmo/doctrine-extensions](https://packagist.org/packages/gedmo/doctrine-extensions).
First, we need to install the package via composer by running:

```bash
composer require gedmo/doctrine-extensions
```

After installing, we need to register the package's event listener into doctrine.
To do this, add the following code block in your doctrine global configuration.
In our case it would be in the `doctrine.global.php` file.

```php
'doctrine' => [
    'event_manager' => [
        'orm_default' => [
            'subscribers' => [
                Gedmo\Sluggable\SluggableListener::class,
            ]
        ]
    ],
]
```

This is all the configuration you will need, the next step is to create a new database column that will be our slug.
We will add the column in the `src/User/src/Entity/User.php` entity and run the migrations.

```php
use Gedmo\Mapping\Annotation as Gedmo;

...

/**
 * @ORM\Column(name="identity", type="string", length=64, nullable=false, unique=true)
 */
protected string $identity;

/**
 * @ORM\Column(name="slug", type="string", length=64, nullable=false, unique=true)
 * @Gedmo\Slug(fields={"identity"})
 */
protected string $slug;
```

When generating the slug we can specify what field(s) to be used; in our case the slug will be generated using the 'identity' field.
Now you can create a new user and see that the slug column was autocompleted.
To wrap things up, human-readable URLs are a must nowadays, thanks to their swift implementation and multiple benefits.

## FAQ

**Q: What are the benefits of human-readable slugs cited in this article?**
A: Readability and a more user-friendly experience, better search engine optimization (SEO), and improved shareability and trustworthiness of the URL.

**Q: What package is used to generate slugs in this example?**
A: gedmo/doctrine-extensions, installed by running `composer require gedmo/doctrine-extensions`.

**Q: How is the slug listener registered with Doctrine?**
A: By adding `Gedmo\Sluggable\SluggableListener::class` under the `doctrine.event_manager.orm_default.subscribers` configuration, e.g. in `doctrine.global.php`.

**Q: How is the slug field configured on the entity in this example?**
A: A new slug column is added (e.g. to `src/User/src/Entity/User.php`) annotated with `@Gedmo\Slug(fields={"identity"})`, so the slug is generated automatically from the identity field, for example when a new user is created.
