---
title: "DotKernel 1.5.0 Released"
description: "DotKernel 1.5.0 skips version 1.4 entirely and brings a switch from Dojo to jQuery, redesigned admin and frontend, model inheritance via Dot_Model, dashed controller support, and a reorganized Zend Registry."
author: "Adrian"
date_published: "2011-06-15"
canonical_url: "https://www.dotkernel.com/dotkernel/dotkernel-1-5-0-released/"
category: "Dotkernel"
language: "en"
---

# DotKernel 1.5.0 Released

## TL;DR

After a longer wait than usual and around 250 commits, DotKernel 1.5.0 was released, skipping 1.4 entirely due to the scale of changes. Highlights include switching from Dojo to jQuery, a redesigned admin and frontend, model inheritance through a new Dot_Model class, support for dashed controller names, and a reorganized Zend Registry.

## Why skip straight to 1.5.0?

Due to the large amount of changes and the long time spent in development, the team chose to skip 1.4 and go straight to 1.5.0.

## Highlights of 1.5.0

### Switched from Dojo to jQuery

Starting with 1.5.0, DotKernel switched from using Dojo to jQuery. Dojo can still be used in your own projects, but only jQuery is used and maintained in the DotKernel distribution itself.

### New designs

The admin site was redesigned, with new themes and a dropdown menu, along with a new and simpler design for the front-end.

### Model inheritance

Previously there was a lot of code duplication in models — for example, a `getUserById` function might exist separately in both the admin and frontend User models. To solve this, a `Dot_Model` class was introduced along with a way to define global models inherited by both admin and frontend. A `User` class in the admin only holds admin-specific methods, a `User` class in the frontend only holds frontend-specific methods, and both inherit a shared `Dot_Model_User` class containing the common code.

### Dashed controllers

The way controller names are parsed was changed so that controllers with multiple words, split with dashes, work without breaking the coding standard. For example, `www.example.com/search-article` calls `SearchArticleController.php`.

### Zend Registry reorganization

The structure of the registry was changed; more details are covered in a separate blog post on Zend Registry usage in DotKernel.

## Scale of the release

There were about 250 commits in the SVN repository since the previous release, so the blog post could not cover every change.

## FAQ

**Q: Why did DotKernel jump from 1.3 straight to 1.5.0?**
A: Because of the large amount of changes and the long time spent in development, the team chose to skip version 1.4 and go straight to 1.5.0.

**Q: Did DotKernel switch from Dojo to jQuery in 1.5.0?**
A: Yes. Starting with 1.5.0, DotKernel switched from Dojo to jQuery for its own distribution, though Dojo can still be used in your own projects.

**Q: What is Dot_Model and why was it introduced?**
A: Dot_Model is a base class introduced to reduce code duplication between admin and frontend models. Both admin- and frontend-specific model classes (such as User) inherit from a shared Dot_Model_User class that holds the common code.

**Q: How does the "dashed controllers" feature work?**
A: The controller name parsing was changed so a URL like www.example.com/search-article correctly calls SearchArticleController.php, allowing multi-word controller names split with dashes without breaking the coding standard.

**Q: How much changed in the 1.5.0 release?**
A: About 250 commits went into the SVN repository since the previous release, so the blog post only covers the highlights - the full DotKernel 1.5.0 download is available to try out.

## Resources

- Intro to jQuery: http://www.dotkernel.com/javascript/intro-to-jquery/
- Zend Registry usage in DotKernel: http://www.dotkernel.com/dotkernel/zend-registry-usage-in-dotkernel/
- DotKernel 1.5.0 download: http://www.dotkernel.com/download/?did=33
