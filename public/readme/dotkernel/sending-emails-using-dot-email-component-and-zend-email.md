---
title: "Sending emails using Dot_Email component and Zend_Email"
description: "Dot_Email extends Zend_Mail with two additional methods, setContent() and send(), and this article covers how to use it and which Zend_Mail methods are inherited."
author: "admin"
date_published: "2010-04-28"
canonical_url: "https://new.dotkernel.com/dotkernel/sending-emails-using-dot-email-component-and-zend-email/"
category: "Dotkernel"
language: "en"
---

# Sending emails using Dot_Email component and Zend_Email

## TL;DR

Dot_Email extends Zend_Mail, so all of Zend_Mail's methods are available in it. Beyond its constructor, Dot_Email itself adds only two methods: setContent() and send(). To send an email you must always call addTo(), setSubject(), one of setBodyText()/setBodyHtml()/setContent(), and finally send().

## Dot_Email overview

Dot_Email is a simple class composed of only two methods (besides the constructor); every other method is inherited from Zend_Mail.

| Method | Description |
|---|---|
| `setContent($content, $format = 'text/plain')` | Sets the body of the email by calling `setBodyText()` if format is "text/plain", or `setBodyHtml()` if format is "text/html". |
| `send()` | Sets the transporter and calls the parent's `send()` method to send the email. |

## Sending an email

For sending the email you MUST use these methods:

```php
$dotEmail = new Dot_Email();
$dotEmail->addTo($email);
$dotEmail->setSubject('Forgot Password');
$dotEmail->setBodyText('Your password is '.$password);
$succeed = $dotEmail->send();
if($succeed)
{
    echo "Message sent!";
}
else
{
    echo "Message failed, not sent!";
}
```

Only if you want to overwrite the default Email and Name, add:

```php
$dotEmail->setFrom($fromEmail, $fromName);
$dotEmail->setReplyTo($fromEmail, $fromName);
```

**NOTE:** you always have to use addTo(), setSubject(), setBodyText() or setBodyHtml() or setContent(), and finally the send() method.

## Methods inherited from Zend_Mail

1. `setBodyText($txt, $charset=null, $encoding=Zend_Mime::ENCODING_QUOTEDPRINTABLE)`
2. `setBodyHtml($html, $charset=null, $encoding=Zend_Mime::ENCODING_QUOTEDPRINTABLE)`
3. `addTo($email, $name='')`
4. `addCc($email, $name='')`
5. `addBcc($email)`
6. `setFrom($email, $name = null)`
7. `setReplyTo($email, $name = null)`
8. `setReturnPath($email)`
9. `setSubject($subject)`
10. `addHeader($name, $value, $append = false)`

## FAQ

**Q: What is Dot_Email?**
A: Dot_Email is a class that extends Zend_Mail, so all methods from Zend_Mail are available in it. Aside from its constructor, Dot_Email itself is composed of only two methods, with everything else inherited from Zend_Mail.

**Q: What are the two methods that Dot_Email adds on top of Zend_Mail?**
A: setContent($content, $format = 'text/plain') sets the body of the email by calling setBodyText() when the format is "text/plain" or setBodyHtml() when the format is "text/html". send() sets the transporter and calls the parent::send() method to send the email.

**Q: What is the minimal sequence of calls needed to send an email with Dot_Email?**
A: You must always use addTo(), setSubject(), and setBodyText() or setBodyHtml() or setContent(), and finally call send(), as shown in the article's example that creates a new Dot_Email, adds a recipient, sets the subject and body text, then sends it and checks the returned success value.

**Q: How do I override the default From and Reply-To addresses?**
A: Only if you want to overwrite the default Email and Name, add calls to setFrom($fromEmail, $fromName) and setReplyTo($fromEmail, $fromName).

**Q: Which Zend_Mail methods are inherited and usable through Dot_Email?**
A: Inherited methods listed in the article include setBodyText(), setBodyHtml(), addTo(), addCc(), addBcc(), setFrom(), setReplyTo(), setReturnPath(), setSubject(), and addHeader().
