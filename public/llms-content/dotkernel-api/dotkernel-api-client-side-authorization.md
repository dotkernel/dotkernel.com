---
title: "DotKernel API Client Side Authorization"
description: "How a client application authorizes against a backend built with DotKernel API, from the authorization request to using the access token."
author: "admin"
date_published: "2019-08-05"
canonical_url: "https://www.dotkernel.com/dotkernel-api/dotkernel-api-client-side-authorization/"
category: "Dotkernel API"
language: "en"
---

# DotKernel API Client Side Authorization

This article covers the basic authorization of a Client application which uses a backend built using DotKernel API.

## Authorization Request

Client application users send a POST request to the backend containing the following JSON object:

```shell
{
"grant_type": "password",
"client_id": "{API_CLIENT}",
"client_secret": "{API_CLIENT_SECRET}",
"scope": "{SCOPE}",
"username": "{USERNAME/EMAIL}",
"password": "{PASSWORD}"
}
```

## Authorization Response

If the credentials are correct, the API will return a JSON object containing the authentication data:

```shell
{
"token_type": "Bearer",
"expires_in": 86400,
"access_token": "...",
"refresh_token": "..."
}
```

When sending API requests to an endpoint which requires authorization, an `Authorization` header must be present containing `"Bearer {access_token}"`, where `{access_token}` represents the content of the key with the same name found in the authorization response.

## FAQ

**Q: What does a client send to request authorization?**
A: The client application sends a POST request to the backend with a JSON object containing `grant_type` (set to "password"), `client_id`, `client_secret`, `scope`, `username`/email, and `password`.

**Q: What does the API return when authorization succeeds?**
A: If the credentials are correct, the API returns a JSON object containing `token_type` ("Bearer"), `expires_in` (86400 seconds), an `access_token`, and a `refresh_token`.

**Q: How do I use the access token in subsequent requests?**
A: When sending API requests to an endpoint that requires authorization, include an Authorization header containing `"Bearer {access_token}"`, where `{access_token}` is the value returned in the authorization response.

## Resources

- [DotKernel API on GitHub](https://github.com/dotkernel/api)
