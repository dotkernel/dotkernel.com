---
title: "API Endpoint to Collect Client Errors"
description: "How Dotkernel API's error-report endpoint lets frontend clients submit and log errors that occur on the user's machine."
author: "kakapiciu"
date_published: "2022-11-07"
canonical_url: "https://www.dotkernel.com/dotkernel-api/api-endpoint-to-collect-client-errors/"
category: "Dotkernel API"
language: "en"
---

# API Endpoint to Collect Client Errors

## API Endpoint to Collect Client Errors

Let's say you have a **(Client)** **Frontend** (e.g. Angular) over a [Dotkernel API](https://github.com/dotkernel/api) and there may be cases when there are errors, eighter the API changed it's response(s) over night or just a simple variable being `undefined` for some reason.

Since in our case **Frontend** is running on user client there is so little to do but we've come with an ideea for "writing down" any inconveniences.

We have created an **endpoint** where **Clients** can submit the **error message** when things are going down hill.

A simple **POST** to your **Dotkernel API** on route: `https://api.dotkernel.net/error-report`

With body:

```
{
    "message": "My awesome error!!!"
}
```

Note: The error **message** will be stored by default in `/log/error-report-endpoint-log.log`, a separate log for **Client** and the message will be saved with a **timestamp**.

## FAQ

**Q: Why was this endpoint created?**
A: When a Frontend client (e.g. Angular) running on the user's machine hits an error against the Dotkernel API - whether from an overnight API response change or a simple undefined variable - there is little that can be done from the client side, so this endpoint lets clients "write down" the error instead.

**Q: How do I submit an error from the client?**
A: Send a simple POST request to your Dotkernel API's https://api.dotkernel.net/error-report route, with a body such as { "message": "My awesome error!!!" }.

**Q: Where is the submitted error message stored?**
A: By default, it is stored in a separate log file for Client, /log/error-report-endpoint-log.log, with the message saved alongside a timestamp.
