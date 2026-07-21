---
title: "Implementing Time-based One-Time Password (TOTP) in Dotkernel"
description: "Tutorial on installing and using dot-totp to add two-factor authentication (2FA) with time-based one-time passwords to Dotkernel Admin, including required files, database changes, and the resulting user flow."
author: "Florin Bidirean"
date_published: "2026-04-03"
canonical_url: "https://new.dotkernel.com/headless-platform/implementing-time-based-one-time-password-totp-in-dotkernel/"
category: "Headless Platform"
content_type: "tutorial"
language: "en"
entities:
  - name: "dot-totp"
    type: "SoftwareSourceCode"
    url: "https://github.com/dotkernel/dot-totp"
  - name: "Dotkernel Admin"
    type: "SoftwareApplication"
    url: "https://github.com/dotkernel/admin"
prerequisites:
  - "Dotkernel Admin installed (the steps apply similarly to any middleware-based application)"
  - "An Authenticator app on a mobile device (for end users enabling TOTP)"
keywords: ["TOTP", "time-based one-time password", "2FA", "two-factor authentication", "dot-totp", "Dotkernel Admin", "PHP", "Mezzio", "middleware", "recovery codes", "QR code", "authenticator app"]
official_docs: "https://docs.dotkernel.org/admin-documentation/v7/tutorials/install-dot-totp/"
code_examples: "https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp"
---

# Implementing Time-based One-Time Password (TOTP) in Dotkernel

## TL;DR

`dot-totp` adds two-factor authentication (2FA) to Dotkernel Admin using time-based one-time passwords. Users authenticate with their password plus a 6-digit code from an Authenticator app that refreshes every 30 seconds. Installation is one Composer command plus a set of forms, handlers, middleware, and templates from the official code examples, applying a `TotpTrait` to the relevant entity, migrating three new database columns, and registering routes/pipeline/ConfigProvider updates.

## Definitions

- **TOTP (Time-based One-Time Password)**: A security algorithm used as part of two-factor authentication. Generates temporary, unique 6-digit codes that change every 30 seconds, via an Authenticator app.
- **2FA (Two-Factor Authentication)**: Requires both a password and an additional one-time code, to protect against account attacks.
- **dot-totp**: The Dotkernel package that integrates the TOTP mechanism into Dotkernel applications.
- **Recovery codes**: Single-use backup codes generated during TOTP activation, usable for login when the mobile device is unavailable.

## Key facts

| Fact | Value |
|---|---|
| Package | `dotkernel/dot-totp` |
| Install command | `composer require dotkernel/dot-totp` |
| Target application | Dotkernel Admin (applies similarly to any middleware-based application) |
| Code format | 6-digit numeric code |
| Code lifetime | 30 seconds |
| Recovery codes | Single-use each; must be saved in a secure location |
| Entity integration | `TotpTrait` applied to any entity requiring 2FA |
| New database columns | `totpSecret`, `totp_enabled`, `recovery_codes` |
| Official tutorial | https://docs.dotkernel.org/admin-documentation/v7/tutorials/install-dot-totp/ |
| Code examples | https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp |

## 2FA with TOTP: authentication flow

1. User submits username and password (unchanged from standard login).
2. Since TOTP is activated, the application also asks for the code from the user's Authenticator app.
3. User submits the current 6-digit code, or alternatively a single-use recovery code.
4. If the code is valid, the user is logged in.

## Installation steps

### Step 1 — Install the package

Prerequisite: a working Dotkernel Admin installation.

```shell
composer require dotkernel/dot-totp
```

### Step 2 — Add the integration files

Following the Dotkernel file structure, add the files below (downloadable from the [official code examples](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp)):

Forms:
- `src/Admin/src/Form/RecoveryForm.php`
- `src/Admin/src/Form/TotpForm.php`

Handlers (in `src/Admin/src/Handler/Account/`):
- `GetDisableTotpFormHandler.php`
- `GetEnableTotpFormHandler.php`
- `GetRecoveryFormHandler.php`
- `GetTotpHandler.php`
- `PostDisableTotpHandler.php`
- `PostEnableTotpHandler.php`
- `PostValidateRecoveryHandler.php`
- `PostValidateTotpHandler.php`

Templates:
- `src/Admin/templates/admin/recovery-form.html.twig`

Middleware:
- `src/App/src/Middleware/CancelUrlMiddleware.php`
- `src/App/src/Middleware/TotpMiddleware.php`

### Step 3 — Apply the entity trait and migrate the database

Apply the trait at `src/Core/src/App/src/Entity/TotpTrait.php` to any entity that requires 2FA, then migrate the new columns onto that entity's table: `totpSecret`, `totp_enabled`, and `recovery_codes`.

### Step 4 — Register the remaining snippets

The `_misc` folder in the code examples contains four required additions:

| Snippet | Destination |
|---|---|
| Enable/disable 2FA button (`totp-append-view-account.html.twig`) | `view-account.html.twig`, or a new page |
| Routes updates (`totp-append-routes.php`) | `src/Admin/src/RoutesDelegator.php` |
| Pipeline updates (`totp-append-Pipeline.php`) | `config/pipeline.php`, after `$app->pipe(AuthMiddleware::class);` |
| ConfigProvider updates (`totp-append-ConfigProvider.php`) | `src/Admin/src/ConfigProvider.php` |

## Using TOTP in Dotkernel Admin (end-user flow)

### Enabling TOTP

1. Navigate to the account profile (top-right image in Dotkernel Admin). A TOTP box with an "Enable TOTP" button is shown.
2. Click "Enable TOTP". A QR code is displayed. An Authenticator app on a mobile device is required.
3. Scan the QR code with the mobile device.
4. Enter the 6-digit code generated by the Authenticator app. The code refreshes every 30 seconds.
5. Save the recovery codes shown during activation in a secure location - each is usable only once.
6. If the code is valid, the user is logged in and TOTP is activated for the account.

### Logging in with TOTP enabled

1. Enter username and password as before.
2. Submit the current code from the Authenticator app, or alternatively a recovery code.
3. On success, the user is logged in.

## FAQ

**Q: What is a Time-based One-Time Password (TOTP)?**
A: A Time-based One-Time Password (TOTP) is a security algorithm used as part of two-factor authentication (2FA) that requires both a password and an additional one-time code generated by an Authenticator app. In Dotkernel, this is implemented through the dot-totp package.

**Q: Can dot-totp be used outside of Dotkernel Admin?**
A: Yes. Although this tutorial installs dot-totp in Dotkernel Admin, the installation steps work similarly in any middleware-based PHP application.

**Q: What happens if I lose access to my authenticator app?**
A: You can log in using one of the recovery codes generated when you activated TOTP. Each recovery code is usable only once, so make sure to save them in a secure location.

**Q: How often does the TOTP code change?**
A: The code generated by your Authenticator app refreshes every 30 seconds.

**Q: What database changes are required to support dot-totp?**
A: You need to migrate three new columns onto the entity that uses the TotpTrait: totpSecret, totp_enabled, and recovery_codes.

## Resources

- dot-totp on GitHub: https://github.com/dotkernel/dot-totp
- Dotkernel Admin on GitHub: https://github.com/dotkernel/admin
- Official tutorial — Installing dot-totp into Dotkernel Admin: https://docs.dotkernel.org/admin-documentation/v7/tutorials/install-dot-totp/
- Complete code examples: https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp
