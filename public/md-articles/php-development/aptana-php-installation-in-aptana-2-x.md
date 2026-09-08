---
title: "Aptana PHP installation in Aptana 2.x"
description: "A quick fix for restoring PHP support in Aptana 2.x after the bundled Aptana PHP plugin was discontinued in favor of PDT, plus adding SVN support via Subclipse."
author: "admin"
date_published: "2009-12-01"
canonical_url: "https://www.dotkernel.com/php-development/aptana-php-installation-in-aptana-2-x/"
category: "PHP Development"
language: "en"
---

# Aptana PHP installation in Aptana 2.x

## TL;DR

Aptana discontinued its bundled Aptana PHP plugin in Aptana 2.x in favor of PDT, but PDT is missing major features needed for professional PHP development.
This article shows how to manually reinstall the Aptana PHP plugin through Aptana's update site, and how to add SVN support via Subclipse if it isn't already installed.

As all [aptana](http://www.aptana.org/) fans know, [Aptana PHP](http://www.aptana.org/php) plugin was discontinued in Aptana 2.x, in favor of PDT.
But PDT is a joke, not suitable for professional PHP development, major features are missing.
So if you want to continue using Aptana PHP, that's what need to be done:

1. Aptana -> Help -> Install New Software
2. Add http://update.aptana.com/install/php
3. Then select Aptana PHP and install it.

In case you don't have yet a SVN plugin, go to

1. Aptana -> Help -> Install Aptana Features
2. Others -> Subclipse
3. Follow the instructions
