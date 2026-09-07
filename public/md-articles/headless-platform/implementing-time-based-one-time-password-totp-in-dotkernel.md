---
title: "Implementing Time-based One-Time Password (TOTP) in Dotkernel"
description: "Tutorial on installing and using dot-totp to add two-factor authentication (2FA) with time-based one-time passwords to Dotkernel Admin, including required files, database changes, and the resulting user flow."
author: "Florin Bidirean"
date_published: "2026-04-03"
canonical_url: "https://www.dotkernel.com/headless-platform/implementing-time-based-one-time-password-totp-in-dotkernel/"
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
`dot-totp` adds two-factor authentication (2FA) to Dotkernel Admin using time-based one-time passwords.
Users authenticate with their password plus a 6-digit code from an Authenticator app that refreshes every 30 seconds.
Installation is one Composer command plus a set of forms, handlers, middleware, and templates from the official code examples, applying a `TotpTrait` to the relevant entity, migrating three new database columns, and registering routes/pipeline/ConfigProvider updates.

## What TOTP Does

A **Time-based One-Time Password (TOTP)** is a security algorithm used as part of **two-factor authentication (2FA)** to protect against account attacks. The mechanism is integrated into [dot-totp](https://github.com/dotkernel/dot-totp) to enhance security by requiring both a **password** and **an additional one-time code**. Our implementation follows the industry standard of using an Authenticator app to generate temporary, unique 6 digit codes that change every 30 seconds.

In this article we will:

- Install `dot-totp` in [Dotkernel Admin](https://github.com/dotkernel/admin).
- Review how `dot-totp` behaves in the UI.

> You can also follow the installation steps in our [documentation site](https://docs.dotkernel.org/admin-documentation/v7/tutorials/install-dot-totp/).

## 2FA with TOTP Flow

Below is a simplified flow for the 2FA with TOTP mechanism.

![](/uploads/article/019f8a80-cc99-7003-89fb-1a4493d92a4c/totp-flow.jpg)

## How to Install dot-totp

If you haven't already, install [Dotkernel Admin](https://github.com/dotkernel/admin).

> These installation steps should work similarly in any middleware-based application.

The first step is to include the package into your project by running this command:

```
composer require dotkernel/dot-totp
```

We will follow the Dotkernel file structure and create the files in the list below. If you follow the links from the [main totp integration example](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp), you can download the files and add them to your codebase.

- [src/Admin/src/Form/RecoveryForm.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Form/RecoveryForm.php)
- [src/Admin/src/Form/TotpForm.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Form/TotpForm.php)
- [src/Admin/src/Handler/Account/GetDisableTotpFormHandler.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Handler/Account/GetDisableTotpFormHandler.php)
- [src/Admin/src/Handler/Account/GetEnableTotpFormHandler.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Handler/Account/GetEnableTotpFormHandler.php)
- [src/Admin/src/Handler/Account/GetRecoveryFormHandler.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Handler/Account/GetRecoveryFormHandler.php)
- [src/Admin/src/Handler/Account/GetTotpHandler.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Handler/Account/GetTotpHandler.php)
- [src/Admin/src/Handler/Account/PostDisableTotpHandler.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Handler/Account/PostDisableTotpHandler.php)
- [src/Admin/src/Handler/Account/PostEnableTotpHandler.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Handler/Account/PostEnableTotpHandler.php)
- [src/Admin/src/Handler/Account/PostValidateRecoveryHandler.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Handler/Account/PostValidateRecoveryHandler.php)
- [src/Admin/src/Handler/Account/PostValidateTotpHandler.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Handler/Account/PostValidateTotpHandler.php)
- [src/Admin/templates/admin/recovery-form.html.twig](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/templates/admin/recovery-form.html.twig)
- [src/App/src/Middleware/CancelUrlMiddleware.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/App/src/Middleware/CancelUrlMiddleware.php)
- [src/App/src/Middleware/TotpMiddleware.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/App/src/Middleware/TotpMiddleware.php)

You can use the trait at [src/Core/src/App/src/Entity/TotpTrait.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Core/src/App/src/Entity/TotpTrait.php) in any entity where you need 2FA.

> Make sure to migrate the new columns `totpSecret`, `totp_enabled` and `recovery_codes` in your entity.

There are still some code snippets in the [_misc](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp) folder:

- [The enable/disable 2FA button](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/_misc/totp-append-view-account.html.twig) should be used in the `view-account.html.twig` file or in a new page.
- [The routes updates](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/_misc/totp-append-routes.php) must be added in the `src/Admin/src/RoutesDelegator.php` file.
- [The pipeline updates](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/_misc/totp-append-Pipeline.php) must be added in the `config/pipeline.php` file after `$app->pipe(AuthMiddleware::class);`.
- [The ConfigProvider updates](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/_misc/totp-append-ConfigProvider.php) must be added in the `src/Admin/src/ConfigProvider.php` file.

## dot-totp in Action

Once you have `dot-totp` implemented, you can activate the feature in your admin accounts. If you navigate to your profile from the top-right image in Dotkernel Admin, you should see this box.

![](/uploads/article/019f8a80-cc99-7003-89fb-1a4493d92a4c/profile-totp-deactivated.jpg)

Simply click on 'Enable TOTP' to begin the activation process.

> We blurred out the QR code and recovery codes for this tutorial. You will receive dynamically generated versions that will be fully visible to you.

> You will need to have an Authenticator app installed on your mobile device.

![](/uploads/article/019f8a80-cc99-7003-89fb-1a4493d92a4c/totp-activate-qr.jpg)

Follow the instructions on the screen:

- Scan the QR code with your mobile device.
- Enter the 6-digit code it generates on your mobile device.

> The code refreshes every 30 seconds.

The TOPT activation flow will list several recovery codes you can use if your mobile device isn't available.

![](/uploads/article/019f8a80-cc99-7003-89fb-1a4493d92a4c/totp-recovery-codes.jpg)

> Each recovery code is usable only once.

> Save the recovery codes in a secure location.

If the code is valid, you will be logged in, and TOTP will be activated for your account.

Whenever you need to log into the account, you will start by entering your username and password, like before. Since TOTP is activated, you will need to also submit the code from your Authenticator app. Alternatively, you can submit a recovery code.

![](/uploads/article/019f8a80-cc99-7003-89fb-1a4493d92a4c/totp-ask-code.jpg)

That's it! You are now logged in securely.

## Additional Resources

- [dot-totp in GitHub](https://github.com/dotkernel/dot-totp)
- [Dotkernel Admin in GitHub](https://github.com/dotkernel/admin)
- [Dotkernel Documentation - Installing dot-totp into Dotkernel Admin](https://docs.dotkernel.org/admin-documentation/v7/tutorials/install-dot-totp/)

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
