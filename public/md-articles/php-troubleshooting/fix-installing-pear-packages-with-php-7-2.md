---
title: "FIX: Installing PEAR packages with PHP 7.2"
description: "How to fix the 'Cannot use result of built-in function in write context' PEAR error on PHP 7.2 by patching Archive_Tar's func_get_args() call, and how to reinstall the affected packages afterward."
author: "Gabi DJ"
date_published: "2018-05-18"
canonical_url: "https://www.dotkernel.com/php-troubleshooting/fix-installing-pear-packages-with-php-7-2/"
category: "PHP Troubleshooting"
language: "en"
---

# FIX: Installing PEAR packages with PHP 7.2

## TL;DR

On PHP 7.2, installing PEAR packages such as PHP Code Sniffer fails with a "Cannot use result of built-in function in write context" error in Archive_Tar's Tar.php, because a function is called by reference.
The fix is to edit the offending line in Tar.php to drop the by-reference call, then reinstall Archive_Tar and the target package.

## The Issue

If installing a pear package (for instance PHP Code Sniffer), when running:

```bash
pear install PHP_CodeSniffer
```

this error is shown:

```
PHP Fatal error: Cannot use result of built-in function in write context in ...\php\pear\Archive\Tar.php on line 639
Fatal error: Cannot use result of built-in function in write context in ...\pear\Archive\Tar.php on line 639
```

This error is shown because the function is called by reference.
More details about this issue can be found in [this Pull Request](https://github.com/pear/Archive_Tar/pull/18).

## The Solution

You might be tempted to execute the following:

```bash
pear install Archive_Tar
```

which will result in the same error.
Go to the line indicated in the error (639 in this case) and replace:

```php
$v_att_list = &func_get_args();
```

with:

```php
$v_att_list = func_get_args();
```

The above means func_get_args() isn't called by reference anymore.

## Our Recommendation

The above does fix the problem, but it's recommended to also install Archive_Tar again so you have the latest working version.
Run the following command:

```bash
pear install Archive_Tar
```

This will update your Archive_Tar PEAR package.
And to install the code sniffer, run:

```bash
pear install PHP_CodeSniffer
```

## FAQ

**Q: What error occurs when installing PEAR packages with PHP 7.2?**
A: Running a command like pear install PHP_CodeSniffer produces the fatal error "Cannot use result of built-in function in write context" in Archive/Tar.php on line 639, because the function is called by reference.

**Q: How do you fix the "Cannot use result of built-in function in write context" error?**
A: Go to the line indicated in the error (line 639) and replace "$v_att_list = &func_get_args();" with "$v_att_list = func_get_args();", so func_get_args() is no longer called by reference.

**Q: Does running pear install Archive_Tar directly fix the problem?**
A: No, running pear install Archive_Tar on its own results in the same error, since the underlying Archive_Tar.php file still has the by-reference function call.

**Q: What is the recommended full fix sequence?**
A: After manually fixing the func_get_args() line, it's recommended to run pear install Archive_Tar again to get the latest working version of the package, and then run pear install PHP_CodeSniffer to install the code sniffer.
