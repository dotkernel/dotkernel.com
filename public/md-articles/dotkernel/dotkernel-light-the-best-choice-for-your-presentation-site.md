---
title: "Dotkernel Light: the best choice for your presentation site"
description: "A walkthrough of using Dotkernel Light to build a simple presentation site: adding new pages, managing assets, and configuring Twitter/OpenGraph cards, the top menu, and the footer."
author: "Florin Bidirean"
date_published: "2024-10-14"
canonical_url: "https://www.dotkernel.com/dotkernel/dotkernel-light-the-best-choice-for-your-presentation-site/"
category: "Dotkernel"
language: "en"
---

# Dotkernel Light: the best choice for your presentation site

## TL;DR
Dotkernel Light is a lightweight starting point for a project when you want full control over its functionality, and it grows into something more complex as you add packages.
It comes with routing, templating, error handling, and tests/code quality checks out of the box, but strips out everything a presentation site doesn't need - database, sessions/cookies/flash messages, auth, dependency injection, mail, navigation, CORS, forms, the user/contact/plugin modules.

**Dotkernel Light** is the smallest complete Mezzio application and a good starting point for a project if you want to have **full control over the functionality** it contains. It **can be expanded** into something more complex with the integration of packages based on your requirements.

Its out-of-box functionality is suitable for a **presentation site**:

- Routing
- Templating
- Error handling
- Tests and code quality checks

Presentation sites don't require features that are present in Dotkernel Frontend. The goal of Dotkernel Light is to have **no clutter**, so these features are removed:

- Everything related to the database
- Sessions/Cookies/Flash messages
- Authentication/Authorization
- Dependency Injection
- Mail related stuff
- Navigation
- CORS
- Forms/Validators/InputFilters
- User module
- Contact module
- Plugin module

## The goal of this article

In this article we explore how to use Dotkernel Light for a simple presentation site. We will mention what files to focus on to teach you how to add more pages of content to your site and how to manage their assets.

### Adding new pages

The first step is to add the new pages in `src/Page/src/Controller/PageController.php`. This means adding an `Action` function for each page, as seen below.

```
    public function examplePageAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::example-template')
        );
    }
```

> The url for the new page in this example is `/page/example-page`.

Each page has its own template, so the next step is to create the template files in the `src/Page/templates/page/` folder. For the example above, the `src/Page/templates/page/example-template.html.twig` file was created. We won't include the entire code here, just the basic building blocks. The `content` block is where your page copy goes.

```
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

> Make sure to check the header for any fonts your content requires.

If you haven't already done so, make sure the `npm` is installed and running during your updates with `npm run watch` or run this command after the edits are completed `npm run prod`.

The assets should be copied under the `src/App/assets/` folder.
These are the default asset folders:

- src/App/assets/fonts
- src/App/assets/images
- src/App/assets/js
- src/App/assets/scss

## Optional items

### Twitter and OpenGraph cards

If you want to promote the pages on other platforms, a helpful item is the header section in the `src/App/templates/layout/default.html.twig` file. This is where the Twitter (X) and OpenGraph cards should be placed.

Make sure to update all items based on your page content.

> In the example:
> 
> - `{{ url('home') }}` is the URL for the homepage, but you can also use this code to generate the url for other pages, just like in the canonical URL `{% block canonical %}{{ url(routeName ?? null) }}{% endblock %}`
>   - The `block` item is present to mitigate for not-found pages, e.g. when the url is typed incorrectly
> - The image from `{{ url('home') }}images/app/My-image.png` is found in `public/images/app/My-image``.png`, but it is copied there by the `npm` script from `src/App/assets/images/PHP-REST-API.png`.

```
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

This menu is displayed on all of the pages, in the header. To edit it, go to `src/App/templates/layout/default.html.twig` and update the items under `id="navbarHeader"`. You can use the below as an example.

```
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

> You can also replace the `nav-item` class for the `li` elements with `button-border` for a link that looks more like a button.

### Footer

To edit the footer on all of the pages, search for `<footer class="app-footer">` in the `src/App/templates/layout/default.html.twig` template. We won't include an example here, since the content is usually basic `HTML` and `CSS` with `twig` elements already covered in this article.

## The result of your hard work

Whew, well done! That's all there is to it.

Now you should have a basic idea on how to work on a presentation site. You know how to expand the site with more pages, where to place the assets and how to promote the site.

## Useful links

- See a working example [dotkernel.org](https://www.dotkernel.org)
- [Dotkernel Light](https://github.com/dotkernel/light)
- More from [Dotkernel](https://github.com/dotkernel)

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
