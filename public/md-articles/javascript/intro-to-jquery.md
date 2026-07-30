---
title: "Intro to jQuery"
description: "A short introduction to jQuery basics for early Dotkernel developers switching from Dojo, covering the jQuery object, selectors, DOM manipulation, events, and Ajax."
author: "Adrian"
date_published: "2011-06-01"
canonical_url: "https://www.dotkernel.com/javascript/intro-to-jquery/"
category: "Javascript"
language: "en"
---

# Intro to jQuery

## TL;DR

Starting with Dotkernel's 1.5 release, the framework switched from Dojo to jQuery, and this post is a quick primer on jQuery basics.
It covers the jQuery (`$`) object and CSS-style selectors, chaining methods to manipulate matched elements, binding events like click, and making Ajax calls with `$.get()` and `$.getJSON()`.

Starting with the 1.5 release, Dotkernel will make the switch from Dojo to jQuery.
On jQuery's website, there's an excellent "[Getting started with jQuery tutorial](http://docs.jquery.com/Tutorials:Getting_Started_with_jQuery)", as well as a extensive [documentation](http://api.jquery.com/) for the framework, so I'll just go over a few basic concepts and common examples.

## The jQuery Object

jQuery is the global object that contains all of jQuery's functionality.
You will probably want to use $ instead which is a synonim of the same object, only faster to type.
It contains a few useful methods such as [jQuery.map](http://api.jquery.com/jQuery.map/), but it's mostly used with a selector parameter to retrieve a set of matched elements.

## Selectors

jQuery supports the same selectors as CSS (note: all selectors will work even in browsers that don't actually have CSS support for them)

### Examples

```javascript
// the element that matches an id
$("#someId");
// all elements with a certain class
$(".someClass")
// all odd rows from a certain table
$("table#monthlyStats tbody tr:nth-child(odd)")
```

The results of these expressions is a jQuery object that contains one, more, or no matched elements.

## Manipulating Matched Elements

There are a number of functions that can be applied to a jQuery object, for example:

```javascript
$("input#username").addClass("error")
```

Since most of these functions will also return the same jQuery object, they can be chained.
For example, the following expression will set the text of the #errors element and fade it in:

```javascript
$("#errors").text("The was an eror").fadeIn()
```

## Events

Adding events in Javascript instead of using "onclick" attributes in HTML means that the markup is much cleaner, and you get better support for more events in all browsers.
In the following example, a click event is attached to an element.
The function that will be called when the event is fired (callback) is passed as an argument to the click function

```javascript
$("#helpButton").click(function(){
    $("#helpMessage").show()
});
```

## Ajax

jQuery has [many ajax helper functions](http://api.jquery.com/category/ajax/), here is a simple example that will replace the contents of an element with data loaded from the server:

```javascript
$.get("get-news.php", function(result){
   $("#news").html(result)
})
```

## Putting It All Together

The following snippet can be used on a registration form to check whether the username already exists, before the form is submitted.

```javascript
$(document).ready(
  $("input#username").blur(function(){
    $.getJSON(
      "/check-username.php",
      {
         username:$("input#username").val()
      },
      function(data){
        if (data.taken === true){
          $("input#username").addClass("taken")
        }else{
          $("input#username").removeClass("taken")
        }
      }
  });
);
```

## FAQ

**Q: Why is Dotkernel switching to jQuery?**
A: Starting with the 1.5 release, Dotkernel makes the switch from Dojo to jQuery.

**Q: What is the jQuery object, and what is $?**
A: jQuery is the global object that contains all of jQuery's functionality. $ is a synonym for the same object that's faster to type, and is mostly used with a selector parameter to retrieve a set of matched elements.

**Q: What selectors does jQuery support?**
A: jQuery supports the same selectors as CSS, and these selectors work even in browsers that don't natively support them, for example `$("#someId")`, `$(".someClass")`, or `$("table#monthlyStats tbody tr:nth-child(odd)")`.

**Q: How do you attach a click event to an element?**
A: Pass a callback function to the `click` function on a jQuery selection, for example: `$("#helpButton").click(function(){ $("#helpMessage").show() });`. This keeps markup cleaner than using "onclick" attributes in HTML.

**Q: How does jQuery handle Ajax requests?**
A: jQuery provides ajax helper functions such as `$.get()` and `$.getJSON()`, for example loading data from the server and replacing an element's contents with `$.get("get-news.php", function(result){ $("#news").html(result) })`.
