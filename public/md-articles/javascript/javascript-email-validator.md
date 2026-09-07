---
title: "Javascript: Email Validator"
description: "A regex-based fix for email validation that allows the plus (+) and dash (-) characters in the appropriate parts of an email address."
author: "admin"
date_published: "2008-10-03"
canonical_url: "https://www.dotkernel.com/javascript/javascript-email-validator/"
category: "Javascript"
language: "en"
---

# Javascript: Email Validator

**Problem:** email should allow +/- characters in user, - in domain. dash (-) should be allowed anywhere in an email address or domain. plus (+) is allowed in the username (many people use this for categorization, especially at gmail)

**Solution :**

```
var regex = new RegExp("^+(\.+)*@+(\.+)*\.({2,})$","i");
```

This will validated also emails like: username1+username2@gmail-domain.co.uk

## FAQ

**Q: What problem does this email validator solve?**
A: Common email regex patterns fail to allow the plus (+) character in the username and the dash (-) character anywhere in the address or domain. The plus sign is used by many people, especially on Gmail, for categorization, and dashes commonly appear in domain names, so a validator that rejects them is too strict.

**Q: What is the suggested regex solution?**
A: The article proposes the regular expression ^+(\.+)*@+(\.+)*\.({2,})$ (case-insensitive) as a replacement that permits both the plus and dash characters in the appropriate parts of the address.

**Q: What kind of email addresses does this regex validate?**
A: According to the article, this pattern will also successfully validate addresses like username1+username2@gmail-domain.co.uk, which combine a plus-separated username with a dashed domain.
