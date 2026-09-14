---
title: "How to Use Twig Markdown to Generate HTML Pages at Runtime"
description: "How to wire twig/markdown-extra with league/commonmark so a Twig application converts Markdown files to HTML on every request, instead of pre-compiling pages at build time."
author: "stefan"
date_published: "2026-09-09"
canonical_url: "https://www.dotkernel.com/how-to/how-to-use-twig-markdown-to-generate-html-pages-at-runtime/"
category: "How to's"
language: "en"
---

# How to Use Twig Markdown to Generate HTML Pages at Runtime

## TL;DR

`twig/markdown-extra` adds a `markdown_to_html` filter to Twig but ships no Markdown parser itself — you provide one through a `RuntimeLoaderInterface` factory.
Wire `MarkdownExtension` and a `MarkdownRuntime` (backed here by `league/commonmark`'s `GithubFlavoredMarkdownConverter`) into your container, register the extension with Twig, then call `{{ someMarkdownString|markdown_to_html }}` in any template.
Because the conversion runs inside the request/response cycle rather than at build time, the source `.md` file can be read from disk, edited, or swapped right up until the moment a request asks for it — no build step, no cache to invalidate by hand.

## Why Runtime Instead of Build Time

A static-site generator turns Markdown into HTML once, ahead of time, and serves the resulting files.
That works well when content changes rarely and a build pipeline is already part of the deploy.
Rendering at runtime is a better fit when:

- Content lives in the same repository as the code and should go live the moment the file is merged, with no separate "build the site" step.
- The same Markdown source needs to serve more than one output — an HTML page, a raw `text/markdown` response for a bot or an LLM, an RSS/Atom feed excerpt — without maintaining a pre-rendered copy for each.
- A handler needs to inspect or transform the Markdown first (strip a section, extract structured data from it) before deciding what HTML to produce.

The tradeoff is that every request pays the parsing cost, so pair it with an opcode cache and, if traffic warrants it, an HTTP or application cache layer — the parsing itself is not the expensive part until a page is read thousands of times a minute.

## Step 1: Install the Packages

Two packages are required: the Twig extension that exposes the filter, and a Markdown engine to actually do the conversion.

```bash
composer require twig/markdown-extra league/commonmark
```

`twig/markdown-extra` is deliberately engine-agnostic — it defines the `markdown_to_html` filter and a `MarkdownInterface` contract, but leaves the choice of parser (CommonMark, Parsedown, michelf/php-markdown, or a custom one) to the application.

## Step 2: Register the Extension and a Runtime Loader

Twig extensions that need constructor arguments, or that wrap a heavier dependency, are usually built through the DI container rather than instantiated inline. Two pieces go into the container: the extension itself, and a `RuntimeLoaderInterface` that lazily builds the `MarkdownRuntime` the extension calls at render time.

```php
// MarkdownExtensionFactory.php
use Psr\Container\ContainerInterface;
use Twig\Extra\Markdown\MarkdownExtension;

class MarkdownExtensionFactory
{
    public function __invoke(ContainerInterface $container): MarkdownExtension
    {
        return new MarkdownExtension();
    }
}
```

```php
// MarkdownRuntimeLoaderFactory.php
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Psr\Container\ContainerInterface;
use Twig\Extra\Markdown\LeagueMarkdown;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

class MarkdownRuntimeLoaderFactory
{
    public function __invoke(ContainerInterface $container): RuntimeLoaderInterface
    {
        return new FactoryRuntimeLoader([
            MarkdownRuntime::class => static fn (): MarkdownRuntime => new MarkdownRuntime(
                new LeagueMarkdown(new GithubFlavoredMarkdownConverter())
            ),
        ]);
    }
}
```

`LeagueMarkdown` is the small adapter `twig/markdown-extra` ships for `league/commonmark`; swapping engines later only means writing a different adapter (or using one of the built-in ones) inside this one factory — nothing in the templates has to change.

Then register both in the Twig configuration, so the extension is loaded and the runtime loader is available to resolve it:

```php
'twig' => [
    'extensions'      => [
        MarkdownExtension::class,
    ],
    'runtime_loaders' => [
        RuntimeLoaderInterface::class,
    ],
],
```

And map both classes to their factories in the container configuration:

```php
'factories' => [
    MarkdownExtension::class      => MarkdownExtensionFactory::class,
    RuntimeLoaderInterface::class => MarkdownRuntimeLoaderFactory::class,
],
```

## Step 3: Convert Markdown to HTML in a Template

With both pieces registered, any string of Markdown passed into a template can be converted with the filter:

```twig
{{ content|markdown_to_html }}
```

`content` here is just a plain PHP string — commonly the body of a `.md` file read with `file_get_contents()` and handed to the template as a render variable. The filter runs CommonMark against it and outputs the resulting HTML, unescaped, directly into the page.

## Step 4: Resolve the Source File Safely

If the Markdown file path is built from anything in the request — a slug, a category, a page name in the URL — resolve it with `realpath()` and verify the result still lives inside the intended base directory before reading it. Otherwise a crafted slug like `../../../../etc/passwd` could walk outside the content directory:

```php
public function resolveMarkdownFilePath(string $categorySlug, string $slug): ?string
{
    $base = realpath($this->articlesPath);
    if ($base === false) {
        return null;
    }

    $realPath = realpath(rtrim($base, '/') . '/' . $categorySlug . '/' . $slug . '.md');
    if ($realPath === false || ! is_file($realPath)) {
        return null;
    }

    if (! str_starts_with($realPath, rtrim($base, '/') . '/')) {
        return null;
    }

    return $realPath;
}
```

`realpath()` returns `false` for a path that doesn't exist and also collapses any `..` segments, so the prefix check afterwards is what actually rejects a resolved path that escaped the articles directory — checking the raw, unresolved string wouldn't be enough on its own.

## Putting It Together in a Handler

A typical request handler ties the four steps into one flow: resolve the file, read it, optionally pull out metadata (front matter, an FAQ section, anything else the page needs), and hand the remaining body to the template that applies the filter.

```php
$markdownFile = $this->blogService->resolveMarkdownFilePath($categorySlug, $slug);
if ($markdownFile === null) {
    return $this->notFound();
}

$body = file_get_contents($markdownFile);

return new HtmlResponse(
    $this->template->render('page::article', ['content' => $body])
);
```

Nothing here writes an HTML file to disk. The `.md` file is the only artifact that exists at rest; the HTML is produced fresh on every request and thrown away as soon as the response is sent.

## Resources

- [twig/markdown-extra documentation](https://twig.symfony.com/doc/3.x/filters/markdown_to_html.html) — the official filter reference
- [league/commonmark documentation](https://commonmark.thephpleague.com/) — the CommonMark/GFM converter used in the examples above
- [CommonMark specification](https://spec.commonmark.org/) — the Markdown dialect both packages implement

## FAQ

**Q: Does twig/markdown-extra include its own Markdown parser?**
A: No. It only defines the `markdown_to_html` filter and a `MarkdownInterface` contract; you must register a `RuntimeLoaderInterface` factory that builds a `MarkdownRuntime` backed by an actual engine, such as league/commonmark, Parsedown, or michelf/php-markdown.

**Q: Why render Markdown at runtime instead of pre-building HTML files?**
A: Runtime rendering lets content go live the moment a file is merged, with no separate build step, and lets the same source serve more than one output (HTML, raw text/markdown, feed excerpts) from a single file.

**Q: Is converting Markdown to HTML on every request expensive?**
A: Parsing itself is fast; the practical cost only shows up at high traffic. An opcode cache plus, if needed, an HTTP or application-level cache in front of the rendered response is enough to handle most load without pre-building anything.

**Q: How do you prevent a URL from reading a file outside the content directory?**
A: Resolve the constructed path with `realpath()`, which returns `false` for paths that don't exist and collapses any `..` segments, then verify the resolved path still starts with the base content directory before reading the file.

**Q: Can the Markdown engine be swapped later without touching templates?**
A: Yes. The engine is only referenced inside the `RuntimeLoaderInterface` factory; templates only ever call the `markdown_to_html` filter, so replacing `LeagueMarkdown` with a different adapter is a one-file change.
