---
title: "Installing AlmaLinux 10 in WSL2: PHP, MariaDB, Composer, PhpMyadmin"
description: "A recipe for setting up a full PHP development environment on AlmaLinux 10 under WSL2, covering installation, requirements, and running applications directly or via virtual hosts."
author: "Florin Bidirean"
date_published: "2025-06-19"
canonical_url: "https://www.dotkernel.com/how-to/installing-almalinux-10-in-wsl2-php-mariadb-composer-phpmyadmin/"
category: "How to's"
language: "en"
---

# Installing AlmaLinux 10 in WSL2: PHP, MariaDB, Composer, PhpMyadmin

## TL;DR

With the release of AlmaLinux OS 10, Dotkernel created a new WSL2 development environment recipe offering performance, security, and hardware improvements over AlmaLinux 9.
The recipe sets up WSL2, AlmaLinux 10, PHP, Apache, MariaDB, Git, Composer, Node.js, and PhpMyAdmin.
It also covers the OS/hardware requirements, installing the distro, and running PHP projects directly or via virtual hosts.

## What You Get

Like for AlmaLinux 9, the WSL recipe sets up the development environment with all the components a regular PHP developer needs:

- WSL2 - Windows Subsystem for Linux.
- AlmaLinux 10 - Free and open source Linux distribution.
- PHP - General-purpose scripting language geared towards web development.
- Apache - Free and open-source cross-platform web server software.
- MariaDB - Community-developed, commercially supported fork of the MySQL relational database management system.
- Git - Distributed version control system.
- Composer - Application-level dependency manager.
- Node.js - JavaScript runtime environment.
- PhpMyAdmin - Open source administration tool for MySQL and MariaDB.

## Requirements

Make sure that you have the [minimum requirements for running WSL2](https://learn.microsoft.com/en-us/windows/wsl/install#prerequisites).
OS-wise you must be running:

- Windows 10:
  - For x64 systems: Version 1903 or later with Build 18362.1049 or later.
  - For ARM64 systems: Version 2004 or later with Build 19041 or later.
- or Windows 11.

Hardware-wise, you need virtualization support.
On some older hardware that supports this feature, it may be disabled from the BIOS.

WSL2 needs the Virtual Machine Platform feature enabled.
You can enable it via PowerShell running as an Administrator:

```bash
dism.exe /online /enable-feature /featurename:VirtualMachinePlatform /all /norestart
```

## Installing the Distro

Start by downloading and installing the Linux distro with this command:

```bash
wsl --install -d AlmaLinux-10
```

If everything ran ok, you are asked to enter a password at the end.
Next, run each of the [ansible playbook commands](https://docs.dotkernel.org/development/v2/setup/setup-packages/) to install the development components.

## Using WSL2 with AlmaLinux 10

You can start AlmaLinux 10 by executing this command in the Windows Terminal:

```bash
wsl -d AlmaLinux-10
```

Our PHP projects can be run by navigating into the root project folder and running this command:

```bash
php -S 0.0.0.0:8080 -t public
```

Then you need to navigate to `http://localhost:8080` in your preferred browser.

### Running the Application via Virtual Hosts

An easier way to run your applications is via virtual hosts.
The full instructions to set up your virtual hosts are in the [Create virtualhosts page](https://docs.dotkernel.org/development/v2/virtualhosts/create-virtualhost/).

After you set up your virtual hosts and install your project in e.g. the `/var/www/example.localhost/html` folder, you can run the application in the browser by accessing `example.localhost`.

If you run into any issues, make sure to first check the [FAQ page](https://docs.dotkernel.org/development/v2/faq/).
If all else fails, [create an issue](https://github.com/dotkernel/development/issues) with details so the team can help.

## FAQ

**Q: What does this WSL recipe set up?**
A: It sets up a full PHP development environment: WSL2, AlmaLinux 10, PHP, Apache, MariaDB, Git, Composer, Node.js and PhpMyAdmin.

**Q: What are the OS and hardware requirements?**
A: Windows 10 (Version 1903+ / Build 18362.1049+ for x64, or Version 2004+ / Build 19041+ for ARM64) or Windows 11, plus hardware virtualization support.
If needed, enable the Virtual Machine Platform feature via `dism.exe /online /enable-feature /featurename:VirtualMachinePlatform /all /norestart` in an administrator PowerShell.

**Q: How do I install AlmaLinux 10 via WSL2?**
A: Run `wsl --install -d AlmaLinux-10`, set a password when prompted, then run each of the Ansible playbook commands from the Dotkernel docs to install the development components.

**Q: How do I run a PHP project after setup?**
A: Either run `php -S 0.0.0.0:8080 -t public` from the project root and browse to http://localhost:8080, or, more easily, set up virtual hosts per the Dotkernel docs and access the project at its configured hostname (e.g. example.localhost).

**Q: What should I do if I run into issues during setup?**
A: First check the Dotkernel FAQ page.
If that doesn't resolve it, create an issue on the dotkernel/development GitHub repository with details.

## Resources

- [Full AlmaLinux 10 Installation Instructions](https://docs.dotkernel.org/development/v2/setup/installation/)
- [AlmaLinux 9 in WSL2 with PHP, Mariadb, Composer, PhpMyadmin instructions](https://www.dotkernel.com/php-development/almalinux-9-in-wsl2-install-php-apache-mariadb-composer-phpmyadmin/)
