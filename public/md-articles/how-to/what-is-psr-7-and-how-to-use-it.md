---
title: "What is PSR-7 and how to use it"
description: "A practical reference to PSR-7's HTTP message interfaces and a cheatsheet-style walkthrough of working with headers and message bodies using Zend Diactoros."
author: "Gabi DJ"
date_published: "2017-05-15"
canonical_url: "https://www.dotkernel.com/how-to/what-is-psr-7-and-how-to-use-it/"
category: "How to's"
language: "en"
---

# What is PSR-7 and how to use it

## TL;DR

PSR-7 defines a set of common interfaces from the PHP Framework Interop Group for representing HTTP messages and URIs, and any application built on those interfaces is a PSR-7 application.
This article lists the PSR-7 interfaces as a cheatsheet, then walks through practical examples using Zend Diactoros: adding, appending, reading, and removing HTTP headers, and reading, writing, appending, and prepending content to a PSR-7 message body via its stream interface.

PSR-7 is a set of common interfaces defined by PHP Framework Interop Group.
These interfaces are representing HTTP messages, and URIs for use when communicating trough HTTP.
Any web application using this set of interfaces is a PSR-7 application.
More about interfaces and interfaces examples can be found [here](http://php.net/manual/language.oop5.interfaces.php).

## Interfaces

In this section the Interfaces methods will be listed.
The purpose of this list is to help in finding the methods when working with PSR-7.
This can be considered as a cheatsheet for PSR-7 interfaces.
The interfaces defined in PSR-7 are the following:

| Class Name | Description |
|---|---|
| [`Psr\Http\Message\MessageInterface`](http://www.php-fig.org/psr/psr-7/#psrhttpmessagemessageinterface) | Representation of a HTTP message |
| [`Psr\Http\Message\RequestInterface`](http://www.php-fig.org/psr/psr-7/#psrhttpmessagerequestinterface) | Representation of an outgoing, client-side request. |
| [`Psr\Http\Message\ServerRequestInterface`](http://www.php-fig.org/psr/psr-7/#psrhttpmessageserverrequestinterface) | Representation of an incoming, server-side HTTP request. |
| [`Psr\Http\Message\ResponseInterface`](http://www.php-fig.org/psr/psr-7/#psrhttpmessageresponseinterface) | Representation of an outgoing, server-side response. |
| [`Psr\Http\Message\StreamInterface`](http://www.php-fig.org/psr/psr-7/#psrhttpmessagestreaminterface) | Describes a data stream |
| [`Psr\Http\Message\UriInterface`](http://www.php-fig.org/psr/psr-7/#psrhttpmessageuriinterface) | Value object representing a URI. |
| [`Psr\Http\Message\UploadedFileInterface`](http://www.php-fig.org/psr/psr-7/#psrhttpmessageuploadedfileinterface) | Value object representing a file uploaded through an HTTP request. |

## Working with PSR-7

The following examples will illustrate how basic operations are done in PSR-7.
Zend Diactoros is an implementation for PSR-7 interfaces.
It will be used to illustrate these examples.
Installation guide for Zend Diactoros: [Zend Diactoros Documentation – Installation](https://zendframework.github.io/zend-diactoros/install/)

> All other PSR-7 implementations should have the same behaviour.

Alternative [PSR-7 implementations](https://packagist.org/search/?q=psr7%20implementation).

To use the `Zend Diactoros` classes add this at the beggining of the php file:

```php
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;
// autoloading
$request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$response = new Response();
```

> Note: The article applies to all PSR-7 implementations from this point forward.

## Working with HTTP Headers

#### Adding Headers to Response

```php
$response->withHeader('My-Custom-Header', 'My Custom Message');
```

#### Appending Values to Headers

```php
$response->withAddedHeader('My-Custom-Header', 'The second message');
```

#### Checking if Header Exists

```php
$response->hasHeader('My-Custom-Header'); // will return true
```

> Note: My-Custom-Header was only added in the Response

#### Getting Comma-Separated Values from a Header (Also Applies to Request)

```php
// getting value from request headers
$request->getHeaderLine('Content-Type'); // will return: "text/html; charset=UTF-8"
// getting value from response headers
$response->getHeaderLine('My-Custom-Header'); // will return:  "My Custom Message; The second message"
```

#### Getting Array of Value from a Header (Also Applies to Request)

```php
// getting value from request headers
$request->getHeader('Content-Type'); // will return:
// getting value from response headers
$response->getHeader('My-Custom-Header'); // will return:
```

#### Removing Headers from HTTP Messages

```php
// removing a header from Request, removing deprecated "Content-MD5" header
$request->withoutHeader('Content-MD5');
// removing a header from Response
// effect: the browser won't know the size of the stream
// the browser will download the stream till it ends
$response->withoutHeader('Content-Length');
```

## Working with HTTP Message Body

When working with the PSR-7 there are two methods of implementation:

#### 1. Getting the Body Separately

> This method makes the body handling easier to understand and is useful when repeatedly calling body methods. (You only call `getBody()` once). Using this method mistakes like `$response->write()` are also prevented.

```php
$body = $response->getBody();
// operations on body, eg. read, write, seek
// ...
// replacing the old body
$response->withBody($body);
// this last statement is optional as we working with objects
// in this case the "new" body is same with the "old" one
// the $body variable has the same value as the one in $request, only the reference is passed
```

#### 2. Working Directly on Response

> This method is useful when only performing few operations as the `$request->getBody()` statement fragment is required

```php
$response->getBody()->write('hello');
```

## Getting the Body Contents

The following snippet gets the contents of a stream contents.

> Note: Streams must be rewinded, if content was written into streams, it will be ignored when calling `getContents()` because the stream pointer is set to the last character, which is end of stream.

```php
$body = $response->getBody();
$body->rewind(); // or $body->seek(0);
$bodyText = $body->getContends();
```

> Note: If `$body->seek(1)` is called before `$body->getContents()`, the first character will be ommited as the starting pointer is set to `1`, not `0`. This is why using `$body->rewind()` is recommended.

## Append to Body

```php
$response->getBody()->write('Hello'); // writing directly
$body = $request->getBody(); // which is a `StreamInterface`
$body->write('xxxxx');
```

## Prepend to Body

Prepending is different when it comes to streams.
The content must be copied before writing the content to be prepended.
The following example will explain the behaviour of streams.

```php
// assuming our response is initially empty
$body = $repsonse->getBody();
// writing the string "abcd"
$body->write('abcd');
// seeking to start of stream
$body->seek(0);
// writing 'ef'
$body->write('ef'); // at this point the stream contains "efcd"
```

#### Prepending by Rewriting Separately

```php
// assuming our response body stream only contains: "abcd"
$body = $response->getBody();
$body->rewind();
$contents = $body->getContents(); // abcd
// seeking the stream to beginning
$body->rewind();
$body->write('ef'); // stream contains "efcd"
$body->write($contents); // stream contains "efabcd"
```

> Note: `getContents()` seeks the stream while reading it, therefore if the second `rewind()` method call was not present the stream would have resulted in `abcdefabcd` because the `write()` method appends to stream if not preceeded by `rewind()` or `seek(0)`.

#### Prepending by Using Contents as a String

```php
$body = $response->getBody();
$body->rewind(); // or $body->seek(0);
$bodyText = $body->getContends();
```

More information can be found in the [PSR-7 article](https://docs.dotkernel.com/Prerequisites/PSR-7.html) in [Dotkernel3 documentation portal](https://docs.dotkernel.com/).

Sources: [PSR-7: HTTP messages](http://www.php-fig.org/psr/psr-7/), [zend-diactoros](https://zendframework.github.io/zend-diactoros/)

## FAQ

**Q: What is PSR-7?**
A: PSR-7 is a set of common interfaces defined by the PHP Framework Interop Group, representing HTTP messages and URIs for use when communicating through HTTP. Any web application using this set of interfaces is a PSR-7 application.

**Q: What interfaces does PSR-7 define?**
A: PSR-7 defines MessageInterface (representation of an HTTP message), RequestInterface (an outgoing, client-side request), ServerRequestInterface (an incoming, server-side HTTP request), ResponseInterface (an outgoing, server-side response), StreamInterface (a data stream), UriInterface (a URI value object), and UploadedFileInterface (a file uploaded through an HTTP request).

**Q: What implementation is used to illustrate the PSR-7 examples?**
A: Zend Diactoros is used as the PSR-7 implementation for the examples, though the article notes that all other PSR-7 implementations should have the same behaviour.

**Q: How do you add or check headers on a response?**
A: Use `$response->withHeader('My-Custom-Header', 'My Custom Message')` to add a header, `$response->withAddedHeader(...)` to append another value to it, and `$response->hasHeader('My-Custom-Header')` to check whether it exists.

**Q: Why must a stream be rewound before reading its contents?**
A: If content was written into a stream, calling `getContents()` without first calling `$body->rewind()` (or `$body->seek(0)`) will return nothing, because the stream pointer is left at the end of the stream after writing.
