---
title: "How to use Alerts in Dotkernel"
description: "How the Dot_Alert system works in Dotkernel, used to e-mail developers when something goes wrong, illustrated with the failed-email-send use case."
author: "Gabi DJ"
date_published: "2015-04-29"
canonical_url: "https://www.dotkernel.com/dotkernel/how-to-use-alerts-in-dotkernel/"
category: "Dotkernel"
language: "en"
---

# How to use Alerts in Dotkernel

## TL;DR
Alerts (Dot_Alert's) are e-mails usually sent to site developers using PHP's `mail()`, meant only to notify a developer that something is wrong - not for regular mail.
Dot_Alert resembles Dot_Email: it has a sender, subject, destination and message, and can be sent.
This guide walks through Dotkernel's existing example, where an Alert notifies the developer when an e-mail fails to send.

Alerts (or Dot_Alert's) are e-mails usually sent to the site developers, these messages are sent with **mail()** therefore you shouldn't use them to send regular mail. Alerts should only notify you as a developer: "***Hey, something's wrong here, you might want to know this!***"

In this article you will find out how to use the Alerts system in Dotkernel, we will also go through an existing example so this can be understood easier.

The Dot_Alert class resembles with Dot_Email, the alerts, like a mail message, have at least the sender, a subject, a destination and a message and it can also be sent.

In Dotkernel we use the Alerts for notifying the developer that an email was not sent successfully.

In this case we kept the message in the dots.xml file

```
    
        
             SMTP Error on {SITE_NAME} 
            
SMTP Error on {SITE_NAME}
We were unable to send SMTP email
---------------------------------
Caught exception: {E_CLASS}
Message:  {E_MESSAGE}
To Email: {TO_EMAIL}
From Email: {FROM_EMAIL}
Date: {DATE_NOW}
---------------------------------
            
        
    
```

If you're not familiar with the dots.xml, you should see [this article](http://www.dotkernel.com/docs/dots-xml/).

First the message will be fetched from the xml file, we already have it in $this->option

```
$subject = $this->option->alertMessages->email->subject;
$message = $this->option->alertMessages->email->message;
```

Second, we get the destination recipient (in this case the developers e-mail addresses)

```
$devEmails = explode(',', $this->settings->devEmails);
```

As you can see the Alert messages contains **{VARIABLES}** called details in the Alert system. Now we will prepare the details:

```
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

Note that $e is a caught exception, this exception is thrown when the e-mail send process fails. Now that we have it all, let's create an alert:

```
$alert = new Dot_Alert();
```

This is just an empty alert, we will now set the sender, the subject and the message, the sender is set on sending

```
$alert->addHeader( "From: " . $this->settings->siteEmail);
$alert->addHeader( "Reply-To:" . $this->settings->siteEmail );
$alert->addHeader( "X-Mailer: PHP/" . phpversion() ) ;
```

```
$alert->setTo($devEmails);
$alert->setSubject($subject);
$alert->setContent($message);
```

Our message doesn't look that good, the setDetails method will replace our **{VARIABLES}** within subject and message with actual data

```
$alert->setDetails($details);
```

Everything is great, we can now send our alert:

```
$alert->send();
```

## FAQ

**Q: What is a Dot_Alert used for?**
A: Alerts (Dot_Alert's) are e-mails usually sent to the site developers using PHP's mail() function. They shouldn't be used to send regular mail - they only notify the developer that something is wrong.

**Q: What does the Dot_Alert class resemble, and what does an alert contain?**
A: Dot_Alert resembles Dot_Email. Like a mail message, an alert has at least a sender, a subject, a destination and a message, and it can be sent.

**Q: What's an example use of Alerts in Dotkernel?**
A: Dotkernel uses Alerts to notify the developer when an email was not sent successfully, with the message kept in the dots.xml file under the alertMessages section.

**Q: How are the {VARIABLES} in an alert message replaced with real data?**
A: A $details array is prepared (e.g. e_class, site_name, site_url, e_message, to_email, from_email, date_now), and the alert's setDetails() method replaces the {VARIABLES} placeholders in the subject and message with that data.

**Q: What are the steps to build and send an alert?**
A: Create a new Dot_Alert(), add headers such as From, Reply-To and X-Mailer, then call setTo(), setSubject() and setContent(), then setDetails() to fill in the placeholders, and finally call send().
