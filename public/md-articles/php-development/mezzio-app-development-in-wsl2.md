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

## Steps

1. Make sure WSL2 is installed on your machine, following the [WSL2 installation guide](https://github.com/dotkernel/development/blob/main/wsl/README.md).
2. Install Ubuntu 20.04 LTS inside WSL2, as described in the Ubuntu 20 setup guide.
3. Create a virtual host for your project using the virtual hosts guide.
4. Using your terminal, move into the virtual host directory you just created:

   ```shell
   cd /home/your-username/projects/your-virtualhost
   ```

Install Dotkernel API by following the [Dotkernel API guide](https://github.com/dotkernel/api).
Make sure the `data` and `log` directories are writable by changing their permissions, as described in the common permission issues guide.
5. Set up PHPStorm to work with WSL2 files, as described in the [JetBrains WSL development environment article](https://www.jetbrains.com/help/phpstorm/how-to-use-wsl-development-environment-in-product.html).

## Note

There is a guide for AlmaLinux 8 as well, but it is not fully functional because of a well-known issue regarding running systemd inside WSL2.

## FAQ

**Q: What does this guide help you accomplish?**
A: It walks you through installing a Mezzio application (Dotkernel API) using WSL2 and running it on Ubuntu 20.04 LTS.

**Q: What do you need before installing Ubuntu inside WSL2?**
A: You first need WSL2 installed on your machine, which you can set up by following the linked WSL2 installation guide, before installing Ubuntu inside it.

**Q: How do you set up a virtual host for the project?**
A: After Ubuntu is installed inside WSL2, you create a virtual host for your project by following the linked virtual-host creation guide.

**Q: How do you install Dotkernel API once the virtual host is ready?**
A: Using your terminal, move into the virtual host directory you created (for example, `cd /home/your-username/projects/your-virtualhost`), then install Dotkernel API by following its guide.
Afterward, make sure the `data` and `log` directories are writable by changing their permissions, as described in the linked permission-fix guide.

**Q: How do you get PHPStorm to work with the WSL2 project files?**
A: You set up PHPStorm to work with WSL2 files by following the linked JetBrains article on using a WSL development environment in the product.

**Q: Does this guide also work for AlmaLinux instead of Ubuntu?**
A: There is a guide for AlmaLinux 8 as well, but it is not fully functional because of a well-known issue regarding running systemd inside WSL2.

## Resources

- [WSL2 installation guide](https://github.com/dotkernel/development/blob/main/wsl/README.md)
- Ubuntu 20 setup inside WSL2
- Create virtual hosts guide
- [Dotkernel API installation guide](https://github.com/dotkernel/api)
- Fix common permission issues
- [Using WSL development environment in PHPStorm](https://www.jetbrains.com/help/phpstorm/how-to-use-wsl-development-environment-in-product.html)
