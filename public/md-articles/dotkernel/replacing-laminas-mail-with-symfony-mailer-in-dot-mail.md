---
title: "Replacing laminas-mail with Symfony mailer in dot-mail"
description: "Why and how Dotkernel replaced the abandoned laminas/laminas-mail package with symfony/mailer inside dotkernel/dot-mail, including configuration changes and the upgrade path to version 5."
author: "Florin Bidirean"
date_published: "2025-01-08"
canonical_url: "https://www.dotkernel.com/dotkernel/replacing-laminas-mail-with-symfony-mailer-in-dot-mail/"
category: "Dotkernel"
language: "en"
---

# Replacing laminas-mail with Symfony mailer in dot-mail

## TL;DR
The Laminas Technical Steering Committee decided on 2023-12-04 to abandon laminas/laminas-mail.
Dotkernel responded by replacing it with symfony/mailer inside the dotkernel/dot-mail package (version 5), aiming for minimal impact on existing projects - calls to send mail stay the same, though mime and imap related functionality is removed.

## What prompted the change

According to the discussion from the [LaminasTechnical steering Committee of 2023-12-04](https://github.com/laminas/technical-steering-committee/blob/main/meetings/minutes/2023-12-04-TSC-Minutes.md#maintainers-for-laminas-mime-and-laminas-mail), it was decided that the [laminas/laminas-mail](https://github.com/laminas/laminas-mail) package would be abandoned. On the one hand, there is nobody to maintain the package and on the other, there are several alternatives available in the ecosystem:

- [ddeboer/imap](https://github.com/ddeboer/imap) for interacting with IMAP
- [zbateson/mail-mime-parser](https://github.com/zbateson/mail-mime-parser) for parsing MIME messages
- [symfony/mailer](https://github.com/symfony/mailer) for sending mail

## How Dotkernel handles the issue

The Dotkernel team has also opted to replace the laminas/laminas-mail package in the [dotkernel/dot-mail](https://github.com/dotkernel/dot-mail) package. This meant revising the code, including the configuraton files. The optimal way to handle this was to try to have minimal impact on existing projects that already use dotkernel/dot-mail. This means that the calls to send the mail should remain the same, even if some lesser-used functionality like mime and imap are lost.

## Technical approach

In this article we will list the edits that enabled the switch from laminas/laminas-mail to symfony/mailer in the dotkernel/dot-mail package. You can follow all the changes in this list of PRs:

- [dot-mail PR 65](https://github.com/dotkernel/dot-mail/pull/65/files)
- [dot-mail PR 66](https://github.com/dotkernel/dot-mail/pull/66/files)
- [dot-mail PR 67](https://github.com/dotkernel/dot-mail/pull/67/files)
- [dot-mail PR 69](https://github.com/dotkernel/dot-mail/pull/69/files)

> Function definition changes will not be covered in this article.

The configuration file `mail.global.php` was revised to remove features that are no longer available and to make it easier to configure.

```
<?php

declare(strict_types=1);

return ,
                //copy destination addresses
                'cc' => [],
                //hidden copy destination addresses
                'bcc' => [],
                //email subject
                'subject' => '',
                //body options - content can be plain text, HTML
                'body' => ,
                //attachments config
                'attachments' => ,
                    'dir'   => ,
                ],
            ],
            /**
             * the mail transport to use can be any class implementing
             * Symfony\Component\Mailer\Transport\TransportInterface
             *
             * for standard mail transports, you can use these aliases:
             * - sendmail  => Symfony\Component\Mailer\Transport\SendmailTransport
             * - esmtp     => Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport
             *
             * defaults to sendmail
             **/
            'transport' => 'sendmail',
            //options that will be used only if esmtp adapter is used
            'smtp_options' => ,
            ],
        ],
        // option to log the SENT emails
        'log' => ,
    ],
];
```

Make sure to use **ONE** of the below transporters, based on your server configuration.

```
'transport' => 'sendmail',
```

**OR**

```
'transport' => 'esmtp',
```

We set **Sendmail** to be the default mail transport.

## How to update `dotkernel/dot-mail` from version 3 or version 4 to version 5 in your projects

- The first thing to do is download the new [mail configuration file](https://github.com/dotkernel/dot-mail/blob/5.0/config/mail.global.php.dist).
- Add the values you configured for your project, focusing on `transport`, `message_options` and `smtp_options`, then replace your old configuration file.
- In your `composer.json` update `"dotkernel/dot-mail": "^5.0.0",` and run `composer update` in the command line.

At this moment, `mime` and `imap` related functionality is removed.

## FAQ

**Q: Why was laminas/laminas-mail replaced?**
A: The Laminas Technical Steering Committee decided on 2023-12-04 that laminas/laminas-mail would be abandoned because there was nobody to maintain it, and several alternatives were already available in the ecosystem.

**Q: What alternatives were identified for the abandoned laminas-mail package?**
A: The alternatives mentioned are ddeboer/imap for interacting with IMAP, zbateson/mail-mime-parser for parsing MIME messages, and symfony/mailer for sending mail.

**Q: How did Dotkernel handle the removal of laminas-mail in dot-mail?**
A: The Dotkernel team replaced laminas/laminas-mail with symfony/mailer inside the dotkernel/dot-mail package, revising the code and configuration files while aiming for minimal impact on existing projects, so the calls used to send mail stay the same, even though some lesser-used functionality like mime and imap is lost.

**Q: Which mail transport does dot-mail use by default?**
A: The revised mail.global.php configuration defaults the transport to sendmail. You should use ONE of the transporters, either 'transport' => 'sendmail' or 'transport' => 'esmtp', based on your server configuration; Sendmail was set as the default.

**Q: How do I update dotkernel/dot-mail from version 3 or 4 to version 5?**
A: Download the new mail.global.php.dist configuration file, add the values you configured for your project (focusing on transport, message_options and smtp_options) to replace your old configuration file, then update "dotkernel/dot-mail" to "^5.0.0" in composer.json and run composer update.

**Q: What functionality is lost after switching to symfony/mailer?**
A: At the time of the article, mime and imap related functionality is removed from dot-mail as a result of the switch.
