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

When a Frontend (e.g. Angular) sits on top of a Dotkernel API, errors can happen - the API's response may have changed overnight, or a variable may simply be `undefined`.
Since the Frontend runs on the user's own client, there's little that can be done about it directly, so an endpoint was created to let clients submit the error message when something goes wrong.

## Usage

Send a POST request to your Dotkernel API on the route:

```
https://api.dotkernel.net/error-report
```

With a body:

```shell
{
    "message": "My awesome error!!!"
}
```

Note: the error message is stored by default in `/log/error-report-endpoint-log.log`, a separate log for Client, and the message is saved together with a timestamp.

## FAQ

**Q: Why was this endpoint created?**
A: When a Frontend client (e.g. Angular) running on the user's machine hits an error against the Dotkernel API - whether from an overnight API response change or a simple undefined variable - there is little that can be done from the client side, so this endpoint lets clients "write down" the error instead.

**Q: How do I submit an error from the client?**
A: Send a simple POST request to your Dotkernel API's `https://api.dotkernel.net/error-report` route, with a body such as `{ "message": "My awesome error!!!" }`.

**Q: Where is the submitted error message stored?**
A: By default, it is stored in a separate log file for Client, `/log/error-report-endpoint-log.log`, with the message saved alongside a timestamp.

## Resources

- [Dotkernel API on GitHub](https://github.com/dotkernel/api)
