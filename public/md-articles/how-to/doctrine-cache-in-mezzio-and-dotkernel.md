---
title: "Doctrine Cache in Mezzio and Dotkernel"
description: "How to configure Doctrine's metadata, query, and result caches (via PhpFileCache) in a Mezzio/Dotkernel application, with usage examples on a query builder and a Doctrine Paginator-based collection."
author: "admin"
date_published: "2020-09-22"
canonical_url: "https://www.dotkernel.com/how-to/doctrine-cache-in-mezzio-and-dotkernel/"
category: "How to's"
language: "en"
---

# Doctrine Cache in Mezzio and Dotkernel

## TL;DR

Running Doctrine ORM in production without any caching strategy wastes CPU cycles regenerating metadata and queries on every request.
This article configures Doctrine's `metadata_cache`, `query_cache`, and `result_cache` through psr/container, using `PhpFileCache` and a default result cache lifetime of 3600 seconds.
It walks through enabling these caches both directly on a query and on a Doctrine Paginator-based collection, with real examples from Dotkernel Admin.
Note: a 2024 follow-up article covers the same topic using Symfony Cache instead.

## Doctrine Caching in Dotkernel

> Following version 2 of doctrine/cache, in 2024 we published an update to this article here: [https://www.dotkernel.com/dotkernel/doctrine-cache-using-symfony-cache/](https://www.dotkernel.com/dotkernel/doctrine-cache-using-symfony-cache/)

Using an ORM in production without any sort of cache strategy is a very bad move.
The database server chokes; lots of CPU cycles wasted only to generate metadata and queries over and over again at each request; the system slows down and so on.
Following [Doctrine documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/caching.html#integrating-with-the-orm), we choose to configure the Doctrine cache system through psr/container.
The main parts we are going to use are `query_cache`, `metadata_cache` and `result_cache`.
As cache type we choose `PhpFileCache` and for result cache we are setting a lifetime of 3600 seconds.

## Configuration

In the file `config/autoload/doctrine.global.php` add the below entry:

```php
'doctrine' =>
        ]
    ],
'resultCacheLifetime' => 3600
```

In the file `config/autoload/local.php` add the below entry in the `doctrine` section:

```php
    'configuration' =>
    ],
```

## Metadata Cache

Your class metadata is being parsed on each request.
Instead of parsing this information we should cache it using one of the cache drivers.
We enabled this by adding the following `metadata_cache` key to our doctrine configuration:

```php
'doctrine' =>
        ]
    ]
```

## Query Cache

It is highly recommended that in a production environment you cache the transformation of a DQL query to its SQL counterpart.
It doesn't make sense to do this parsing multiple times as it doesn't change unless you alter the DQL query.
We enabled this by adding the following `query_cache` key to our doctrine configuration:

```php
'doctrine' =>
        ]
    ]
```

### Usage

```php
$query = $em->createQuery('select u from \Entities\User u');
$query->useQueryCache(true);
```

## Result Cache

The result cache can be used to cache the results of your queries so that doctrine doesn't have to query the database or hydrate the data again after the first time.
We enabled this by adding the following `result_cache` key to our doctrine configuration:

```php
'doctrine' =>
        ]
    ]
```

### Usage

```php
$query = $em->createQuery('select u from \Entities\User u');
$query->enableResultCache();
```

Note: you can set a `lifetime` for how long the result cache should live before it will be rewritten by simply passing the value as the first argument:

```php
$query->enableResultCache($resultCacheLifetime);
```

Note: you can set a custom `ID` for the result cache, which is automatically generated for you if you don't set a custom ID yourself:

```php
$query->enableResultCache($resultCacheLifetime, 'my_custom_id');
```

## Dotkernel Admin Example

Below is the code used in [Dotkernel Admin](https://github.com/dotkernel/admin) in order to [list all Admins](https://github.com/dotkernel/admin/blob/3.0/src/User/src/Repository/AdminRepository.php#L78).
It uses both `query_cache` and `result_cache`.

```php
$qb = $this->getEntityManager()->createQueryBuilder();
$qb->select('admin')
->from(Admin::class, 'admin');

if (!is_null($search)) {
$qb->where($qb->expr()->like('admin.identity', ':search'))
->setParameter('search', '%' . $search . '%');
}

$qb->setFirstResult($offset)
->setMaxResults($limit);
$qb->orderBy('admin.' . $sort, $order);

return $qb->getQuery()->useQueryCache(true)->enableResultCache($this->getCacheLifetime())->getResult();
```

## How to Return Collections

For returning collections that extend Doctrine Paginator (`Doctrine\ORM\Tools\Pagination\Paginator`), just enable result and/or query cache before passing the query builder to the collection:

```php
$qb = $this->getEntityManager()->createQueryBuilder();
$qb->select('collection')
->from(MyCollection::class, 'collection');

$qb->setFirstResult($offset)
->setMaxResults($limit);

$qb->getQuery()->enableResultCache($this->getCacheLifetime())->useQueryCache(true);

return new MyCollection($qb);
```

and the MyCollection class looks like:

```php
<?php

use Doctrine\ORM\Tools\Pagination\Paginator;

/**
* Class MyCollection
* @package Core\Collection
*/
class MyCollection extends Paginator
{
}
```

## FAQ

**Q: Is this article still current?**
A: The article notes that following version 2 of doctrine/cache, an updated version of this article covering Doctrine cache using Symfony Cache was published in 2024.

**Q: Which three Doctrine cache types does this configure?**
A: query_cache, metadata_cache and result_cache, configured through psr/container using PhpFileCache as the cache type.

**Q: What is the default result cache lifetime?**
A: A lifetime of 3600 seconds, set via `resultCacheLifetime` in `config/autoload/doctrine.global.php`.

**Q: How do I enable query cache and result cache in code?**
A: Call `$query->useQueryCache(true)` and `$query->enableResultCache()` on the query object.
`enableResultCache()` can also take a lifetime as its first argument and a custom cache ID as a second argument, otherwise one is auto-generated.

**Q: How should caching be enabled for a Doctrine Paginator-based collection?**
A: Enable result and/or query cache on the query builder's query before passing the query builder into the collection (a class extending `Doctrine\ORM\Tools\Pagination\Paginator`).

## Resources

- [Doctrine Advanced Configuration](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/advanced-configuration.html)
- [Doctrine Caching Documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/caching.html)
