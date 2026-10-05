# Dependency Injection vs. Factory Design Pattern

Oct 5, 2026 · @bidi

## TL;DR

Declarative dependency injection with an `#[Inject]` attribute removes one factory class per service and keeps dependencies next to the constructor they feed.
The Factory design pattern, one hand-written creational class per service, keeps static type checking and full control over construction.
Use `#[Inject]` for plain constructor wiring, apply the Factory pattern to computed, conditional, decorated, or third-party classes, and mix both freely in the same Mezzio application.

## Introduction

**Dependency injection (DI)** means a class receives the objects it needs instead of building them, usually through its constructor, as Fabien Potencier explains in [What is Dependency Injection?](https://fabien.potencier.org/what-is-dependency-injection.html).
You gain swappable implementations, easy mocking in tests, and classes that leave object creation to someone else.

The **Factory design pattern** answers a different question: *who builds* an object, and where that construction code lives.
On its own it is "pull" (the caller asks a factory), while DI is "push" (dependencies are handed in).

In a [PSR-11](https://www.php-fig.org/psr/psr-11/) application, both meet in the container.
PSR-11 only defines how entries are retrieved (`get()` and `has()`); the factories and delegators configuration below belongs to `ServiceManager`.
PSR-11 also warns against passing the container into an object so it can fetch its own dependencies: the service locator anti-pattern.

The classic approach applies the Factory pattern once per service, so every constructor change means editing two files.
Dotkernel's [dot-dependency-injection](https://github.com/dotkernel/dot-dependency-injection) package takes a DI-first route: an `#[Inject]` attribute on the constructor plus one shared `AttributedServiceFactory` for every service ([introduction](https://www.dotkernel.com/dotkernel/dependency-injection-made-easy-in-laminas-mezzio-applications/), [documentation](https://docs.dotkernel.org/dot-dependency-injection/v1/attributes-vs-factories/)).

Below, both are compared using real code from the [Dotkernel API](https://github.com/dotkernel/api).

## Comparison Table

| Aspect                          | Factory design pattern (hand-written factory)                                                    | Dependency injection (`#[Inject]` attribute)                                                                                   |
|---------------------------------|--------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------|
| Where construction is described | In a dedicated factory class                                                                     | Declared on the constructor itself                                                                                             |
| How dependencies flow           | The factory pulls from the container, then pushes into the constructor                           | The shared factory reads the attribute and pushes into the constructor                                                         |
| Files per service               | 2: the class and its factory                                                                     | 1: the class                                                                                                                   |
| Static type checking            | Verified by your IDE and static analysis                                                         | None; identifiers are strings resolved at runtime, and PHP's type declarations catch a mismatch only when the service is built |
| Argument order                  | Kept in sync manually, but verified statically in the `new` call (named arguments also possible) | Kept in sync manually; a mistake surfaces only at runtime                                                                      |
| Reflection                      | None when the service is built                                                                   | One `ReflectionClass` per service creation (usually once per request, since services are shared by default)                    |
| Conditional wiring              | Fully supported                                                                                  | Not supported                                                                                                                  |
| Third-party classes             | Works on any class                                                                               | Only classes you can annotate                                                                                                  |
| Config access                   | `$container->get('config')['user']`                                                              | `'config.user'`                                                                                                                |
| Error handling                  | Whatever you write                                                                               | Uniform `Dot\DependencyInjection\Exception\ExceptionInterface`                                                                 |
| Doctrine repositories           | A factory for each repository                                                                    | `AttributedRepositoryFactory`                                                                                                  |

Rule of thumb: if the constructor only needs things the container already holds, let dependency injection do the work with the attribute.
If construction needs logic, that logic deserves the Factory pattern.

## Code Examples from dotkernel/api

### A Service Wired with Dependency Injection

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

The same `AttributedServiceFactory` entry is repeated for every handler, service, and middleware in the module.
Note that a factory is still involved, but it is one generic factory driven by metadata, not a per-class application of the pattern.
The attribute lists the container identifiers in constructor order, which is the order you must keep in sync by hand.

### The Same Service with the Factory Design Pattern

The factory below is illustrative: dotkernel/api does not ship it, but it is what the same wiring looks like when you apply the Factory pattern instead of the attribute.

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
Either way, `UserAvatarService` itself still receives its dependencies through the constructor; the pattern only changes where the assembly code lives.

### Doctrine Repositories

Repositories are where declarative injection saves the most repetition.
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

With the Factory pattern applied per class, each repository needs its own factory that fetches the entity manager and asks it for the right repository, the same boilerplate repeated per entity.

### Where the Factory Pattern Still Wins

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

Three things make this a job for the Factory pattern.
`ErrorResponseGenerator` belongs to Mezzio, so you cannot annotate it.
The `has()` check is conditional logic.
The constructor receives a computed scalar, `$config['debug'] ?? false`, not something the container already holds.
Encapsulating that kind of construction logic is exactly what the pattern exists for.

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

An attribute cannot express "give me the container," and this resolver exists to look up Doctrine entity listeners on demand.
That is a deliberate exception.
It does not make the container a general-purpose dependency for your services.

### Using Both in One Project

Dependency injection and the Factory pattern share the same `ConfigProvider`. dotkernel/api's `App` module, for example, wires its services with the attribute and still decorates selected services with delegators, which are factories you write:

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

A service can be built through attribute-driven injection and still be wrapped by a delegator.
Which approach applies to which service is decided entirely by the configuration.

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
The same problem appears if the service calls a static factory such as `UserAvatarRepositoryFactory::create()` from inside its own methods: the Factory pattern belongs in the wiring, not inside the class it serves.
Declare `UserAvatarRepository` in the constructor instead, using either approach from this article.

## Conclusion

DI decides how a class receives its dependencies; the Factory pattern decides where its assembly code lives.
Either way, your service gets the same constructor injection.

Default to `#[Inject]` when a class only needs what the container already holds.
Use the Factory pattern when construction needs logic or the class isn't yours to annotate.
The `ConfigProvider` decides per service, so you can mix both.

Learn more in the [dot-dependency-injection documentation](https://docs.dotkernel.org/dot-dependency-injection/v1/attributes-vs-factories/) and the [Dotkernel API](https://github.com/dotkernel/api) source.

## FAQ

**Q: Isn't a factory itself a form of dependency injection?**
A: Only when it hands dependencies to a constructor, as container factories do.
A class that calls a factory itself is pulling, not being injected.

**Q: Can I mix dependency injection attributes and the Factory pattern in the same project?**
A: Yes.
The `ConfigProvider` assigns a factory per service, and dotkernel/api mixes both.

**Q: Does attribute-driven injection slow the application down?**
A: Slightly: one `ReflectionClass` per service creation, usually once per request since services are shared.
No benchmarks are published, so measure if it matters.

**Q: Why does `#[Inject]` lose static type checking?**
A: Its identifiers are strings resolved at runtime, so static analysis can't match them to the constructor.
PHP still throws a `TypeError` when the service is built.

**Q: When must I use the Factory pattern?**
A: For computed or conditional construction, third-party classes, or dependencies that aren't constructor arguments.
Decorators need a delegator factory, but the wrapped service can still use `#[Inject]`.

**Q: How does this relate to PSR-11 and the service locator anti-pattern?**
A: Both approaches keep the container inside a factory, never inside your service, as PSR-11 recommends.

**Q: Does `#[Inject]` need Doctrine?**
A: The package requires Doctrine ORM, but `#[Inject]` works for any service.
Only `AttributedRepositoryFactory` and `#[Entity]` are Doctrine-specific.
