---
title: "Development Environment | AlmaLinux 10 on WSL 2"
description: "AlmaLinux 10 on WSL 2 for a RHEL-compatible local stack. One Ansible playbook installs PHP, Apache, MariaDB, Composer, Node.js and phpMyAdmin, and provisions a virtualhost for every project you add."
canonical_url: "https://www.dotkernel.com/wsl2/"
language: "en"
---

# Development Environment

Development environment . AlmaLinux 10 . WSL2

Dotkernel's local environment runs AlmaLinux 10 - inside WSL 2 on Windows, or on bare metal with no WSL at all.
One Ansible playbook installs PHP, Apache, MariaDB, Composer, Node.js and phpMyAdmin, and every project gets its own `*.localhost` virtualhost without touching a hosts file.

- [Read the docs](https://docs.dotkernel.org/development/v2/terminal/)
- [View on GitHub](https://github.com/dotkernel/development/tree/alma-linux-10)

| | |
| --- | --- |
| Distro | AlmaLinux 10 |
| Provisioning | Ansible |
| Host | WSL 2 or bare metal |

## Terminal to running stack

Terminal -> WSL 2 -> AlmaLinux -> Ansible -> Ready.

## The same OS family your servers run

AlmaLinux is a RHEL-compatible distribution, so the packages, package manager and service conventions you use locally - `dnf`, `systemd` and the rest - are the ones you will see again in staging and production.
WSL 2 puts that distro on a Windows machine without a second computer or a Windows-native rebuild of every tool.

Everything here also runs the same way without WSL, directly on a bare AlmaLinux 10 host - the Ansible playbooks do not know or care which one they are provisioning.

- RHEL-compatible, matches production
- One playbook, the whole stack
- Virtualhosts without touching hosts file
- Aliases for switching PHP & Node versions

## Three steps to a running shell

Each step runs in a different place - Windows Terminal for the first two, the AlmaLinux 10 shell for the third.
The full walkthrough, prompts and all, is in the docs.

### 1 . Terminal & requirements

Install Windows Terminal, then confirm WSL 2 is enabled - Hyper-V, Virtual Machine Platform and Windows Subsystem for Linux, all turned on in Windows features.

```shell
wsl -v
```

### 2 . Install AlmaLinux 10

Stop any other running distro, then install AlmaLinux 10 and create your Unix username and password when prompted.

```shell
wsl --install -d AlmaLinux-10
```

### 3 . Setup packages

Clone `dotkernel/development`, fill in `config.yml` with your Git identity and MariaDB root password, and let Ansible provision the rest.

```shell
ansible-playbook -i hosts install.yml --ask-become-pass
```

Not using WSL? Skip straight to Setup Packages on a bare AlmaLinux 10 host - the same playbook runs there unchanged.

## What one playbook installs

`install.yml` reads `config.yml` once and provisions every one of these - safe to re-run if a step fails partway through.

| Component | What you get |
| --- | --- |
| Web server | Apache, with virtualhosts routed automatically under `*.localhost`. |
| Database | MariaDB 11.4 LTS, plus phpMyAdmin for browsing it. |
| PHP | 8.4 by default via the Remi repository; `php81` … `php85` aliases switch versions. |
| Node.js | 22 by default via NodeSource; `node18` … `node24` aliases switch versions. |
| Git & Composer | Your Git identity from `config.yml`, and the latest Composer, kept current with `composer self-update`. |
| Ansible | `community.general` and `community.mysql` collections - the same tool that just installed itself. |

## Every project, its own subdomain

`api.dotkernel.localhost` and `frontend.dotkernel.localhost` can point at two different projects on the same machine, and Apache routes both without a single edit to the Windows hosts file - any `*.localhost` domain is routed automatically.

List the domains you want under `config.yml`'s `virtualhosts` key and run the playbook.
Existing entries are left untouched, so you keep adding to the same file as your project grows.

- [Read the virtualhosts docs](https://docs.dotkernel.org/development/v2/virtualhosts/overview/)

### One playbook, every domain

In `development/wsl/config.yml`, under `virtualhosts`:

```text
api.dotkernel.localhost
```

Then provision it:

```shell
ansible-playbook -i hosts create-virtualhost.yml --ask-become-pass
```

Files go under `/var/www/api.dotkernel.localhost/html`, with the document root at `html/public`.

## Common questions

The short version of the full FAQ - see the docs for the rest.

### How do I switch PHP versions?

Run `sudo dnf module switch-to php:remi-{major}.{minor} -y`, or use one of the predefined aliases: `php81`, `php82`, `php83`, `php84` or `php85`.

### How do I switch Node.js versions?

Use one of the predefined aliases - `node18`, `node20`, `node22` or `node24` - which reinstall Node.js from NodeSource at that major version.

### How do I fix permission issues?

Local development only: `chmod -R 777 data`, `log` or `public/uploads`, whichever directory the error names.

### Where are the error logs?

Apache: `/var/log/httpd/error_log`. PHP-FPM: `/var/log/php-fpm/error.log` and `www-error.log`.

### How do I update Composer?

`sudo /usr/local/bin/composer self-update`, then confirm with `composer --version`.

### How do I create command aliases?

Add `alias name="command"` to `.bash_profile` in your home directory, then run it like any other command.

### How do I delete a virtualhost?

Remove its folder under `/var/www/`, its Apache config and enabled-site symlink, then `sudo systemctl restart httpd`.

### Can I run this without WSL?

Yes - every instruction here works the same way on a bare AlmaLinux 10 host.

## Provision once, match production everywhere.

Built for how the platform ships.

The WSL 2 + AlmaLinux 10 setup is maintained by the same team behind the rest of the Headless Platform, so the local environment stays in step with what actually runs in production - not a Docker approximation of it.

[Talk to us ->](https://www.dotkernel.com/contact/)
