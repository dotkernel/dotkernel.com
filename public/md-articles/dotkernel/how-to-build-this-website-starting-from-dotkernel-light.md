---
title: "How to build this website starting from Dotkernel Light"
description: "How this blog itself came together: starting from the Dotkernel Light starter project, then following the Tutorial 101 to add Doctrine ORM and load real data into the database."
author: "stefan"
date_published: "2026-08-14"
canonical_url: "https://www.dotkernel.com/dotkernel/how-to-build-this-website-starting-from-dotkernel-light/"
category: "Dotkernel"
language: "en"
---

# How to build this website starting from Dotkernel Light

## TL;DR

This project started from the Dotkernel Light starter (Mezzio, Twig, FastRoute, PSR-7 via Laminas Diactoros) as a bare-bones website skeleton.
Following the official Tutorial 101, Doctrine ORM was added on top: entities for posts, categories, authors and tags, migrations to create the schema, and fixtures to load real content into the database.
The result is this: a Dotkernel Light project turned into a fully data-driven site.

## Starting from Dotkernel Light

This blog didn't start as a blog. It started as a bare [Dotkernel Light](https://docs.dotkernel.org/light-documentation/) installation — a minimal starter project built on Mezzio, with Twig for templating, FastRoute for routing, PSR-7 via Laminas Diactoros, and a PSR-11 container using Laminas Service Manager. No database, no posts, just the skeleton for a simple website.

```
git clone https://github.com/dotkernel/light.git dotkernel-light
```

At that point you have routing, templating and a working request lifecycle, but nothing to persist. Dotkernel Light is deliberately unopinionated about storage — it's up to the project to add a persistence layer if it needs one.

## Adding Doctrine, following Tutorial 101

This project needed one: categories, authors, tags, and posts, all queryable and paginated. Rather than wiring Doctrine ORM in from scratch, the official [Tutorial 101](https://docs.dotkernel.org/tutorial-101/v1/introduction/) was followed, which walks through exactly this on top of a fresh Dotkernel Light install:

1. Installing Doctrine ORM and wiring it into the container.
2. Defining entities and generating migrations to create the schema.
3. Loading fixture data and building the repositories/queries needed to list and display it.

Following that path is what turned this project's `Category`, `Author`, `Tag` and `Post` entities, their migrations, and the repositories behind every listing and category page on this site into what they are now.

## Where the data comes from

The actual content — every article, its author, its category and tags — is authored as data in `src/App/src/Fixture/articles_cleaned.json` and loaded into the database with `php bin/doctrine-fixtures`. That file wasn't written from scratch: it was built from what already existed in dotkernel.com's own database, cleaned up and reshaped into fixture data so it could be loaded into this project's schema. Doctrine takes care of turning that into rows; the entities and repositories added while following Tutorial 101 take care of turning those rows back into the pages you're browsing right now.

## FAQ

**Q: What is Dotkernel Light?**
A: A minimal Mezzio-based starter project for building a simple website — routing (FastRoute), templating (Twig) and PSR-7 (Laminas Diactoros) out of the box, without a database layer.

**Q: Why was Doctrine added on top?**
A: Dotkernel Light doesn't include persistence by default. This blog needed categories, authors, tags and posts stored and queried from a database, so Doctrine ORM was added following the official Tutorial 101.

**Q: Where does the article content come from?**
A: From `src/App/src/Fixture/articles_cleaned.json`, loaded into the database via `php bin/doctrine-fixtures`.
