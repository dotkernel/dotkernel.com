---
title: "Dependency Injection made easy in Laminas/Mezzio applications"
description: "Introduces DotKernel's dot-dependency-injection package, which autowires constructor dependencies in Laminas/Mezzio applications via a PHP attribute instead of a hand-written factory per class."
author: "Claudiu Pintiuta"
date_published: "2024-06-20"
canonical_url: "https://www.dotkernel.com/dotkernel/dependency-injection-made-easy-in-laminas-mezzio-applications/"
category: "Dotkernel"
language: "en"
---

# Dependency Injection made easy in Laminas/Mezzio applications

## TL;DR

DotKernel's dot-dependency-injection package autowires constructor dependencies in Laminas/Mezzio (and other PSR-11) applications, removing the need to write and maintain a custom factory class for every service.
Instead of a bespoke factory, you add an attribute to the class constructor and register a single shared AttributedServiceFactory in your ConfigProvider.
The package requires Doctrine ORM but can still be used in applications that don't integrate Doctrine, and it also supports injecting Doctrine repositories directly instead of fetching them from the EntityManager.

> Note: The package requires Doctrine ORM. Still, it can be used in applications which do not integrate Doctrine.

So, first thing first, the problem.
You have a Laminas / Mezzio application with a bunch of services that you need to use in a, let's say, controller class or in any other class, and you are tired of building, updating, and maintaining factories every time you add a new dependency to your class.

DotKernel has you covered.
We built a tool to autowire those dependencies in your class.
There is no need for factories for every class you make.
Just use one "factory" class that you tie to your custom class in the config, and that's it.

Sounds easy, right?
Let's finish with the chat and speak some code, first showing the problem and then the solution.

> The examples below are from the [DotKernel API framework](https://github.com/dotkernel/api), but the pattern applies to all laminas and mezzio applications and to all PSR-11 applications.

```php
class UserHandler implements RequestHandlerInterface
{
    public function __construct(
        protected UserServiceInterface $userService,
        protected array $config,
    ) {
    }
}
```

Above, we have a UserHandler (Controller), and we have the required dependencies: `UserService` and `config`.
Normally, we would build a factory for this to get things from the container and put them in the config provider like this:

```php
class UserHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        $userService = $container->get(UserService::class);
        assert($userService instanceof UserService);

        $config = $container->get('config');

        return new UserHandler($userService, $config);
    }
}
```

And in the config provider, we would have the following:

```php
public function getDependencies(): array
{
    return
    ];
}
```

In one more example, let's look at the real-world required dependencies for `UserService`, the dependency that is required for `UserHandler`.

```php
class UserService implements UserServiceInterface
{
    public function __construct(
        protected UserRoleServiceInterface $userRoleService,
        protected MailService $mailService,
        protected TemplateRendererInterface $templateRenderer,
        protected OAuthAccessTokenRepository $oAuthAccessTokenRepository,
        protected OAuthRefreshTokenRepository $oAuthRefreshTokenRepository,
        protected UserRepository $userRepository,
        protected UserDetailRepository $userDetailRepository,
        protected UserResetPasswordRepository $userResetPasswordRepository,
        protected LoggerInterface $logger,
        protected array $config = [],
    ) {
    }
}
```

Now consider that we need to build the factory for this and update it when we add a new dependency, and so on.
We'd also need to build the logic in the factory to handle any dependencies missing from the container.
Painful, right?

Now let's use DotKernel's [dot-dependency-injection](https://github.com/dotkernel/dot-dependency-injection) package to inject the required dependency into your class.

After you install the package, your class needs to `use Dot\DependencyInjection\Attribute\Inject`, then you need to add the `#` attribute to the constructor definition to specify which dependencies should be injected.

```php
use Dot\DependencyInjection\Attribute\Inject;

class UserHandler implements RequestHandlerInterface
{
    #
    public function __construct(
        protected UserServiceInterface $userService,
        protected array $config,
    ) {
    }
}
```

Add the `Dot\DependencyInjection\Factory\AttributedServiceFactory` class to your `ConfigProvider`:

```php
public function getDependencies(): array
{
    return
    ];
}
```

That's right, the `AttributedServiceFactory` class is the only one you need to add to your config, so you are ready to go.
This class will "build" the factory for you and will handle all the logic if any dependencies are not found in the container, with appropriate exceptions and messages.

One more time, let's see how the `UserService` will look now.

```php
class UserService implements UserServiceInterface
{
    use Dot\DependencyInjection\Attribute\Inject;

    #
    public function __construct(
        protected UserRoleServiceInterface $userRoleService,
        protected MailService $mailService,
        protected TemplateRendererInterface $templateRenderer,
        protected OAuthAccessTokenRepository $oAuthAccessTokenRepository,
        protected OAuthRefreshTokenRepository $oAuthRefreshTokenRepository,
        protected UserRepository $userRepository,
        protected UserDetailRepository $userDetailRepository,
        protected UserResetPasswordRepository $userResetPasswordRepository,
        protected LoggerInterface $logger,
        protected array $config = [],
    ) {
    }

}
```

## And, That's Not All.

If you use Doctrine and the repository pattern and you don't want to get your repository from `EntityManager` and want to inject it into your service, this package covers that too.
The principle is the same, and for more insight about this, you can check the package documentation at [dot-dependency-injection](https://docs.dotkernel.org/dot-dependency-injection/).

## FAQ

**Q: What problem does dot-dependency-injection solve?**
A: In Laminas/Mezzio applications, developers normally have to build, update, and maintain a factory class for every class that needs dependencies. dot-dependency-injection autowires those dependencies instead, so you don't need a factory for every class.

**Q: Does dot-dependency-injection require Doctrine ORM?**
A: The package requires Doctrine ORM, but the article notes it can still be used in applications that don't integrate Doctrine.

**Q: How do you mark a class's constructor dependencies for injection?**
A: Import Dot\DependencyInjection\Attribute\Inject in the class, then add the attribute to the constructor definition to specify which dependencies should be injected.

**Q: What do you need to add to the ConfigProvider to use this package?**
A: Only the Dot\DependencyInjection\Factory\AttributedServiceFactory class needs to be added to your config's dependencies. It builds the factory for you and handles the logic for dependencies missing from the container, with appropriate exceptions and messages.

**Q: Can this package be used with the Doctrine repository pattern?**
A: Yes. If you don't want to fetch a repository from the EntityManager and instead want to inject it directly into your service, the article says this package covers that too, following the same principle, with more details in the package documentation.
