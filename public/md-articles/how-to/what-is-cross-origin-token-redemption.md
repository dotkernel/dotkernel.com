---
title: "What is cross origin token redemption?"
description: "An explanation of cross-origin token redemption, the technique for securely verifying and redeeming a token issued on one domain when it's used on another, including how JWT and OAuth 2.0 implement it."
author: "Alex Karajos"
date_published: "2023-01-27"
canonical_url: "https://www.dotkernel.com/how-to/what-is-cross-origin-token-redemption/"
category: "How to's"
language: "en"
---

# What is cross origin token redemption?

## TL;DR

Cross-origin token redemption verifies the security and authenticity of a token issued on one domain but used on another, which is common when a logged-in user needs to access resources on a different site.
The receiving domain checks the token's signature and decrypts it before trusting it.
JWT and OAuth 2.0 are two standards that implement this pattern, each with a different verification flow.

Cross-origin token redemption is a technique used to ensure the security and authenticity of a token that is issued by one website or domain, but intended for use on a different website or domain.
This process is commonly used in situations where a user needs to access resources from multiple domains, such as when a user is logged in to one website and needs to access resources from another website.
When a token is issued from one domain, it is typically encrypted and signed to prevent tampering and ensure that it can only be used by the intended recipient.
When the token is redeemed on a different domain, the receiving domain must first verify the authenticity of the token by checking the signature and decrypting the token.

## JSON Web Token (JWT)

One common method of cross-origin token redemption is to use the JWT standard.
JWTs are a compact, URL-safe means of representing claims to be transferred between two parties.
They consist of three parts:

- `header`: it contains information about the token, including the algorithm used to sign it
- `payload`: it contains the claims, which are the pieces of information that the token is intended to convey
- `signature`: is used to verify that the token has not been tampered with

In order to redeem a JWT on a different domain, the receiving domain must first verify the signature using the algorithm specified in the header and the secret key that was used to sign the token.
Once the signature is verified, the domain can then read the claims from the payload to determine if the user is authorized to access the requested resources.

## OAuth 2.0

Another method of cross-origin token redemption is to use the OAuth 2.0 standard.
OAuth 2.0 is an open standard for authorization that enables a third-party application to obtain limited access to an HTTP service.
In this method, when a user is logged in to one website and wants to access resources from another website, the first website will redirect the user to the second website and pass a token.
The second website will then validate the token and provide the user with access to the requested resources.

In summary, cross-origin token redemption is a technique used to ensure the security and authenticity of a token that is issued by one website or domain, but intended for use on a different website or domain.
This is achieved by verifying the signature of the token and decrypting it and by using standards like JWT or OAuth 2.0.

## FAQ

**Q: What is cross-origin token redemption?**
A: It is a technique used to ensure the security and authenticity of a token that is issued by one website or domain but intended for use on a different website or domain, commonly used when a user needs to access resources from multiple domains.

**Q: What are the three parts of a JSON Web Token (JWT)?**
A: A JWT consists of a header (information about the token, including the signing algorithm), a payload (the claims, i.e. the pieces of information the token conveys), and a signature (used to verify the token has not been tampered with).

**Q: How does a receiving domain verify a JWT?**
A: The receiving domain first verifies the signature using the algorithm specified in the header and the secret key used to sign the token, then reads the claims from the payload to determine if the user is authorized to access the requested resources.

**Q: How does OAuth 2.0 handle cross-origin token redemption?**
A: OAuth 2.0 is an open standard for authorization enabling a third-party application limited access to an HTTP service. When a user logged in to one website wants to access resources on another, the first website redirects the user to the second and passes a token, which the second website validates before granting access.
