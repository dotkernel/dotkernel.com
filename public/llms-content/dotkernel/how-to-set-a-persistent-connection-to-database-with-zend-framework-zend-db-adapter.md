---
title: "How to Set a Persistent Connection to Database with Zend Framework Zend_Db adapter"
description: "How to configure a persistent database connection in application.ini using the Zend_Db adapter, and the option that will not work."
author: "admin"
date_published: "2012-02-16"
canonical_url: "https://www.dotkernel.com/dotkernel/how-to-set-a-persistent-connection-to-database-with-zend-framework-zend-db-adapter/"
category: "Dotkernel"
language: "en"
---

# How to Set a Persistent Connection to Database with Zend Framework Zend_Db adapter

From time to time, it may be a good idea to have a persistent connection to the database.
The configuration option should be added to `application.ini` (using DotKernel as an example).

Do **not** use the following — it will not work:

```ini
database.params.options.PDO::ATTR_PERSISTENT = TRUE
```

Instead, use this line:

```ini
database.params.persistent = TRUE
```

## FAQ

**Q: Where do you configure a persistent database connection in a DotKernel project?**
A: The option should be added in application.ini, as shown in the DotKernel approach used as an example in the article.

**Q: What Zend_Db configuration line should NOT be used for a persistent connection?**
A: `database.params.options.PDO::ATTR_PERSISTENT = TRUE` should not be used — the article states that it will not work.

**Q: What's the correct line to enable a persistent connection?**
A: Use `database.params.persistent = TRUE` instead.
