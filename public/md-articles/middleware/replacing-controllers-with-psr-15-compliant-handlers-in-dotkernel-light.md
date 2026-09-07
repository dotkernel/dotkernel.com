---
title: "Replacing controllers with PSR-15 compliant handlers in Dotkernel Light"
description: "Why and how Dotkernel Light replaced its controllers with PSR-15 compliant handlers, including a worked GetPageViewHandler example."
author: "Florin Bidirean"
date_published: "2025-03-03"
canonical_url: "https://www.dotkernel.com/middleware/replacing-controllers-with-psr-15-compliant-handlers-in-dotkernel-light/"
category: "Middleware"
language: "en"
---

# Replacing controllers with PSR-15 compliant handlers in Dotkernel Light

## TL;DR
The goal of this update is to implement [PSR-15](https://www.php-fig.org/psr/psr-15/) handlers into [Dotkernel Light](https://github.com/dotkernel/light), keeping the application up-to-date with recommended design guidelines, secure, and aligned with standards widely adopted by the PHP community.

The goal of this update is to implement [PSR-15](https://www.php-fig.org/psr/psr-15/) handlers into [Dotkernel Light](https://github.com/dotkernel/light). There are several advantages to using handlers, which we will explore below.

We strive to keep our applications up-to-date with the recommended design guidelines. This ensures that we keep the applications secure, while also implementing standards widely adopted by the PHP community.

## What makes handlers better than controllers?

It's all fine and good if you have one large controller file with several actions, but handlers split the code into manageable chunks that make your life a lot easier in the long run. This follows the first of the SOLID principles. SOLID stands for:

> - S - Single-responsibility Principle
> - O - Open-closed Principle
> - L - Liskov Substitution Principle
> - I - Interface Segregation Principle
> - D - Dependency Inversion Principle

We are focusing on that first `S` in the SOLID acronym. Instead if having multiple actions we would normally include in controllers, with `single-responsibility` the handlers separate each action into its own class. This makes handlers easier to maintain, refactor and test.

Expanding your application is also helped by handlers. Rather than searching for a place to fit in that new code, simply create a handler to keep thing orderly. Your future self or the programmer that takes over from you will thank you for it.

Refactoring is always easier if you don't have to worry about edge cases that are unexpectedly not supported because of an error on your part. Simpler code means refactoring steps are more obvious.

Writing tests for actions that have multiple branches tends to take a lot of time. Since handlers only deal with a single action, your tests only have to inject or bind mocks for that specific action.

## How to implement the Page handler

When it comes to [Dotkernel Light](https://github.com/dotkernel/light), replacing controllers with handlers means we don't need the `dot-controller` package any more. Go ahead and remove it, along with any `Controllers` you may have.

Below we are going to detail how to set up the `GetPageViewHandler`. If you already have Controllers in your application, you will have to repeat the steps below for each controller. Based on your application, you may have to split your actions over multiple Handlers.

For Dotkernel Light we were able to combine the functionality of most of the old Controller's `actions` under a single Handler, since the actions performed a single task - displaying static content. The only exception is `IndexHandler.php` which we opted to leave separate, but its setup is similar to GetPageViewHandler.php.

Handlers use the `ConfigProvider` in each module to map factories under `getDependencies()`. You should already have delegators and aliases, but make sure to add `GetPageViewHandler` under the `factories` key and remove any reference to PageController.

```
public function getDependencies(): array
{
    return ,
        ],
        'factories'  => ,
        'aliases'    => ,
    ];
}
```

`GetPageViewHandlerFactory.php` adds the template as a dependency, making it available in the Handler. We don't need the PageControllerFactory.php file, so go ahead and delete it.

```
<?php

declare(strict_types=1);

namespace Light\Page\Factory;

use Light\Page\Handler\GetPageViewHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class GetPageViewHandlerFactory
{
    /**
     * @param class-string $requestedName
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetPageViewHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        return new GetPageViewHandler($template);
    }
}
```

`GetPageViewHandler.php` determines the template file name from the route name and displays it. No dynamic elements are included, since we are dealing only with static pages right now. If you haven't already, delete PageController.php.

```
<?php

declare(strict_types=1);

namespace Light\Page\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouteResult;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetPageViewHandler implements RequestHandlerInterface
{
    public function __construct(
        protected TemplateRendererInterface $template,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $template = $request->getAttribute(RouteResult::class)->getMatchedRouteName();

        return new HtmlResponse(
            $this->template->render($template)
        );
    }
}
```

And that's it! These are the bare essentials to get yourself started with handlers for a website that displays static pages.

## Additional resources

[PSR-15](https://www.php-fig.org/psr/psr-15/)

[Dotkernel Light](https://github.com/dotkernel/light)

While the [PR for replacing controllers with handlers](https://github.com/dotkernel/light/pull/33) might not be as focused on the task, because it implements a few other bells and whistles, it's worth reviewing since it includes all the coding details.

[Mezzio features](https://docs.mezzio.dev/mezzio/v3/getting-started/features/)

## FAQ

**Q: What is the goal of replacing controllers with PSR-15 handlers?**
A: The goal is to implement PSR-15 handlers into Dotkernel Light, keeping the application up to date with recommended design guidelines, ensuring it stays secure while implementing standards widely adopted by the PHP community.

**Q: What makes handlers better than controllers?**
A: Handlers split code into manageable chunks instead of one large controller file with several actions, following the Single-responsibility Principle (the "S" in SOLID). Each action becomes its own class, which makes handlers easier to maintain, refactor and test, easier to expand with new functionality, and simpler to write focused tests for since each handler only deals with a single action.

**Q: Do you still need the dot-controller package?**
A: No. Replacing controllers with handlers in Dotkernel Light means the dot-controller package is no longer needed and can be removed, along with any existing Controllers.

**Q: How is GetPageViewHandler set up?**
A: Handlers are registered in each module's ConfigProvider, mapping factories under getDependencies(). GetPageViewHandler is added under the factories key, replacing any reference to PageController, and GetPageViewHandlerFactory adds the template renderer as a dependency so it's available in the handler, which determines the template file name from the matched route name and renders it.

**Q: Does every action need its own separate handler?**
A: Not necessarily. For Dotkernel Light, most of the old Controller's actions were combined under a single Handler since they performed a single task - displaying static content. The exception is IndexHandler.php, which was kept separate but is set up similarly to GetPageViewHandler.php. Depending on your application, you may need to split actions over multiple handlers.
