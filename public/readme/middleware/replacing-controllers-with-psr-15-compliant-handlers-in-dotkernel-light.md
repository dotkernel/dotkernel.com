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

## What makes handlers better than controllers?

Handlers split code into manageable chunks instead of one large controller file with several actions, following the first SOLID principle (Single-responsibility). SOLID stands for:

- S - Single-responsibility Principle
- O - Open-closed Principle
- L - Liskov Substitution Principle
- I - Interface Segregation Principle
- D - Dependency Inversion Principle

With single-responsibility, handlers separate each action into its own class, which makes them easier to maintain, refactor, and test. Expanding an application is easier too - rather than searching for a place to fit new code into a controller, you simply create a new handler. Refactoring is simpler because there's less code to worry about breaking, and tests only need to inject or bind mocks for a single action per handler instead of covering multiple branches.

## How to implement the Page handler

Replacing controllers with handlers in Dotkernel Light means the `dot-controller` package is no longer needed and can be removed, along with any existing Controllers.

For Dotkernel Light, most of the old Controller's actions were combined under a single Handler, `GetPageViewHandler`, since they all performed the same task - displaying static content. The only exception is `IndexHandler.php`, which was kept separate but is set up similarly.

Handlers are registered in each module's `ConfigProvider`, mapping factories under `getDependencies()`. Add `GetPageViewHandler` under the `factories` key and remove any reference to `PageController`:

```php
public function getDependencies(): array
{
    return [
        ...,
        'factories'  => [ ... ],
        'aliases'    => [ ... ],
    ];
}
```

`GetPageViewHandlerFactory.php` adds the template renderer as a dependency, making it available in the handler. `PageControllerFactory.php` is no longer needed and can be deleted:

```php
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

`GetPageViewHandler.php` determines the template file name from the route name and displays it. No dynamic elements are included, since it deals only with static pages. `PageController.php` can be deleted:

```php
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

These are the bare essentials to get started with handlers for a website that displays static pages.

## FAQ

**Q: What is the goal of replacing controllers with PSR-15 handlers?**
A: Keeping Dotkernel Light up-to-date with recommended design guidelines, secure, and aligned with PHP community standards.

**Q: What makes handlers better than controllers?**
A: They apply the Single-responsibility Principle, splitting actions into their own classes, which makes them easier to maintain, refactor, expand, and test.

**Q: Do you still need the dot-controller package?**
A: No - it can be removed along with any existing Controllers once handlers replace them.

**Q: How is GetPageViewHandler set up?**
A: Registered as a factory in the module's ConfigProvider (replacing PageController), with GetPageViewHandlerFactory injecting the template renderer, and the handler resolving the template from the matched route name.

**Q: Does every action need its own separate handler?**
A: Not necessarily - in Dotkernel Light most static-page actions were combined into one handler, with only IndexHandler.php kept separate; other applications may need to split actions across multiple handlers.

## Resources

- [PSR-15](https://www.php-fig.org/psr/psr-15/)
- [Dotkernel Light](https://github.com/dotkernel/light)
- [PR for replacing controllers with handlers](https://github.com/dotkernel/light/pull/33)
- [Mezzio features](https://docs.mezzio.dev/mezzio/v3/getting-started/features/)
- [Single Action Handlers in PHP Frameworks](https://dev.to/ilyasdeckers/single-action-handlers-in-php-frameworks-3jai)
