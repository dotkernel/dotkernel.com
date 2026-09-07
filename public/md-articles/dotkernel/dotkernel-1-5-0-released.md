---
title: "Dotkernel 1.5.0 Released"
description: "Dotkernel 1.5.0 skips version 1.4 entirely and brings a switch from Dojo to jQuery, redesigned admin and frontend, model inheritance via Dot_Model, dashed controller support, and a reorganized Zend Registry."
author: "Adrian"
date_published: "2011-06-15"
canonical_url: "https://www.dotkernel.com/dotkernel/dotkernel-1-5-0-released/"
category: "Dotkernel"
language: "en"
---

# Dotkernel 1.5.0 Released

## TL;DR
After a longer wait than usual and around 250 commits, Dotkernel 1.5.0 was released, skipping 1.4 entirely due to the scale of changes.
Highlights include switching from Dojo to jQuery, a redesigned admin and frontend, model inheritance through a new Dot_Model class, support for dashed controller names, and a reorganized Zend Registry.

After a longer wait than usual, Dotkernel 1.5.0 was just released. Due to the large amount of changes and the long time spent in development, we chose to skip 1.4 and go straight to 1.5.0.

Here are a few of the many changes to Dotkernel in the latest release:

## Highlights of 1.5.0

### Switched from Dojo to jQuery

Starting with 1.5.0 we've [switched from using Dojo to jQuery](http://www.dotkernel.com/javascript/intro-to-jquery/). This doesn't mean you can't still use Dojo in your own projects, but only jQuery will be used and maintained in the Dotkernel distribution.

### New designs

We've redesigned the admin site, with new themes, and a dropdown menu, as well as a new and simpler design for the front-end.

### Model inheritance

Up until now, there was a lot of code duplication in models. For example, in the user model, you might have a *getUserById* function in the admin as well as the frontend. When you've got more models and more modules, your project can start having a lot of copy-pasted code.

To prevent this, we've introduced a *Dot_Model* class, and a way to define global models that are inherited in the admin and frontend. This way, you can have *User* class in the admin that only has methods specific to the admin module, a *User* class in the frontend that only has code specific for the frontend, and they both inherit the *Dot_Model_User* class which will have all the common code.

### Dashed controllers

We've changed the way the controller name is parsed, so that you can have controller with multiple words, split with dashes, without breaking the coding standard (for example, *www.example.com/search-article* will call *SearchArticleController.php*)

### Zend Registry reorganization

We've changed the structure of the registry, for more about this, please check [this blog post](http://www.dotkernel.com/dotkernel/zend-registry-usage-in-dotkernel/).

 

There have been about 250 commits in our SVN repository since the latest release, so we can't cover all changes in this blog post. Please [download Dotkernel 1.5.0](http://www.dotkernel.com/download/?did=33) try it out yourself and tell us what you think.

 

## FAQ

**Q: Why did Dotkernel jump from 1.3 straight to 1.5.0?**
A: Because of the large amount of changes and the long time spent in development, the team chose to skip version 1.4 and go straight to 1.5.0.

**Q: Did Dotkernel switch from Dojo to jQuery in 1.5.0?**
A: Yes. Starting with 1.5.0, Dotkernel switched from Dojo to jQuery for its own distribution, though Dojo can still be used in your own projects.

**Q: What is Dot_Model and why was it introduced?**
A: Dot_Model is a base class introduced to reduce code duplication between admin and frontend models. Both admin- and frontend-specific model classes (such as User) inherit from a shared Dot_Model_User class that holds the common code.

**Q: How does the "dashed controllers" feature work?**
A: The controller name parsing was changed so a URL like www.example.com/search-article correctly calls SearchArticleController.php, allowing multi-word controller names split with dashes without breaking the coding standard.

**Q: How much changed in the 1.5.0 release?**
A: About 250 commits went into the SVN repository since the previous release, so the blog post only covers the highlights - the full Dotkernel 1.5.0 download is available to try out.
