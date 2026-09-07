---
title: "ConfigProvider - Bootstrap Modern PHP Applications"
description: "An overview of the ConfigProvider pattern used in Laminas/Mezzio-based applications, including Dotkernel, to bootstrap middleware pipelines and dependency injection."
author: "Florin Bidirean"
date_published: "2025-08-20"
canonical_url: "https://www.dotkernel.com/architecture/configprovider-bootstrap-modern-php-applications/"
category: "Architecture"
language: "en"
---

# ConfigProvider - Bootstrap Modern PHP Applications

## TL;DR
In PHP, a `ConfigProvider` is a class or callable that is part of an application's bootstrap process, returning configuration data that tells the platform which middleware should run, in what order, and under what conditions.
Frameworks like Mezzio, Laminas, Slim, and the Dotkernel Headless Platform use ConfigProviders to declare middleware pipeline configuration, dependency injection mappings, and request handlers, which get merged together automatically during bootstrap (except in Dotkernel, where new ConfigProviders must be registered manually).

In PHP, the `ConfigProvider` is a class that is part of an application's bootstrap process. **It's a class or callable that returns configuration data telling the platform which middleware should run, in what order, and sometimes under what conditions.**

If you're talking specifically about the ConfigProvider in the Laminas/Mezzio ecosystem, it's literally an array of configuration, settings, or anything else your application needs.

## Where Is the ConfigProvider Used?

Mezzio (formerly Zend Expressive), Laminas, Slim, the Dotkernel Headless Platform, or other middleware-based frameworks often have a `ConfigProvider` class. In Laminas/Mezzio specifically, each module or package may contain a `ConfigProvider` that returns:

- Middleware pipeline configuration.
  - Middleware classes or service names.
  - Error-handling middleware, which should have the lowest priority.
  - Middleware groups or nested arrays.
- Dependency injection mappings.
- Request Handlers.

Example in Dotkernel, which is an approach similar to Laminas/Mezzio:

```
class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    public function getDependencies(): array
    {
        return ,
           'invokables'   => ,
        ];
    }

    public function getTemplates(): array
    {
        return ,
        'error'  => ,
        ];
    }
}
```

What each item above means:

- `dependencies` is used by the dependency injector (like [laminas-servicemanager](https://docs.mezzio.dev/mezzio/v3/features/container/laminas-servicemanager/)) to construct every requested service.
  - `factories` will have the factory build the service.
  - `invokables` will use `new` directly.
  - You can also use `aliases` to redirect to another service name and `delegators` to wrap the original service.
- `templates` defines the paths for the template files.

## How the ConfigProvider works

The ConfigProvider is automatically picked up by the framework during application bootstrap. Let's look at it step by step:

- **Merge the global configuration** - All ConfigProviders are merged into one array.
- **Read the configuration array** - The call is similar to the below and expects an array of entries:

```
$config = $container->get('config') ?? [];
```

- **Resolve item** - `$app->pipe()` is called to resolve one of the below instances:
  - Resolve the service name from the container
  - Wrap the middleware, if an array is provided
  - Call the closure or invokable object.
- **Handle errors** - This middleware is the last one in the pipeline to make sure it handles any exceptions.
- **Execute at runtime** - [Laminas Stratigility](https://docs.laminas.dev/laminas-stratigility/) iterates over the pipeline in the order it was registered.
  - Each middleware can **handle** the request and return a response, or **delegate** execution to the next middleware in the pipeline, until a `ResponseInterface` is returned to the client.

Below you can see how Mezzio and Dotkernel merge and use ConfigProviders to build the middleware pipeline and dependencies.

![](/uploads/article/019f8a80-cc92-7277-92c8-c0e68d81615f/ConfigProvider2.png)

## Benefits

- Centralized setup – Instead of hardcoding bootstrap code, you declare it in a config provider so it's easy to read, change, or extend.
- Modular – Each package can ship with its own config without interfering with others.
- Container-friendly – It works well with frameworks using DI containers like Laminas ServiceManager, PHP-DI, or Pimple.
- Standardized service definitions - It has consistent rules for object creation that are separate from business logic.
- Auto-Discovery - In Laminas/Mezzio, the [ConfigAggregator](https://docs.laminas.dev/laminas-config-aggregator/) automatically loads and merges all ConfigProviders.

> Dotkernel is an exception to this rule: new ConfigProviders have to be added manually in `config/config.php`, because all the initial ConfigProviders required to install the applications are already injected.

- Environment-agnostic - It returns an array that defines dev, test, or prod environments.
- Testability - The consistent, central configuration promotes isolated (e.g. per-module) testing, easier swapping of dependencies and the assertion of pipeline setup (e.g. check if a config key is present).

## Additional Resources

- [Mezzio Container](https://docs.mezzio.dev/mezzio/v3/features/container/config/)
- [Laminas Config Aggregator](https://docs.laminas.dev/laminas-config-aggregator/config-providers/)
- [PSR-15 (HTTP Server Request Handlers)](https://www.php-fig.org/psr/psr-15/)

## FAQ

**Q: What is a ConfigProvider in PHP?**
A: It is a class that is part of an application's bootstrap process: a class or callable that returns configuration data telling the platform which middleware should run, in what order, and sometimes under what conditions.

**Q: What does the ConfigProvider return in the Laminas/Mezzio ecosystem?**
A: In the Laminas/Mezzio ecosystem, it's literally an array of configuration, settings, or anything else the application needs, and each module or package may contain its own ConfigProvider returning middleware pipeline configuration, dependency injection mappings, and request handlers.

**Q: What is the difference between 'factories' and 'invokables' in the dependencies array?**
A: factories will have the factory build the service, while invokables will use new directly. You can also use aliases to redirect to another service name and delegators to wrap the original service.

**Q: How does the ConfigProvider get used during application bootstrap?**
A: It is automatically picked up by the framework during bootstrap: all ConfigProviders are merged into one array, the configuration array is read (similar to $config = $container->get('config') ?? [];), each item is resolved via $app->pipe(), the error-handling middleware is placed last in the pipeline, and at runtime Laminas Stratigility iterates over the pipeline in the order it was registered.

**Q: Are new ConfigProviders auto-discovered in Dotkernel?**
A: Dotkernel is an exception to the usual auto-discovery rule: new ConfigProviders have to be added manually in config/config.php, because all the initial ConfigProviders required to install the applications are already injected.

**Q: What are the benefits of using a ConfigProvider?**
A: Benefits include centralized setup instead of hardcoded bootstrap code, modularity so each package can ship its own config, container-friendliness with DI containers like Laminas ServiceManager, PHP-DI or Pimple, standardized service definitions, environment-agnostic configuration for dev/test/prod, and better testability of the pipeline setup.
