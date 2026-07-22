---
title: "How to use Alerts in DotKernel"
description: "How the Dot_Alert system works in DotKernel, used to e-mail developers when something goes wrong, illustrated with the failed-email-send use case."
author: "Gabi DJ"
date_published: "2015-04-29"
canonical_url: "https://www.dotkernel.com/dotkernel/how-to-use-alerts-in-dotkernel/"
category: "Dotkernel"
language: "en"
---

# How to use Alerts in DotKernel

## TL;DR
Alerts (Dot_Alert's) are e-mails usually sent to site developers using PHP's `mail()`, meant only to notify a developer that something is wrong — not for regular mail.
Dot_Alert resembles Dot_Email: it has a sender, subject, destination and message, and can be sent.
This guide walks through DotKernel's existing example, where an Alert notifies the developer when an e-mail fails to send.

## What Dot_Alert is for

Alerts should only notify a developer: *"Hey, something's wrong here, you might want to know this!"*
The Dot_Alert class resembles Dot_Email — like a mail message, an alert has at least a sender, a subject, a destination and a message, and it can be sent.

## Example: notifying a developer of a failed e-mail send

In DotKernel, Alerts are used to notify the developer when an e-mail was not sent successfully.

### 1. The message template (dots.xml)

The alert message is kept in the `dots.xml` file:

```xml
<variable option="global">
    <alertMessages>
        <email>
            <subject> SMTP Error on {SITE_NAME} </subject>
            <message>
SMTP Error on {SITE_NAME}
We were unable to send SMTP email
---------------------------------
Caught exception: {E_CLASS}
Message:  {E_MESSAGE}
To Email: {TO_EMAIL}
From Email: {FROM_EMAIL}
Date: {DATE_NOW}
---------------------------------
            </message>
        </email>
    </alertMessages>
</variable>
```

### 2. Fetch the message from the config

```php
$subject = $this->option->alertMessages->email->subject;
$message = $this->option->alertMessages->email->message;
```

### 3. Get the destination recipients

```php
$devEmails = explode(',', $this->settings->devEmails);
```

### 4. Prepare the details that fill the {VARIABLES}

```php
$details = array(
    'e_class' => get_class($e),
    'site_name' => $this->seoOption->siteName,
    'site_url' => $registry->configuration->website->params->url,
    'e_message' => $e->getMessage(),
    'to_email' => implode(',', $this->_to),
    'from_email' => $this->getFrom(),
    'date_now' => date('F dS, Y h:i:s A'),
);
```

`$e` is the caught exception thrown when the e-mail send process fails.

### 5. Create and send the alert

```php
$alert = new Dot_Alert();

$alert->addHeader("From: " . $this->settings->siteEmail);
$alert->addHeader("Reply-To:" . $this->settings->siteEmail);
$alert->addHeader("X-Mailer: PHP/" . phpversion());

$alert->setTo($devEmails);
$alert->setSubject($subject);
$alert->setContent($message);

// replaces the {VARIABLES} in subject/message with real data
$alert->setDetails($details);

$alert->send();
```

## FAQ

**Q: What is a Dot_Alert used for?**
A: Alerts (Dot_Alert's) are e-mails usually sent to the site developers using PHP's mail() function.
They shouldn't be used to send regular mail — they only notify the developer that something is wrong.

**Q: What does the Dot_Alert class resemble, and what does an alert contain?**
A: Dot_Alert resembles Dot_Email.
Like a mail message, an alert has at least a sender, a subject, a destination and a message, and it can be sent.

**Q: What's an example use of Alerts in DotKernel?**
A: DotKernel uses Alerts to notify the developer when an email was not sent successfully, with the message kept in the dots.xml file under the alertMessages section.

**Q: How are the {VARIABLES} in an alert message replaced with real data?**
A: A $details array is prepared (e.g. e_class, site_name, site_url, e_message, to_email, from_email, date_now), and the alert's setDetails() method replaces the {VARIABLES} placeholders in the subject and message with that data.

**Q: What are the steps to build and send an alert?**
A: Create a new Dot_Alert(), add headers such as From, Reply-To and X-Mailer, then call setTo(), setSubject() and setContent(), then setDetails() to fill in the placeholders, and finally call send().

## Resources

- [Understanding dots.xml](http://www.dotkernel.com/docs/dots-xml/)
