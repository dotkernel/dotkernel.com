---
title: "Development Environment | AlmaLinux 10 on WSL 2"
description: "AlmaLinux 10 on WSL 2 for a RHEL-compatible local stack. One Ansible playbook installs PHP, Apache, MariaDB, Composer, Node.js and phpMyAdmin, and a second provisions a virtualhost for every project you add."
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

Everything from Setup Packages onwards also runs on a bare AlmaLinux 10 host without WSL - the Ansible playbooks do not know or care which one they are provisioning.
Connect over SSH, and use the server's IP address instead of `localhost` when you test it.

- RHEL-compatible, matches production
- One playbook, the whole stack
- Virtualhosts without touching hosts file
- Aliases for switching PHP & Node versions

## Three steps to a running shell

Steps 1 and 2 run in Windows Terminal and step 3 in the AlmaLinux 10 shell; step 2 ends inside the new distro with a quick systemd check.
The full walkthrough, prompts and all, is in the docs.

### 1 . Terminal & requirements

On Windows 11, install Windows Terminal, then check for a modern WSL 2 install.
If `wsl --version` isn't recognized, install WSL 2 with `wsl --install --no-distribution` and restart when prompted.

```shell
wsl --version
```

### 2 . Install AlmaLinux 10

Stop any other running distro, then install AlmaLinux 10 and create your Unix username and password when prompted.

```shell
wsl --install -d AlmaLinux-10
```

Before moving on, confirm systemd is active inside AlmaLinux 10 - the playbook needs it.
If the check below prints an error instead of a status, add `systemd=true` under `[boot]` in `/etc/wsl.conf`, run `wsl --shutdown` from Windows Terminal and reopen the distro.

```shell
systemctl is-system-running
```

### 3 . Setup packages

Inside AlmaLinux 10, update the system, add the EPEL and Remi repositories, and install `ansible-core` with the `community.general` and `community.mysql` collections.
Then clone the `alma-linux-10` branch of `dotkernel/development`, copy `wsl/config.yml.dist` to `config.yml`, fill in your Git identity and MariaDB root password, and run the playbook from `development/wsl`.

```shell
ansible-playbook -i hosts install.yml --ask-become-pass
```

Not using WSL? Skip straight to Setup Packages on a bare AlmaLinux 10 host - the same playbook runs there unchanged; test it with the server's IP address instead of `localhost`.

After setup, [Editor Integration](https://docs.dotkernel.org/development/v2/editor-integration/) connects VS Code or PhpStorm, and [WSL Configuration](https://docs.dotkernel.org/development/v2/wsl-configuration/) covers where to keep projects and how to cap WSL's memory and CPU.

## What one playbook installs

`install.yml` reads `config.yml` once and provisions every one of these - safe to re-run if a step fails partway through.

| Component | What you get |
| --- | --- |
| Web server | Apache, with virtualhosts routed automatically under `*.localhost`. |
| Database | MariaDB 12.3 from the MariaDB repository, plus phpMyAdmin for browsing it. |
| PHP | 8.5 by default via the Remi repository; `php81` … `php85` aliases switch versions. |
| Node.js | 22 by default via NodeSource; `node18` … `node24` aliases switch versions. |
| Git & Composer | Your Git identity from `config.yml`, and the latest Composer at install time; update it later with `composer self-update`. |

## Every project, its own subdomain

`api.dotkernel.localhost` and `frontend.dotkernel.localhost` can point at two different projects on the same machine, and Apache routes both without a single edit to the Windows hosts file - any `*.localhost` domain is routed automatically.

List the domains you want under `config.yml`'s `virtualhosts` key and run `create-virtualhost.yml` - a separate playbook you re-run for every new project, without repeating `install.yml`.
Existing entries are left untouched, so you keep adding to the same file as your project grows.

- [Read the virtualhosts docs](https://docs.dotkernel.org/development/v2/virtualhosts/overview/)

### One playbook, every domain

In `development/wsl/config.yml`, under `config.virtualhosts`:

```yaml
config:
  virtualhosts:
    - "api.dotkernel.localhost"
```

`api.dotkernel.localhost` is only an example - use any name your project needs, such as `laravel.localhost` or `shop.localhost`, as long as it ends in `.localhost` and uses only lowercase letters, numbers and hyphens.
Add one list item per project.

Then provision it:

```shell
ansible-playbook -i hosts create-virtualhost.yml --ask-become-pass
```

Files go under `/var/www/<your-domain>/html` - for example `/var/www/api.dotkernel.localhost/html` - with the document root at `html/public`.
`html/public` doesn't exist until you place a project there, so the URL shows an error until then.

## Common questions

The short version of the full FAQ - see the docs for the rest.

### How do I switch PHP versions?

Run `sudo dnf module switch-to php:remi-{major}.{minor} -y`, or use one of the predefined aliases: `php81`, `php82`, `php83`, `php84` or `php85`.

### How do I switch Node.js versions?

Use one of the predefined aliases - `node18`, `node20`, `node22` or `node24` - which reinstall Node.js from NodeSource at that major version.

### How do I fix permission issues?

Local development only: `chmod -R 777 data`, `log` or `public/uploads`, whichever directory the error names.
Don't carry this into staging or production.

### Where are the error logs?

Apache: `/var/log/httpd/error_log`, plus `/var/www/<virtualhost>/log/error.log` for each virtualhost. PHP-FPM: `/var/log/php-fpm/error.log` and `www-error.log`.

### How do I update Composer?

`sudo /usr/local/bin/composer self-update`, then confirm with `composer --version`.

### How do I create command aliases?

Add `alias name="command"` to `.bash_profile` in your home directory, then run it like any other command.

### How do I delete a virtualhost?

Remove its folder under `/var/www/`, its Apache config and enabled-site symlink, then `sudo systemctl restart httpd`.

### Why does the playbook fail with a systemd error?

systemd isn't active in the distro yet.
Add `systemd=true` under `[boot]` in `/etc/wsl.conf`, run `wsl --shutdown` from Windows Terminal, reopen AlmaLinux 10 and re-run `install.yml`.

### What if port 80 is already in use?

Find the Windows service holding it with `netstat -ano | findstr :80` and stop it, or change Apache's `Listen` port in `/etc/httpd/conf/httpd.conf` and restart httpd.

### Is this environment safe to expose beyond localhost?

No. It is for local development only: the MariaDB root password is stored in plaintext, phpMyAdmin allows root login and the firewall is off by default.

### Can I run this without WSL?

Yes - from Setup Packages onwards the steps are the same on a bare AlmaLinux 10 host.
Skip the WSL steps, connect over SSH, and use the server's IP address instead of `localhost`.

## Provision once, match your production OS

Built for how the platform ships.

The WSL 2 + AlmaLinux 10 setup is maintained by the same team behind the rest of the Headless Platform, so the local environment stays on the same OS family as production - not a Docker approximation of it.
It is a local development environment only: security is relaxed by default, so never expose it to a network.

[Talk to us ->](https://www.dotkernel.com/contact/)
