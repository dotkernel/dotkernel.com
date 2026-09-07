---
title: "Mezzio app development in WSL2"
description: "A step-by-step guide to installing a Mezzio application (Dotkernel API) inside WSL2, running it on Ubuntu 20.04 LTS."
author: "Alex Karajos"
date_published: "2022-09-08"
canonical_url: "https://www.dotkernel.com/php-development/mezzio-app-development-in-wsl2/"
category: "PHP Development"
language: "en"
---

# Mezzio app development in WSL2

## TL;DR
This article runs through the steps of installing a Mezzio application (Dotkernel API) in WSL2 and running it on Ubuntu 20.04 LTS, from installing WSL2 itself to configuring PHPStorm to work with the WSL2 file system.

## Install a Mezzio app (Dotkernel API) using WSL2

This article will run you through the steps of installing a Mezzio application (Dotkernel API) in **WSL2** and run it on **Ubuntu 20.04 LTS**.

### Step 1:

Make sure you have WSL2 installed on your machine by following [this guide](https://github.com/dotkernel/development/blob/main/wsl/README.md).

### Step 2:

Install **Ubuntu 20.0 LTS** inside **WLS2** as described [here](https://docs.dotkernel.org/development/v2/running/) (the current version of this guide covers AlmaLinux 9, the distro the WSL setup has since moved to).

### Step 3:

Create a virtualhost for your project using [this guide](https://docs.dotkernel.org/development/v2/virtualhosts/create-virtualhost/).

### Step 4:

Using your terminal, move into the virtualhost directory that you just created:

```
cd /home/your-username/projects/your-virtualhost
```

Install Dotkernel API, by following [this guide](https://github.com/dotkernel/api).

Make sure your `data` and `log` directories are writable by changing their permissions, as described [here](https://docs.dotkernel.org/development/v2/faq/#how-do-i-fix-common-permission-issues).

### Step 5:

Setup PHPStorm to work with WSL2 files like in [this article](https://www.jetbrains.com/help/phpstorm/how-to-use-wsl-development-environment-in-product.html)

**Note:**

> There is a guide for **AlmaLinux 8** as well, but that's not fully functional because of a well-known issue regarding running *systemd* inside **WSL2**.

## FAQ

**Q: What does this guide help you accomplish?**
A: It walks you through installing a Mezzio application (Dotkernel API) using WSL2 and running it on Ubuntu 20.04 LTS.

**Q: What do you need before installing Ubuntu inside WSL2?**
A: You first need WSL2 installed on your machine, which you can set up by following the linked WSL2 installation guide, before installing Ubuntu inside it.

**Q: How do you set up a virtual host for the project?**
A: After Ubuntu is installed inside WSL2, you create a virtual host for your project by following the linked virtual-host creation guide.

**Q: How do you install Dotkernel API once the virtual host is ready?**
A: Using your terminal, move into the virtual host directory you created (for example, cd /home/your-username/projects/your-virtualhost), then install Dotkernel API by following its guide. Afterward, make sure the data and log directories are writable by changing their permissions, as described in the linked permission-fix guide.

**Q: How do you get PHPStorm to work with the WSL2 project files?**
A: You set up PHPStorm to work with WSL2 files by following the linked JetBrains article on using a WSL development environment in the product.

**Q: Does this guide also work for AlmaLinux instead of Ubuntu?**
A: There is a guide for AlmaLinux 8 as well, but it is not fully functional because of a well-known issue regarding running systemd inside WSL2.
