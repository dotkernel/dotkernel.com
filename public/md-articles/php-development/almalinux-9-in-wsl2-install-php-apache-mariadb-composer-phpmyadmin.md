---
title: "AlmaLinux 9 in WSL2 : install PHP, Apache, MariaDB, Composer, PhpMyadmin"
description: "A step-by-step guide to installing AlmaLinux 9 under Windows Subsystem for Linux (WSL2) and provisioning it with Ansible to run PHP, Apache, MariaDB, Composer, and phpMyAdmin."
author: "Alex Karajos"
date_published: "2022-12-14"
canonical_url: "https://www.dotkernel.com/php-development/almalinux-9-in-wsl2-install-php-apache-mariadb-composer-phpmyadmin/"
category: "PHP Development"
language: "en"
---

# AlmaLinux 9 in WSL2 : install PHP, Apache, MariaDB, Composer, PhpMyadmin

## TL;DR

This guide shows how to install AlmaLinux 9 through Windows Subsystem for Linux (WSL2) and provision it with an Ansible-driven installer script that sets up PHP, Apache, MariaDB, Composer, and phpMyAdmin.
It covers verifying WSL2 readiness, installing the AlmaLinux 9 distribution from the Microsoft Store, running the two-step Ansible installer (with a required restart in between), and confirming the setup through Apache's homepage, a PHP info page, and phpMyAdmin.

In this article we will demonstrate how we install AlmaLinux 9 using Windows Subsystem for Linux (WSL2).
First, you need to check if your machine is ready for using WSL2.
Open Windows Terminal and execute the following command:

```bash
wsl -v
```

The output should look similar to this:

```
WSL version: 2.2.4.0
Kernel version: 5.15.153.1-2
WSLg version: 1.0.61
MSRDC version: 1.2.5326
Direct3D version: 1.611.1-81528511
DXCore version: 10.0.26091.1-240325-1447.ge-release
Windows version: 10.0.22631.3737
```

If instead of the above output, you get an error, it means that WSL is not (completely) installed on your machine.
In this case, please follow the instructions found in [this guide](https://docs.dotkernel.org/development/v1/setup/installation/), then return to this page and continue with the next step.

## Download and Install AlmaLinux 9

Open Microsoft Store and search for `AlmaLinux`.
From the results, select `AlmaLinux 9` and install it.
Once installed, clicking on *Open* will open it in Windows Terminal.

The installer will prompt you for your *username*, your *password* and *password confirmation*.

## Setup AlmaLinux 9

While still in the AlmaLinux 9 terminal, start executing the following commands.

Install required packages:

```bash
sudo dnf install epel-release dnf-utils http://rpms.remirepo.net/enterprise/remi-release-9.rpm -y
```

Update installed packages:

```bash
sudo dnf upgrade -y
```

Install Ansible:

```bash
sudo dnf install ansible -y
```

Clone our development environment setup package:

```bash
git clone https://github.com/dotkernel/development.git
```

Navigate to the directory with the Ansible recipes:

```bash
cd ~/development/wsl/
```

Using your preferred text editor, open config.yml where you must fill in the empty fields.
Save and close the file.

Run Step 1 of the installer script (it will prompt you for the password you entered during the installation process):

```bash
ansible-playbook -i hosts install.yml --ask-become-pass
```

Restart AlmaLinux 9:

- press `Control` + `d`
- Open Windows Terminal
- stop AlmaLinux 9 by executing `wsl -t AlmaLinux9`
- start AlmaLinux 9 by executing `wsl -d AlmaLinux9`

Navigate back to the directory with the Ansible recipes:

```bash
cd ~/development/wsl/
```

Run Step 2 of the installer script (once again, it will prompt you for the password you entered during the installation process):

```bash
ansible-playbook -i hosts install.yml --ask-become-pass
```

Once finished, the installation is complete, your AlmaLinux 9 development environment is ready to use.

## Test AlmaLinux 9

Check if everything works by opening in your browser:

- http://localhost/ - Apache's default home page
- http://localhost/info.php - PHP info page
- http://localhost/phpmyadmin/ - PhpMyAdmin (login with username root + the root password you configured in config.yml under mariadb -> root_password)

The complete guide with additional features is available [here](https://docs.dotkernel.org/development/).

## FAQ

**Q: How do I check if my machine is ready for WSL2?**
A: Open Windows Terminal and run `wsl -v`. If it returns version information (WSL, Kernel, WSLg, MSRDC, Direct3D, DXCore, Windows versions), WSL2 is installed. If you get an error instead, WSL is not completely installed and you need to follow the linked setup guide first.

**Q: How do I download and install AlmaLinux 9?**
A: Open Microsoft Store, search for AlmaLinux, select AlmaLinux 9 from the results, and install it. Once installed, clicking Open launches it in Windows Terminal, where the installer prompts for your username, password, and password confirmation.

**Q: What are the main setup steps inside AlmaLinux 9?**
A: Install epel-release, dnf-utils and the Remi repository RPM, upgrade installed packages, install Ansible, clone the dotkernel/development Git repository, navigate to its wsl directory, fill in the empty fields in config.yml, then run the installer script's Step 1 with `ansible-playbook -i hosts install.yml --ask-become-pass` (it will prompt for the password set during installation).

**Q: Why do I need to restart AlmaLinux 9 partway through setup?**
A: After Step 1 of the installer script, you press Control+D, open Windows Terminal, stop AlmaLinux 9 with `wsl -t AlmaLinux9`, and start it again with `wsl -d AlmaLinux9`. Then you navigate back to the Ansible recipes directory and run Step 2 of the installer script (again with `ansible-playbook -i hosts install.yml --ask-become-pass`), which prompts for the password again. Once Step 2 finishes, the AlmaLinux 9 development environment is ready.

**Q: How do I verify the installation worked?**
A: Check three URLs in your browser: http://localhost/ for Apache's default home page, http://localhost/info.php for the PHP info page, and http://localhost/phpmyadmin/ for PhpMyAdmin, logging in with username root and the root password configured in config.yml under mariadb -> root_password.
