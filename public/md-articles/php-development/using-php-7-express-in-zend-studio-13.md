---
title: "Using PHP 7 Express in Zend Studio 13"
description: "A walkthrough of Zend Studio 13's PHP 7 Express feature, covering test-project setup, PHP interpreter selection, adding Zend Framework 1, and running a PHP7 compatibility check."
author: "Gabi DJ"
date_published: "2015-12-24"
canonical_url: "https://www.dotkernel.com/php-development/using-php-7-express-in-zend-studio-13/"
category: "PHP Development"
language: "en"
---

# Using PHP 7 Express in Zend Studio 13

## TL;DR

Zend Studio 13 introduces PHP 7 Express, a feature that checks whether pre-PHP7 code will run cleanly on a PHP7 server.
This article walks through setting up a test project with the correct PHP version, verifying the PHP Interpreter setting, adding Zend Framework 1 to the project, and running PHP 7 Express to surface compatibility issues.

## What Is PHP 7 Express?

PHP 7 Express is the newest feature added in Zend Studio, starting with version 13.
This feature is very useful for checking if your pre-PHP7 code will work on a PHP7 server.

## The Project Creation

If you already have the project backup and you are sure you can modify your project, you can skip this step.
If you want to create a new project for testing instead of modifying the original project, make sure the PHP Version is set to PHP 5.6, or whichever PHP version you use in the project.
Choosing the PHP 7 option will hide the PHP 7 Express feature, because Zend Studio assumes a PHP 7 project doesn't need compatibility checks, but if the project is actually using a different version of PHP, compatibility issues might appear.
Long story short: use the exact PHP version your project is using at project creation.

## The 'PHP Interpreter' Selection

If the PHP 7 Express feature won't show up, the issue might be the PHP Interpreter selected.
To check and modify the PHP Interpreter:

- Right click the project.
- Type "interpreter".
- The PHP -> Interpreter option should show up.
- If you only need a specific interpreter for a specific project, check "Enable Project specific settings" - this way you won't affect the other projects in the current workspace.

The PHP 7 Express feature should now show up.

## Project Preparation

As an example, a local project named zend-framework-test is used, and the latest Zend Framework 1 version is added to it.

- Right click on the project.
- Create a new folder named library.
- Download the latest Zend Framework 1 version from [here](http://framework.zend.com/downloads/latest#ZF1), making sure to choose the Full option, not the Full Package one.
- Extract the archive and browse the library folder.
- Copy the Zend folder into your project's library folder.
- If the drag and drop didn't work and you had to manually copy the folder into your project, click the project folder in Zend Studio and press F5 (Refresh).

## Using PHP 7 Express - Testing Zend Framework 1

Using PHP 7 Express is very easy:

- Right-click the project, not a folder or a file, because the feature won't show up.
- Click "Run PHP 7 Express...".
- A confirmation prompt will be displayed.

After the analysis is done, the PHP 7 Express view (or tab) appears in the bottom pane in Zend Studio, depending on the perspective.

## FAQ

**Q: What is PHP 7 Express in Zend Studio?**
A: PHP 7 Express is a feature added in Zend Studio starting with version 13. It's useful for checking whether pre-PHP7 code will work correctly on a PHP7 server.

**Q: What PHP version should you select when creating a test project?**
A: Use the exact PHP version your project actually uses (e.g. PHP 5.6), not PHP 7. Choosing PHP 7 at project creation hides the PHP 7 Express feature, because Zend Studio assumes a PHP 7 project doesn't need compatibility checks.

**Q: What should you check if the PHP 7 Express feature doesn't show up?**
A: Check the PHP Interpreter selected for the project: right click the project, type "interpreter" to find the PHP -> Interpreter option, and optionally enable "Enable Project specific settings" so the change only affects that project.

**Q: How do you run PHP 7 Express once it's available?**
A: Right-click the project itself (not a folder or file, or the feature won't show up), click "Run PHP 7 Express...", and confirm the prompt. After the analysis finishes, results appear in the PHP 7 Express view/tab in the bottom pane.
