---
title: "PHP Environment : Development Staging Production"
description: "An overview of the development, staging and production server environments used to improve the development, testing and release process of client-server applications."
author: "Teo"
date_published: "2010-07-30"
canonical_url: "https://www.dotkernel.com/php-development/php-environment-development-staging-production/"
category: "PHP Development"
language: "en"
---

# PHP Environment : Development Staging Production

## TL;DR
In hosted software development, an environment is a server tier designated to a specific stage of a release process.
The three most common environments are Development, Staging and Production, and applications are typically moved between them using Subversion source control.

In hosted software development, the environment refers to a server tier designated to a specific stage in a release process. The purpose of these environments is to improve the development, testing and release processes in client-server applications. The most common environments are described below.

## Development

This is where the software is developed – it’s *the working environment for individual developers* or small teams. The purpose of this environment is for the developer to work on local host, separate from the rest of the team, allowing them to make various changes without worrying that it may alter the work of the other members of the team.

## Staging

It is *used to assemble, test and review the application before it goes into production*. Usually the staging environment tries to simulate as much as possible the production environment (hardware and software-wise). Normally, before releasing an update version of the application on the production environment, the update must be tested on the staging environment. This environment can also be used as a demonstration/training environment.

## Production

It is *the “live” environment*, where the final application goes out to the world and becomes active.

To switch from one environment to another use the ***Subversion*** *source code*.

Using SVN on Aptana is an article that explains how to set your *development* environment on your local computer and then to change it on your *staging* environment.

To better understand the development of an application using environments, check this helpful article [http://dltj.org/article/software-development-practice/](http://dltj.org/article/software-development-practice/)

## FAQ

**Q: What is a "server environment" in hosted software development?**
A: It refers to a server tier designated to a specific stage in a release process. The purpose of using different environments is to improve the development, testing and release processes in client-server applications.

**Q: What is the development environment used for?**
A: It's the working environment for individual developers or small teams, where the developer works on local host, separate from the rest of the team, allowing changes to be made without worrying about altering the work of other team members.

**Q: What is the staging environment used for?**
A: It's used to assemble, test and review the application before it goes into production, and it usually tries to simulate the production environment as closely as possible, both hardware- and software-wise. Before releasing an update to production, it must normally be tested on staging first, which can also serve as a demonstration or training environment.

**Q: What is the production environment?**
A: It's the "live" environment, where the final application goes out to the world and becomes active.

**Q: How do you switch an application from one environment to another?**
A: The article recommends using Subversion source code to switch from one environment to another, and points to the "Using SVN on Aptana" article for details on setting up a development environment locally and then moving it to a staging environment.
