---
title: "Doctrine cache using symfony/cache"
description: "How to enable and configure the dotkernel/dot-cache component, a wrapper around symfony/cache, to cache Doctrine's result, metadata, query, and hydration data in Dotkernel Admin."
author: "MarioRadu"
date_published: "2024-02-27"
canonical_url: "https://www.dotkernel.com/dotkernel/doctrine-cache-using-symfony-cache/"
category: "Dotkernel"
language: "en"
---

# Doctrine cache using symfony/cache

## TL;DR

Caching stores data the first time it's requested so that later requests can be served from the cache instead of the original, slower source, which improves response times.
This article, a follow-up to an earlier caching article, shows how to enable the dot-cache component, a wrapper around symfony/cache, in Dotkernel Admin.
It covers the array and filesystem storage adapters, configuring Doctrine's four cache types (result, metadata, query, hydration), and marking entities and queries as cacheable.

## Installation

Run the following command in your project directory:

```bash
composer require dotkernel/dot-cache
```

After installing, add the `DotCacheConfigProvider::class` class to your configuration aggregate (config/config.php).
Before continuing with the configuration process, it helps to know a few things about how and where the data is stored.
The [dotkernel/dot-cache](https://packagist.org/packages/dotkernel/dot-cache) component is a wrapper that sits on top of [symfony/cache](https://packagist.org/packages/symfony/cache).
It currently supports two adapters and can store data in two distinct locations:

- array - stores data in-memory
- filesystem - stores data on local disk files

1. Storing data in-memory is the fastest and sometimes the cheapest caching mechanism, but it also comes with down-sides.
Storing everything in RAM memory is not the best idea when your application is running on a low memory system.
In this case you should consider using the filesystem mechanism.

2. The second caching mechanism involves storing data into files on the local disk, known as the filesystem option.
While this option may be slightly slower than the first one, it provides a more persistent storage solution.

Feel free to explore and use other adapters from [symfony/cache](https://packagist.org/packages/symfony/cache) by checking the [official documentation](https://symfony.com/doc/current/components/cache.html#advanced-usage).

## Configuration

In `config/autoload/doctrine.global.php`, in the `doctrine.configuration.orm_default` key add the following entry:

```php
'result_cache'       => 'filesystem',
'metadata_cache'     => 'filesystem',
'query_cache'        => 'filesystem',
'hydration_cache'    => 'array',
'second_level_cache' => ,
],
```

Next, under the `doctrine` key add the following items:

```php
'cache' => ,
    'filesystem' => ,
],
```

The result is that the metadata and query cache will be stored in the `data/cache/doctrine` folder and the hydration cache will be stored in-memory.
Each system is unique, requiring customized configurations.
Make sure to identify the specific configuration requirements for your application.
Doctrine cache is divided into 4 different types:

- `result_cache`
- `metadata_cache`
- `query_cache`
- `hydration_cache`

### Result Cache

The result cache can be used to store the results of your queries, enabling Doctrine to avoid querying the database or hydrating the data again after the initial retrieval.

### Metadata Cache

Parsing your class metadata on every request is inefficient.
Instead, it's advisable to cache this information using one of the available cache adapters.

### Query Cache

In a production environment, it's strongly recommended to cache the resulting DQL query into its SQL equivalent.
Since the query doesn't change unless the DQL query itself changes, it's unnecessary to parse it multiple times.

### Hydration Cache

Doctrine hydration cache is a feature that stores the results of data hydration, which is the process of converting raw database data into usable objects or arrays.
By caching these results, it avoids repeating the hydration process for repeated queries, improving performance.

## How to Use

To enable caching for entities, you need to add the `#` attribute like in the following example:

```php
#
#
#
class Admin extends AbstractEntity implements AdminInterface
{
}
```

For further details about the cache mode please refer to the [official documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/3.0/reference/second-level-cache.html).
When querying data, you can have Doctrine cache your results.
You do this by calling the `setCacheable` method on the query builder.

```php
$this->getQueryBuilder()
     ->select('admin')
     ->from(Admin::class, 'admin')
     ->setCacheable(true)
     ->getQuery()
     ->getResult();
```

Caching is not limited to entities alone.
Objects can be cached too.
Check the [basic cache usage](https://symfony.com/doc/current/components/cache.html#basic-usage-psr-6) for this purpose.
In conclusion, cache plays a vital role in optimizing system performance and improving user experience by storing frequently accessed data.
As technology continues to evolve, caching mechanisms will remain an integral part of modern computing architectures, driving faster access to data and smoother user interactions across various digital platforms.

## FAQ

**Q: What component does this article use for caching Doctrine data?**
A: The dotkernel/dot-cache component, a wrapper that sits on top of symfony/cache. It's installed with composer require dotkernel/dot-cache and registered by adding DotCacheConfigProvider::class to the configuration aggregate.

**Q: What storage adapters does dot-cache currently support?**
A: Two: array, which stores data in-memory and is the fastest option but uses more RAM, and filesystem, which stores data in local disk files and is slightly slower but more persistent.

**Q: What are the four types of Doctrine cache covered?**
A: result_cache, metadata_cache, query_cache, and hydration_cache, each configured under the doctrine.configuration.orm_default key.

**Q: What does the result cache do?**
A: It stores the results of queries, letting Doctrine avoid querying the database or hydrating the data again after the initial retrieval.

**Q: Why cache class metadata?**
A: Because parsing class metadata on every request is inefficient, so it's advisable to cache it using one of the available cache adapters.

**Q: How do you mark an entity or a query as cacheable?**
A: To enable caching for entities you add a caching attribute to the entity class; to cache an individual query, call setCacheable(true) on the query builder before getResult().
