---
title: "Version Control Ignore Patterns in Zend Studio"
description: "How to configure global 'Ignored Resources' patterns in Zend Studio so they apply across all projects, instead of setting them individually per project."
author: "admin"
date_published: "2013-04-10"
canonical_url: "https://www.dotkernel.com/php-development/version-control-ignore-patterns-in-zend-studio/"
category: "PHP Development"
language: "en"
---

# Version Control Ignore Patterns in Zend Studio

## TL;DR

Zend Studio lets you manage "Ignored Resources" patterns globally, under Window -> Preferences -> Team -> Ignored Resources, instead of configuring them separately for each project.
This is especially handy when a workspace mixes Git and SVN projects, though any given project can still opt to use its own specific patterns instead of the global ones.

## Managing Global Ignored Resources

In order to globally manage the [Ignored Resources](https://www.dotkernel.com/best-practice/svn-keywords-setup-in-php-ide-zend-studio/) patterns in Zend Studio for all projects, instead of manually adding them to each project, you can do the following:

1. Go to Window -> Preferences.
2. Team -> Ignored Resources.
3. Using Add Pattern, add each ignored pattern one by one, or remove some of the already existing patterns.

This is especially useful when you have both Git and SVN projects in your workspace, and also when you tend to be less careful about your code and workspace.
Also, on each project, you can use either the global ignored patterns or specific ones.

## FAQ

**Q: How do you set global ignored resource patterns in Zend Studio?**
A: Go to Window -> Preferences, then Team -> Ignored Resources. From there, use Add Pattern to add ignored patterns one by one, applying them globally to all projects instead of adding them manually to each project, or remove existing patterns.

**Q: When is this global ignore configuration especially useful?**
A: It's especially useful when you have both Git and SVN projects in the same workspace, and also when you tend to be less careful about your code and workspace. On each project you can still choose to use either the global ignored patterns or specific ones.
