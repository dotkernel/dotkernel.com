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

From time to time, it may be a good idea to have a persistent connection to database.

The place where it should be added that new configuration option is application.ini ( Dotkernel approach as an example)

Do **not** try something like below, will not work:

```
database.params.options.PDO::ATTR_PERSISTENT = TRUE
```

 

Instead , use the below line

```
database.params.persistent = TRUE
```

## FAQ

**Q: Where do you configure a persistent database connection in a Dotkernel project?**
A: The option should be added in application.ini, as shown in the Dotkernel approach used as an example in the article.

**Q: What Zend_Db configuration line should NOT be used for a persistent connection?**
A: database.params.options.PDO::ATTR_PERSISTENT = TRUE should not be used - the article states that it will not work.

**Q: What's the correct line to enable a persistent connection?**
A: Use database.params.persistent = TRUE instead.
