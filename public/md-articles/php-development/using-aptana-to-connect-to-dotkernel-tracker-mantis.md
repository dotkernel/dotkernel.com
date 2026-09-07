---
title: "Using Aptana to connect to Dotkernel tracker (Mantis)"
description: "A step-by-step guide to integrating the Aptana IDE with Dotkernel Tracker, a Mantis-based bug tracker, using the Mylyn plugin's Mantis connector."
author: "Teo"
date_published: "2010-10-18"
canonical_url: "https://www.dotkernel.com/php-development/using-aptana-to-connect-to-dotkernel-tracker-mantis/"
category: "PHP Development"
language: "en"
---

# Using Aptana to connect to Dotkernel tracker (Mantis)

## TL;DR
This guide explains how to connect the Aptana IDE to Dotkernel Tracker, the Mantis-based bug tracker used for the Dotkernel application framework, via the Mylyn plugin's Mantis connector.
It walks through installing Aptana and Mylyn, adding Dotkernel Tracker as a task repository, and validating the connection so tickets can be managed directly from the IDE.

In developing **Dotkernel** application framework, we needed a tracking system. **[Dotkernel Tracker](http://www.dotkernel.net/)** is the place where the *bugs* are reported, new *features* are announced and other *general tickets* are added.

**[Dotkernel Tracker](http://www.dotkernel.net/)** is a **[Mantis](http://www.mantisbt.org/)** web-based bugtracking system. To simplify the development of **Dotkernel**, we integrated our web development IDE – **[Aptana](http://www.aptana.com/)** with our bugtracking system – **[Dotkernel Tracker](http://www.dotkernel.net/)**. In the ***Downloads*** section, you will find screenshots with the below steps for **[Using Aptana with the Mylyn connector for Mantis](../download/?did=24)**

**1.** Install **Aptana** 2.0.5 by following these steps: **[http://www.dotkernel.com/php-development/aptana-php-installation-in-aptana-2-x/](../php-development/aptana-php-installation-in-aptana-2-x/)**

**2.** Install the **Mylyn** plugin:

*Aptana* -> *Help* ->*Software Update*

*Add***: [http://download.eclipse.org/tools/mylyn/update/e3.4/](http://download.eclipse.org/tools/mylyn/update/e3.4/)**

**3.** *Aptana*- >*Windows* -> *Show View* -> *Other* -> *Task* *Repository*

**4.** Right click: *Add* *Task* *Repository* -> *Install* *More* *Connectors*

**5.** Select *Mantis* , then *Finish*

**6.** Install ***Mylyn Connector*** *–* ***Mantis***

**7.** From the *Task* *Repository* window, right click to select *Add* *Task* *Repository*-> *Mantis*->*Next*

*Server* : **[http://www.dotkernel.net/api/soap/mantisconnect.php](http://www.dotkernel.net/api/soap/mantisconnect.php)**

*Label*: **Dotkernel Tracker** (or a custom string )

*User ID*: your username from **[http://www.dotkernel.net/](http://www.dotkernel.net/)**. If you don’t have an account, signup **[here](http://www.dotkernel.net/signup_page.php)**

**8.** To check if your connection works, click on *Validate* *Settings*, then *Finish*

**9.** A window will popup asking if you want to create a query;  click *Yes*

**10.** *Select project**: Dotkernel***

*Select filter: **[Build-in] Latest Dot Kernel tasks***

*Query Title:* change title – optional

*Finish*

## FAQ

**Q: What is Dotkernel Tracker?**
A: Dotkernel Tracker is a Mantis-based, web-based bugtracking system used by the Dotkernel application framework, where bugs are reported, new features are announced, and other general tickets are added.

**Q: How is Aptana integrated with Dotkernel Tracker?**
A: Aptana, the web development IDE used for Dotkernel development, is integrated with Dotkernel Tracker via the Mylyn plugin's Mantis connector, so tickets can be managed directly from within the IDE.

**Q: What plugin do you need to install in Aptana before connecting to Mantis?**
A: You need to install the Mylyn plugin via Aptana -> Help -> Software Update, adding the update site http://download.eclipse.org/tools/mylyn/update/e3.4/, and then install the Mylyn Connector for Mantis.

**Q: What information is needed to add Dotkernel Tracker as a task repository?**
A: You need the server URL (http://www.dotkernel.net/api/soap/mantisconnect.php), a label of your choosing (e.g. "Dotkernel Tracker"), and your User ID, which is your username from dotkernel.net (you can sign up if you don't already have an account).
