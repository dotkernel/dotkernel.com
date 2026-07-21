---
title: "Using DotKernel with Composer Dependencies"
description: "How to use external Composer dependencies in DotKernel, demonstrated by rendering a barcode with both the non-namespaced Zend Framework 1 and the namespaced Zend Framework 2."
author: "Gabi DJ"
date_published: "2016-04-04"
canonical_url: "https://new.dotkernel.com/dotkernel/using-dotkernel-with-composer-dependencies/"
category: "Dotkernel"
language: "en"
---

# Using DotKernel with Composer Dependencies

## TL;DR

This article covers using external dependencies via Composer within DotKernel applications. Composer autoloads dependencies automatically, so there is no need to include/require them. The example renders a Barcode using Zend Framework 1 (non-namespaced) and Zend Framework 2 (namespaced), and applies to any DotKernel 1.x version running PHP greater than 5.4.0.

## The Composer Dependencies

The ZendFramework 1 Barcode module can only be loaded with ZF1 itself:

```shell
composer require 'zendframework/zendframework1'
```

The ZendFramework 2 Barcode module can be loaded separately:

```shell
composer require 'zendframework/zend-barcode'
```

## Important note

These dependencies can be used anywhere after the `Dot_Kernel::initialize()` function was called.

## Using Non-Namespaced Dependencies (Zend Framework 1)

The class is loaded [PSR-0](http://www.php-fig.org/psr/psr-0/) style, meaning the class name looks like `VendorName_PackageName_ClassName`:

```php
// Only the text to draw is required
$barcodeOptions = array('text' => 'ZEND-FRAMEWORK');

// No required options
$rendererOptions = array();

// Draw the barcode in a new image,
// send the headers and the image
Zend_Barcode::factory(
    'code39', 'image', $barcodeOptions, $rendererOptions
)->render();
```

## Using Namespaced Dependencies (Zend Framework 2)

The class is loaded [PSR-4](http://www.php-fig.org/psr/psr-4/) style, meaning the class name looks like `\VendorName\PackageName\ClassName`:

```php
use Zend\Barcode\Barcode;

// Only the text to draw is required
$barcodeOptions = array('text' => 'ZEND-FRAMEWORK');

// No required options
$rendererOptions = array();

// Draw the barcode in a new image,
// send the headers and the image
Barcode::factory(
    'code39', 'image', $barcodeOptions, $rendererOptions
)->render();
```

### The result

Both examples render the same barcode.

### Tip

The first (ZF1-style) example will work for both namespaced and non-namespaced dependencies if you add this as the first line:

```php
use Zend\Barcode\Barcode as Zend_Barcode;
```

You can then use any of the following to access ZF2's Barcode module:

- `Zend\Barcode\Barcode`
- `Barcode`
- `Zend_Barcode`

## Compatibility

This article works for any DotKernel 1.x version if your server is running PHP greater than 5.4.0.

## FAQ

**Q: Do I need to manually include or require Composer dependencies in DotKernel?**
A: No. Composer automatically loads dependencies, so there is no need to include or require them yourself.

**Q: What example does the article use to demonstrate Composer dependencies?**
A: The article renders a Barcode using Zend Framework 1 as the Non-Namespaced dependency (installed with `composer require 'zendframework/zendframework1'`) and Zend Framework 2 as the Namespaced dependency (installed with `composer require 'zendframework/zend-barcode'`).

**Q: When can these Composer dependencies be used in the application?**
A: These dependencies can be used anywhere after the Dot_Kernel::initialize() function has been called.

**Q: How are non-namespaced (Zend Framework 1) classes loaded compared to namespaced (Zend Framework 2) classes?**
A: Non-namespaced ZF1 classes are loaded PSR-0 style, meaning the class name looks like VendorName_PackageName_ClassName (e.g. Zend_Barcode). Namespaced ZF2 classes are loaded PSR-4 style, meaning the class name looks like \VendorName\PackageName\ClassName (e.g. Zend\Barcode\Barcode).

**Q: Can the same code work for both namespaced and non-namespaced barcode dependencies?**
A: Yes. If you add "use Zend\Barcode\Barcode as Zend_Barcode;" as the first line, the ZF1-style example will work for both, and you can then reference the module as Zend\Barcode\Barcode, Barcode, or Zend_Barcode.

**Q: What DotKernel and PHP versions does this article apply to?**
A: This article works for any DotKernel 1.x version if your server is running PHP greater than 5.4.0.

## Resources

- [Adding Composer support in your DotKernel project](http://www.dotkernel.com/dotkernel/adding-composer-support-in-your-dotkernel-project)
- [Zend Framework 1 manual](http://framework.zend.com/manual/1.12/en/manual.html)
- [Zend Framework 2 manual](http://framework.zend.com/manual/current/en/index.html)
- [PSR-0](http://www.php-fig.org/psr/psr-0/)
- [PSR-4](http://www.php-fig.org/psr/psr-4/)
- [Zend Framework 1 - Rendering a barcode](http://framework.zend.com/manual/1.12/en/zend.barcode.creation.html#zend.barcode.creation.renderering)
- [Zend Framework 2 - Rendering a barcode](http://framework.zend.com/manual/current/en/modules/zend.barcode.creation.html#rendering-a-barcode)
