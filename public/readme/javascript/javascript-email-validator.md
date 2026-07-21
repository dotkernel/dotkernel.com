---
title: "Javascript: Email Validator"
description: "A regex-based fix for email validation that allows the plus (+) and dash (-) characters in the appropriate parts of an email address."
author: "admin"
date_published: "2008-10-03"
canonical_url: "https://new.dotkernel.com/javascript/javascript-email-validator/"
category: "Javascript"
language: "en"
---

# Javascript: Email Validator

## Problem

Email validation should allow the dash (-) character anywhere in an email address or domain, and the plus (+) character in the username (many people use it for categorization, especially on Gmail).

## Solution

```javascript
var regex = new RegExp("^+(\.+)*@+(\.+)*\.({2,})$","i");
```

This will also validate emails like `username1+username2@gmail-domain.co.uk`.

## FAQ

**Q: What problem does this email validator solve?**
A: Common email regex patterns fail to allow the plus (+) character in the username and the dash (-) character anywhere in the address or domain, even though both are commonly used (plus for categorization on Gmail, dash in domain names).

**Q: What is the suggested regex solution?**
A: The regular expression `^+(\.+)*@+(\.+)*\.({2,})$` (case-insensitive) that permits both characters in the appropriate parts of the address.

**Q: What kind of email addresses does this regex validate?**
A: Addresses like `username1+username2@gmail-domain.co.uk`, combining a plus-separated username with a dashed domain.
