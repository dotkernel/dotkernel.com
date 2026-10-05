---
title: "Attributes vs. factories"
description: "Dotkernel's dot-dependency-injection package lets you wire a service with an #[Inject] attribute or a hand-written factory. This article compares both approaches with real dotkernel/api code and explains when each one fits."
author: "Florin Bidirean"
date_published: "2026-10-01"
canonical_url: "https://www.dotkernel.com/best-practice/attributes-vs-factories/"
category: "Best Practice"
language: "en"
---

# Attributes vs. Hand-Written Factories

## TL;DR

Attributes remove one factory file per service and keep dependencies next to the constructor they feed, while hand-written factories keep static type checking and full control over construction. Use #[Inject] for plain constructor wiring, keep factories for computed, conditional, decorated or third-party classes, and mix both freely in the same Mezzio application.

## Introduction

Dependency injection (DI) is simple to define: a class receives the objects it needs instead of building them itself.
As Fabien Potencier puts it in [What is Dependency Injection?](https://fabien.potencier.org/what-is-dependency-injection.html), components are given their dependencies through their constructors, methods or fields.
The payoff is flexibility, because you can swap an implementation without touching the class that uses it, testability, because you can pass a mock, and separation of concerns, because a class does its own job and leaves object creation to someone else.
For required dependencies, the constructor is the natural place to receive them.

Someone still has to build those objects, and in a [PSR-11](https://www.php-fig.org/psr/psr-11/) application that someone is the container.
[PSR-11](https://www.php-fig.org/psr/psr-11/) standardizes only how you retrieve entries from a container, not how they are built: `ContainerInterface` defines just two methods, `get()` and `has()`.
The factories and delegators configuration you'll see below belongs to `ServiceManager`.
It also gives a clear warning: "Users SHOULD NOT pass a container into an object so that the object can retrieve its own dependencies."
That is the service locator anti-pattern, and it is why the container belongs in a factory, not inside your service.

The classic answer is one factory class per service.
It works, but every new constructor argument means editing two files, and the factory can quietly drift out of sync with the class it builds.
Dotkernel's [dot-dependency-injection](https://github.com/dotkernel/dot-dependency-injection) package offers a second answer: declare the dependencies with an `#[Inject]` attribute on the constructor and register one shared factory, `AttributedServiceFactory`, for every service.
We introduced the package in [Dependency Injection made easy in Laminas/Mezzio applications](https://www.dotkernel.com/dotkernel/dependency-injection-made-easy-in-laminas-mezzio-applications/), and the [package documentation](https://docs.dotkernel.org/dot-dependency-injection/v1/attributes-vs-factories/) covers the trade-offs.

This article puts the two side by side and helps you choose.
The real-world code comes from the [Dotkernel API](https://github.com/dotkernel/api) repository.

## Comparison Table

| Aspect | Hand-written factory | `#[Inject]` attribute |
| --- | --- | --- |
| Files per service | 2: the class and its factory | 1: the class |
| Static type checking | Verified by your IDE and static analysis | None; identifiers are strings resolved at runtime, and PHP's type declarations catch a mismatch only when the service is built |
| Argument order | Kept in sync manually, but verified statically in the `new` call (named arguments also possible) | Kept in sync manually; a mistake surfaces only at runtime |
| Reflection | None when the service is built | One `ReflectionClass` per service creation (usually once per request, since services are shared by default) |
| Conditional wiring | Fully supported | Not supported |
| Third-party classes | Works on any class | Only classes you can annotate |
| Config access | `$container->get('config')['user']` | `'config.user'` |
| Error handling | Whatever you write | Uniform `Dot\DependencyInjection\Exception\ExceptionInterface` |
| Doctrine repositories | A factory for each repository | `AttributedRepositoryFactory` |

Rule of thumb: if the constructor only needs things the container already holds, use the attribute. If construction needs logic, use a factory.

## Code Examples from dotkernel/api

### A Service Wired with an Attribute

`UserAvatarService` needs a Doctrine repository and the application config.
In dotkernel/api, the class declares both on the constructor:

```php
use Core\User\Repository\UserAvatarRepository;
use Dot\DependencyInjection\Attribute\Inject;

class UserAvatarService implements UserAvatarServiceInterface
{
    /**
     * @param array<non-empty-string, mixed> $config
     */
    #[Inject(
        UserAvatarRepository::class,
        'config',
    )]
    public function __construct(
        protected UserAvatarRepository $userAvatarRepository,
        protected array $config,
    ) {
    }
}
```

The only wiring left is one line in the module's `ConfigProvider`:

```php
use Dot\DependencyInjection\Factory\AttributedServiceFactory;

'factories' => [
    UserAvatarService::class => AttributedServiceFactory::class,
],
```

The same `AttributedServiceFactory` entry is repeated for every handler, service and middleware in the module.
The attribute lists the container identifiers in constructor order, which is the order you must keep in sync by hand.

### The Same Service with a Hand-Written Factory

The factory below is illustrative: dotkernel/api does not ship it, but it is what the same wiring looks like without the attribute.

```php
use Core\User\Repository\UserAvatarRepository;
use Psr\Container\ContainerInterface;

class UserAvatarServiceFactory
{
    public function __invoke(ContainerInterface $container): UserAvatarService
    {
        return new UserAvatarService(
            $container->get(UserAvatarRepository::class),
            $container->get('config'),
        );
    }
}
```

Its `ConfigProvider` entry points to the new class, not to the shared one:

```php
'factories' => [
    UserAvatarService::class => UserAvatarServiceFactory::class,
],
```

That is two files instead of one, and a second place to edit whenever the constructor changes.
In return, the `new UserAvatarService(...)` call is checked statically: a wrong argument type or order is caught by your IDE and static analysis before the code runs.
With the attribute, the same mistake shows up when the service is built.

### Doctrine Repositories

Repositories are where attributes save the most repetition.
In dotkernel/api, the repository declares which entity it serves:

```php
use Core\App\Repository\AbstractRepository;
use Core\User\Entity\UserAvatar;
use Dot\DependencyInjection\Attribute\Entity;

#[Entity(name: UserAvatar::class)]
class UserAvatarRepository extends AbstractRepository
{
}
```

The module `ConfigProvider` registers it with the dedicated factory:

```php
use Dot\DependencyInjection\Factory\AttributedRepositoryFactory;

'factories' => [
    UserAvatarRepository::class => AttributedRepositoryFactory::class,
    UserDetailRepository::class => AttributedRepositoryFactory::class,
    UserRepository::class       => AttributedRepositoryFactory::class,
],
```

Without it, each repository needs its own factory that fetches the entity manager and asks it for the right repository, the same boilerplate repeated per entity.

### Where a Factory Still Wins

Some objects cannot be described by a list of container identifiers.
`ErrorResponseGeneratorFactory` in dotkernel/api reads a config value, with a fallback when the container has no config at all:

```php
class ErrorResponseGeneratorFactory
{
    public function __invoke(ContainerInterface $container): ErrorResponseGenerator
    {
        $config = $container->has('config') ? $container->get('config') : [];
        assert(is_array($config));

        return new ErrorResponseGenerator($config['debug'] ?? false);
    }
}
```

Three things make this a factory job.
`ErrorResponseGenerator` belongs to Mezzio, so you cannot annotate it.
The `has()` check is conditional logic.
The constructor receives a computed scalar, `$config['debug'] ?? false`, not something the container already holds.

`EntityListenerResolverFactory` is another case: it builds an `EntityListenerResolver` that receives the container itself.

```php
class EntityListenerResolverFactory
{
    public function __invoke(ContainerInterface $container): EntityListenerResolver
    {
        return new EntityListenerResolver($container);
    }
}
```

An attribute cannot express "give me the container", and this resolver exists to look up Doctrine entity listeners on demand.
That is a deliberate exception.
It does not make the container a general-purpose dependency for your services.

### Using Both in One Project

The two approaches share the same `ConfigProvider`.
dotkernel/api's `App` module, for example, wires its services with the attribute and still decorates selected services with delegators:

```php
'delegators' => [
    GetIndexResourceHandler::class  => [HandlerDelegatorFactory::class],
    ProblemDetailsMiddleware::class => [ProblemDetailsDelegatorFactory::class],
],
'factories'  => [
    GetIndexResourceHandler::class => AttributedServiceFactory::class,
    ErrorReportService::class      => AttributedServiceFactory::class,
],
```

A service can be built by `AttributedServiceFactory` and still be wrapped by a delegator.
Which factory applies to which service is decided entirely by the configuration.

### The Anti-Pattern to Avoid

Both approaches keep the container out of your classes.
This is what PSR-11 warns against:

```php
class UserAvatarService
{
    public function __construct(
        protected ContainerInterface $container,
    ) {
    }

    public function deleteAvatar(User $user): void
    {
        $repository = $this->container->get(UserAvatarRepository::class);
        // ...
    }
}
```

The dependencies are now hidden: the constructor signature no longer says what the class needs, and a missing service no longer fails when `UserAvatarService` is built, but only when `deleteAvatar()` is called.
That may be a rarely used code path that slips past your tests and fails in production.
Tests also have to build a container just to run one method.
Declare `UserAvatarRepository` in the constructor instead, using either approach from this article.

## Conclusion

Attributes and factories are two ways of delivering the same constructor injection.
Default to `#[Inject]` when a class only needs services and config the container already holds: it means one file per service, dependencies visible next to the constructor, and uniform error messages.
Reach for a hand-written factory when construction involves logic or when a class is not yours to annotate.
Decorating a service is a separate concern: the delegator is a factory you write, but the service it wraps can still be built with `#[Inject]`.
You do not have to choose once for the whole project, since the `ConfigProvider` decides per service.

To go further, read the [dot-dependency-injection documentation](https://docs.dotkernel.org/dot-dependency-injection/v1/attributes-vs-factories/) and browse the [Dotkernel API](https://github.com/dotkernel/api) source for both styles in a working application.

## FAQ

**Q: Can I mix attributes and factories in the same project?**
A: Yes. The `ConfigProvider` decides which factory builds each service, so some services can use `AttributedServiceFactory` while others use a hand-written factory. dotkernel/api does exactly that.

**Q: Do attributes slow the application down?**
A: There is a small cost: the attribute approach uses one `ReflectionClass` per service creation, while a hand-written factory has no reflection when the service is built.
Because ServiceManager shares services by default, a service is usually created once per request rather than on every `get()` call, so the cost is paid once per service per request.
The documentation does not publish benchmark figures, so measure your own application if it matters.

**Q: Why does `#[Inject]` lose static type checking?**
A: The attribute lists container identifiers as strings, such as `UserAvatarRepository::class` or `'config'`, and they are resolved at runtime.
Static analysis cannot check that they match the constructor's parameter types or order, whereas a factory's `new` call is checked statically.
PHP still enforces the constructor's type declarations, so a mismatch throws a `TypeError` when the service is built rather than going unnoticed; it just isn't caught before the code runs.

**Q: When must I write a factory?**
A: When construction is computed or conditional, when the class is a third-party class you cannot annotate, or when the dependency is not a constructor argument. To decorate a service you write a delegator factory, but the service itself can still be wired with `#[Inject]`, as the `App` module example shows.

**Q: How does this relate to PSR-11 and the service locator anti-pattern?**
A: PSR-11 says users should not pass a container into an object so that it can retrieve its own dependencies. Both approaches follow that: the container is used inside the factory, never inside your service, and your service receives its dependencies through the constructor.

**Q: Does `#[Inject]` need Doctrine?**
A: The package requires Doctrine ORM, but you can use `#[Inject]` for services that have nothing to do with Doctrine. `AttributedRepositoryFactory` and the `#[Entity]` attribute are the Doctrine-specific parts.
