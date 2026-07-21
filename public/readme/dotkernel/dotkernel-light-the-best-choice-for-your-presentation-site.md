---
title: "Dotkernel Light: the best choice for your presentation site"
description: "A walkthrough of using Dotkernel Light to build a simple presentation site: adding new pages, managing assets, and configuring Twitter/OpenGraph cards, the top menu, and the footer."
author: "Florin Bidirean"
date_published: "2024-10-14"
canonical_url: "https://new.dotkernel.com/dotkernel/dotkernel-light-the-best-choice-for-your-presentation-site/"
category: "Dotkernel"
language: "en"
---

# Dotkernel Light: the best choice for your presentation site

## TL;DR

Dotkernel Light is a lightweight starting point for a project when you want full control over its functionality, and it grows into something more complex as you add packages. It comes with routing, templating, error handling, and tests/code quality checks out of the box, but strips out everything a presentation site doesn't need — database, sessions/cookies/flash messages, auth, dependency injection, mail, navigation, CORS, forms, the user/contact/plugin modules.

## What's included vs. removed

| Included out of the box | Removed (not needed for a presentation site) |
|---|---|
| Routing | Everything related to the database |
| Templating | Sessions/Cookies/Flash messages |
| Error handling | Authentication/Authorization |
| Tests and code quality checks | Dependency Injection |
| | Mail related stuff |
| | Navigation |
| | CORS |
| | Forms/Validators/InputFilters |
| | User module |
| | Contact module |
| | Plugin module |

## Adding new pages

1. Add an `Action` function for the page in `src/Page/src/Controller/PageController.php`, for example:

```php
public function examplePageAction(): ResponseInterface
{
    return new HtmlResponse(
        $this->template->render('page::example-template')
    );
}
```

   The URL for this example page would be `/page/example-page`.

2. Create the matching template in `src/Page/templates/page/` — for the example above, `src/Page/templates/page/example-template.html.twig`. Put the page copy inside the `content` block:

```twig
{% extends '@layout/default.html.twig' %}

{% block title %}Page Title{% endblock %}

{% block page_title %}{% endblock %}

{% block content %}
    <div class="page-intro">
        <div class="container">
            <h2>Add title here!</h2>
        </div>
    </div>

    <div>
    Add cool content here!
    </div>
{% endblock %}
```

   Make sure to check the header for any fonts your content requires.

3. Place assets under `src/App/assets/`, in the default folders:
   - `src/App/assets/fonts`
   - `src/App/assets/images`
   - `src/App/assets/js`
   - `src/App/assets/scss`

   Make sure `npm` is installed and running during updates with `npm run watch`, or run `npm run prod` after edits are completed.

## Optional items

### Twitter and OpenGraph cards

To promote pages on other platforms, edit the header section in `src/App/templates/layout/default.html.twig`, where the Twitter (X) and OpenGraph cards are placed. Update all items based on your page content.

- `{{ url('home') }}` generates the homepage URL, and the same pattern is used for other pages, as in the canonical URL block: `{% block canonical %}{{ url(routeName ?? null) }}{% endblock %}` (the `block` is present to handle not-found pages, e.g. mistyped URLs).
- An image referenced as `{{ url('home') }}images/app/My-image.png` is found at `public/images/app/My-image.png`, copied there by the `npm` script from `src/App/assets/images/PHP-REST-API.png`.

```html
<!-- Twitter card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@example">
<meta name="twitter:title" content="Page title">
<meta name="twitter:description" content="Basic description">
<meta name="twitter:image" content="{{ url('home') }}images/app/My-image.png">
<meta name="twitter:image:alt" content="Image alt">

<!-- OpenGraph card -->
<meta property="og:title" content="Page title"/>
<meta property="og:type" content="website"/>
<meta property="og:url" content="{{ url('home') }}"/>
<meta property="og:image" content="{{ url('home') }}images/app/My-image.png"/>
<meta property="og:description" content="Basic description"/>
```

### Top menu

This menu is displayed on all pages, in the header. Edit it in `src/App/templates/layout/default.html.twig`, under `id="navbarHeader"`:

```html
<div class="menu" id="navbarHeader">
    <ul class="navbar-nav mr-auto">
    <li class="nav-item">
        <a class="nav-link" target="_blank" href="https://first.example.com/">First Link</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" target="_blank" href="https://second.example.com/">Second Link</a>
    </li>
    </ul>
</div>
```

You can replace the `nav-item` class for the `li` elements with `button-border` for a link that looks more like a button.

### Footer

To edit the footer on all pages, search for `<footer class="app-footer">` in `src/App/templates/layout/default.html.twig`. Its content is usually basic HTML and CSS with twig elements already covered above.

## FAQ

**Q: What is Dotkernel Light designed for?**
A: It's a good starting point for a project when you want full control over its functionality, and it easily grows into something more complex by integrating packages based on your requirements.

**Q: What functionality does Dotkernel Light include out of the box?**
A: Routing, Templating, Error handling, and Tests and code quality checks.

**Q: What features are removed from Dotkernel Light compared to Dotkernel Frontend?**
A: Everything related to the database, Sessions/Cookies/Flash messages, Authentication/Authorization, Dependency Injection, Mail related stuff, Navigation, CORS, Forms/Validators/InputFilters, the User module, the Contact module, and the Plugin module.

**Q: How do you add a new page to a Dotkernel Light site?**
A: Add an Action function for the page (e.g. examplePageAction()) in src/Page/src/Controller/PageController.php that renders a template, then create the matching template file in src/Page/templates/page/ (e.g. example-template.html.twig), with the page copy placed in its content block.

**Q: Where should new page assets like fonts, images, JS, and CSS be placed?**
A: Under src/App/assets/, in its default folders: src/App/assets/fonts, src/App/assets/images, src/App/assets/js, and src/App/assets/scss. Run npm run watch during edits, or npm run prod once the edits are completed.

**Q: Where do you edit the top menu and footer that appear on every page?**
A: Both live in src/App/templates/layout/default.html.twig: the top menu items are under id="navbarHeader", and the footer content is inside the footer element with class "app-footer".

## Resources

- [dotkernel.org](https://www.dotkernel.org) — a working example
- [Dotkernel Light](https://github.com/dotkernel/light)
- [More from Dotkernel](https://github.com/dotkernel)
