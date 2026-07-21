---
title: "DotKernel version 1.0 in action"
description: "An overview of DotKernel 1.0, DotBoost's in-house framework built on Zend Framework, its simplified MVC architecture, and the specific Zend Framework classes it relies on."
author: "admin"
date_published: "2009-10-02"
canonical_url: "https://new.dotkernel.com/dotkernel/dotkernel-version-1-0-in-action/"
category: "Dotkernel"
language: "en"
---

# DotKernel version 1.0 in action

## TL;DR

DotKernel is DotBoost's in-house developed framework, built on top of Zend Framework and released under the Open Software License (OSL 3.0). It uses a simplified MVC architecture, easy to learn for beginner and intermediate programmers, by eliminating much of Zend Framework's complexity through a different approach to handling web requests. It relies on only a handful of Zend Framework classes.

## Zend Framework classes DotKernel relies on

| Class | Purpose |
|---|---|
| `Zend_Config` | Provides multiple backends for configuration storage and a simple, intuitive, object-oriented interface for accessing it. DotKernel stores configuration as simple PHP arrays, wrapped by Zend_Config. |
| `Zend_Db` and `Zend_Db_Table` | A classic implementation of the Table Data Gateway and Row Data Gateway design patterns, for easy and intuitive access to database tables and rows, and an entry point for custom business logic. |
| `Zend_Mail` | Generalized functionality to compose and send text and MIME-compliant multipart e-mail, via the default Zend_Mail_Transport_Sendmail transport or via Zend_Mail_Transport_Smtp. |
| `Zend_Registry` | A container for storing objects and values in the application space, so the same object is always available throughout the application — an alternative to global storage. |
| `Zend_Validate` | Provides a set of commonly needed validators, plus a simple validator chaining mechanism so multiple validators can be applied to a single datum in a user-defined order. |

## FAQ

**Q: Who developed DotKernel and what is it built on?**
A: DotKernel is DotBoost's in-house developed framework, built on top of Zend Framework. At the time of this article it was at version 1.0, released under the Open Software License (OSL 3.0).

**Q: What architecture does DotKernel use?**
A: A simplified MVC (Model-View-Controller) architecture that's easy to learn for beginner and intermediate level programmers, achieved by eliminating much of Zend Framework's complexity through a different approach to handling web requests.

**Q: What does Zend_Config provide in DotKernel?**
A: It provides multiple backends for configuration storage and a simple, intuitive, object-oriented interface for accessing it. DotKernel stores configuration as simple PHP arrays, which are then wrapped by Zend_Config.

**Q: What role do Zend_Db and Zend_Db_Table play?**
A: Zend_Db_Table is a classic implementation of the Table Data Gateway and Row Data Gateway design patterns, allowing easy and intuitive access to database tables and rows, as well as an entry point for custom business logic surrounding the data.

**Q: What are Zend_Mail and Zend_Registry used for?**
A: Zend_Mail composes and sends both text and MIME-compliant multipart e-mail, via the default Zend_Mail_Transport_Sendmail transport or via Zend_Mail_Transport_Smtp. Zend_Registry is a container for storing objects and values in the application space, so the same object is always available throughout the application, as an alternative to using global storage.

## Resources

- [DotBoost](http://www.dotboost.com)
- [Open Software License (OSL 3.0)](http://opensource.org/licenses/osl-3.0.php)
- [Zend Framework](http://framework.zend.com/)
