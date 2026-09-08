---
title: "Sunsetting PEAR Channel for Zend Framework 1"
description: "Dotkernel announces the sunsetting of its unofficial PEAR channel for Zend Framework 1, citing PHP 8 compatibility issues and aging server infrastructure."
author: "admin"
date_published: "2022-11-11"
canonical_url: "https://www.dotkernel.com/zend-framework/sunsetting-pear-channel-for-zend-framework-1/"
category: "Zend Framework"
language: "en"
---

# Sunsetting PEAR Channel for Zend Framework 1

## TL;DR

Dotkernel is sunsetting its unofficial PEAR channel for Zend Framework 1, which was created in 2016 when PEAR was still widely used.
The main reasons are that upgrading PEAR to work with PHP 8 is too painful, and the channel currently runs on an LXC container with CentOS 7, which doesn't work on the latest Proxmox version, making the upgrade to AlmaLinux not worth the effort.
The post closes by thanking PEAR for its historical contribution to the PHP ecosystem.

The unofficial PEAR channel for Zend Framework 1 was created in [2016](https://www.dotkernel.com/dotkernel/migration-of-zend-framework-1-pear-channel/), at the time when [PEAR](https://pear.php.net/) was still used a lot.

Due to the fact that it is a pain to upgrade PEAR to work with PHP 8, we must sunset the channel.

Another reason for sunsetting is that it currently runs on an LXC container with CentOS 7, which does not work on the latest Proxmox version, and the upgrade to AlmaLinux is too much of a pain.

Thank you PEAR for your contribution to the PHP ecosystem, it was a major part of PHP infrastructure.

## FAQ

**Q: When was the PEAR channel for Zend Framework 1 created?**
A: The unofficial PEAR channel for Zend Framework 1 (pear.dotkernel.com) was created in 2016, at a time when PEAR was still used a lot.

**Q: Why is the PEAR channel for Zend Framework 1 being sunset?**
A: Because it's a pain to upgrade PEAR to work with PHP 8, the channel had to be sunset.

**Q: What other reason is given for sunsetting the channel?**
A: The channel was running on an LXC container with CentOS 7, which doesn't work on the latest Proxmox version, and upgrading to AlmaLinux was too much of a pain.
