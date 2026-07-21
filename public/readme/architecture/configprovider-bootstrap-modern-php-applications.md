---
title: "ConfigProvider - Bootstrap Modern PHP Applications"
description: "An overview of the ConfigProvider pattern used in Laminas/Mezzio-based applications, including Dotkernel, to bootstrap middleware pipelines and dependency injection."
author: "Florin Bidirean"
date_published: "2025-08-20"
canonical_url: "https://new.dotkernel.com/architecture/configprovider-bootstrap-modern-php-applications/"
category: "Architecture"
language: "en"
---

# ConfigProvider - Bootstrap Modern PHP Applications

## TL;DR

In PHP, a `ConfigProvider` is a class or callable that is part of an application's bootstrap process, returning configuration data that tells the platform which middleware should run, in what order, and under what conditions. Frameworks like Mezzio, Laminas, Slim, and the Dotkernel Headless Platform use ConfigProviders to declare middleware pipeline configuration, dependency injection mappings, and request handlers, which get merged together automatically during bootstrap (except in Dotkernel, where new ConfigProviders must be registered manually).

## Where Is the ConfigProvider Used?

Mezzio (formerly Zend Expressive), Laminas, Slim, the Dotkernel Headless Platform, and other middleware-based frameworks often have a `ConfigProvider` class. In Laminas/Mezzio specifically, each module or package may contain a `ConfigProvider` that returns:

- Middleware pipeline configuration:
  - Middleware classes or service names.
  - Error-handling middleware, which should have the lowest priority.
  - Middleware groups or nested arrays.
- Dependency injection mappings.
- Request Handlers.

Example structure used in Dotkernel:

```php
class ConfigProvider
{
    public function __invoke(): array
    {
        return [ /* ... */ ];
    }

    public function getDependencies(): array
    {
        return [
            'factories'  => [ /* ... */ ],
            'invokables' => [ /* ... */ ],
        ];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [ /* ... */ ],
            'error' => [ /* ... */ ],
        ];
    }
}
```

What each item means:

| Item | Meaning |
|---|---|
| `dependencies` | Used by the dependency injector (e.g. laminas-servicemanager) to construct every requested service. |
| `factories` | The factory builds the service. |
| `invokables` | The service is built with `new` directly. |
| `aliases` | Redirects to another service name. |
| `delegators` | Wraps the original service. |
| `templates` | Defines the paths for the template files. |

## How the ConfigProvider Works

The ConfigProvider is automatically picked up by the framework during application bootstrap:

1. **Merge the global configuration** - All ConfigProviders are merged into one array.
2. **Read the configuration array** - A call similar to `$config = $container->get('config') ?? [];` reads an array of entries.
3. **Resolve item** - `$app->pipe()` is called to resolve one of the following: resolve the service name from the container, wrap the middleware if an array is provided, or call the closure or invokable object.
4. **Handle errors** - The error-handling middleware is the last one in the pipeline, to make sure it can handle any exceptions.
5. **Execute at runtime** - Laminas Stratigility iterates over the pipeline in the order it was registered. Each middleware can handle the request and return a response, or delegate execution to the next middleware in the pipeline, until a `ResponseInterface` is returned to the client.

## Benefits

- **Centralized setup** - Instead of hardcoding bootstrap code, it's declared in a config provider so it's easy to read, change, or extend.
- **Modular** - Each package can ship with its own config without interfering with others.
- **Container-friendly** - Works well with frameworks using DI containers like Laminas ServiceManager, PHP-DI, or Pimple.
- **Standardized service definitions** - Consistent rules for object creation, separate from business logic.
- **Auto-Discovery** - In Laminas/Mezzio, the ConfigAggregator automatically loads and merges all ConfigProviders. Dotkernel is an exception: new ConfigProviders have to be added manually in `config/config.php`, because all the initial ConfigProviders required to install the applications are already injected.
- **Environment-agnostic** - Returns an array that defines dev, test, or prod environments.
- **Testability** - The consistent, central configuration promotes isolated (e.g. per-module) testing, easier swapping of dependencies, and assertion of pipeline setup (e.g. checking if a config key is present).

## FAQ

**Q: What is a ConfigProvider in PHP?**
A: It is a class that is part of an application's bootstrap process: a class or callable that returns configuration data telling the platform which middleware should run, in what order, and sometimes under what conditions.

**Q: What does the ConfigProvider return in the Laminas/Mezzio ecosystem?**
A: In the Laminas/Mezzio ecosystem, it's literally an array of configuration, settings, or anything else the application needs, and each module or package may contain its own ConfigProvider returning middleware pipeline configuration, dependency injection mappings, and request handlers.

**Q: What is the difference between 'factories' and 'invokables' in the dependencies array?**
A: `factories` will have the factory build the service, while `invokables` will use `new` directly. You can also use `aliases` to redirect to another service name and `delegators` to wrap the original service.

**Q: How does the ConfigProvider get used during application bootstrap?**
A: It is automatically picked up by the framework during bootstrap: all ConfigProviders are merged into one array, the configuration array is read, each item is resolved via `$app->pipe()`, the error-handling middleware is placed last in the pipeline, and at runtime Laminas Stratigility iterates over the pipeline in the order it was registered.

**Q: Are new ConfigProviders auto-discovered in Dotkernel?**
A: Dotkernel is an exception to the usual auto-discovery rule: new ConfigProviders have to be added manually in `config/config.php`, because all the initial ConfigProviders required to install the applications are already injected.

**Q: What are the benefits of using a ConfigProvider?**
A: Benefits include centralized setup instead of hardcoded bootstrap code, modularity so each package can ship its own config, container-friendliness with DI containers like Laminas ServiceManager, PHP-DI or Pimple, standardized service definitions, environment-agnostic configuration for dev/test/prod, and better testability of the pipeline setup.

## Resources

- [Mezzio Container](https://docs.mezzio.dev/mezzio/v3/features/container/config/)
- [Laminas Config Aggregator](https://docs.laminas.dev/laminas-config-aggregator/config-providers/)
- [PSR-15 (HTTP Server Request Handlers)](https://www.php-fig.org/psr/psr-15/)
