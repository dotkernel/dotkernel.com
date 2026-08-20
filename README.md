# Adding an article

Steps to publish a new article and regenerate the public artifacts that depend on it.

## 1. Add the article data

Add a new entry to the category's `articles` array in `src/App/src/Fixture/articles_cleaned.json`:

```json
{
    "post_title": "Your article title",
    "post_date": "YYYY-MM-DD HH:MM:SS",
    "post_status": "publish",
    "author": {
        "display_name": "admin",
        "github": "arhimede"
    },
    "isObsolete": false,
    "opengraph_img": null,
    "excerpt": "Short excerpt shown in listings.",
    "tl_dr": "One or two sentence summary.",
    "tags": [
        {
            "name": "your tag",
            "slug": "your-tag"
        }
    ]
}
```

`author.display_name` can either match an existing author or be a new name - `bin/doctrine-fixtures` creates a new `Author` automatically for any name not already in the database. The category (top-level `slug`) must already exist, though. The article's slug is derived automatically from the title (lowercased, non-alphanumeric characters collapsed to `-`) by `PostLoader::slugify()`.

`tags` can be left empty, but adding tags that describe the article's subject is useful - they show up on the article page and back its tag-resource pages.

**`post_status` values.** `PostLoader` (`src/App/src/Fixture/PostLoader.php`) only recognizes 3 JSON strings — anything else (including the literal `"draft"`) falls through to `Draft`:

| JSON value | Maps to | Behavior |
| --- | --- | --- |
| `"publish"` | `PostStatusEnum::Published` | The only status shown in listings, the RSS feed, and the sitemap (`getPublishedPosts()` and every category/tag/author query filter on `Published` only). |
| `"private"` | `PostStatusEnum::Private` | Not published: excluded from listings/feed/sitemap same as a draft, and its own page returns `404` — there is currently no route or view that treats `Private` differently from `Draft`. |
| `"archived"` | `PostStatusEnum::Archived` | Not published: excluded from listings/feed/sitemap, but its own page returns `410 Gone` instead of `404` — use this for content that existed and was intentionally removed (outdated articles, leftover test content, etc.), as opposed to content that was never public. |
| anything else (including `"draft"`) | `PostStatusEnum::Draft` | Not published: excluded from listings/feed/sitemap, its own page returns `404`. This is also the fallback for typos in `post_status`. |

After changing `post_status`, follow the same steps: re-run `bin/doctrine-fixtures`, then `bin/generate-feed` and `bin/sitemap`. This applies generally, not just to status changes — **any** edit to `articles_cleaned.json` (title, excerpt, status, date, etc.) needs `bin/doctrine-fixtures` re-run to update the database, followed by re-running the 4 generators in step 4 so `feed.xml`/`sitemap.xml`/`llms.txt`/`llms-full.txt` reflect it. One exception: `bin/generate-llms-full` reads straight from the `.md` files on disk and does **not** check `post_status` at all — a non-published article's `.md` file will still be included in `llms-full.txt` unless you also remove or rename that file.

## 2. Create the templates

- `src/Blog/templates/page/blog-resource/{category-slug}/{article-slug}.html.twig` - the page body, extending `@layout/blog-post.html.twig`.
- `src/Blog/templates/page/JSON-LD/{category-slug}/{article-slug}.jsonld.twig` - the `@graph` of `TechArticle` + `BreadcrumbList` + `FAQPage` structured data.
- `public/md-articles/{category-slug}/{article-slug}.md` - the markdown version, with YAML front matter (`title`, `description`, `author`, `date_published`, `canonical_url`, `category`, `language`) followed by the article body (`TL;DR`, sections, `FAQ`). This feeds `llms-full.txt`.

Copy an existing set of these three files in the same category as a starting point, to match the established structure (FAQ block matching the `FAQPage` entries, etc.).

If the article body uses images (via `asset('uploads/article/' ~ article.id ~ '/filename.png')` in the `.html.twig`), just drop the image file anywhere under `public/uploads` - `bin/create-uploads-dir` (step 4) finds it by filename and copies it to the right place. No manual path/folder creation needed.

## 3. At deploy - run in this order

```shell
php bin/doctrine-fixtures
php bin/create-uploads-dir
```

- `bin/doctrine-fixtures` loads `articles_cleaned.json` into the database, creating the `Post` entity (with its database-generated UUID) for the new article.
- `bin/create-uploads-dir` must run *after* it - it resolves the post by slug to get that UUID, creates `public/uploads/article/{post-id}/`, and copies each image referenced in the `.html.twig` there from wherever it already lives under `public/uploads`.

## 4. Regenerate the public artifacts - any order

```shell
php bin/generate-feed
php bin/sitemap
php bin/generate-llms
php bin/generate-llms-full
```

- `bin/generate-feed` rewrites `public/feed.xml` from the published posts in the database.
- `bin/sitemap` rewrites `public/sitemap.xml` from the published posts in the database.
- `bin/generate-llms` rewrites `public/llms.txt`, the short curated index (one line per article, grouped by category) — built from the published posts in the database, grouped by category (known categories first, in a fixed order, then any remaining categories by post count) and sorted alphabetically by title within each category. Requires the `llms.sourceDir` / `llms.indexFile` keys in `config/autoload/local.php` (see `local.php.dist`); the `llms.pagesDir` key is optional, same as for `bin/generate-llms-full` below.
- `bin/generate-llms-full` rewrites `public/llms-full.txt` by concatenating `public/md-articles/index.md` and every other `public/md-articles/*/*.md` file, sorted by path, then appending each `public/md-pages/*.md` file — the markdown versions of the static pages — labelled with a `md-pages/` prefix in the section header. Requires the `llms.sourceDir` / `llms.outputFile` keys in `config/autoload/local.php` (see `local.php.dist`); the `llms.pagesDir` key is optional, and omitting it leaves the page sections out.

These four have no ordering dependency on each other, only on step 3 being done first.

None of this is wired into an automated deploy pipeline in this repository - there is no `deploy` script or CI job that runs these `bin/` scripts. **This must be run manually as part of every deploy** whenever `articles_cleaned.json` changed since the last deploy. `public/feed.xml`, `public/sitemap.xml`, `public/llms.txt`, and `public/llms-full.txt` are committed generated artifacts, so re-running these scripts leaves them modified in git until committed.

## How to update an article

Steps to edit an existing article (change its status, text, or both) and get the change live.

1. **Edit the article's data.** Find its entry under the category's `articles` array in `src/App/src/Fixture/articles_cleaned.json` and change whatever needs updating: `post_title`, `excerpt`, `tl_dr`, `post_status`, `isObsolete`, etc. `PostLoader` matches the existing article by slug (derived from `post_title`), so as long as you don't change the title, it updates the same `Post` row instead of creating a new one.
   - See the `post_status` values table in step 1 above for what each status does — e.g. `"archived"` is the right choice for content that existed and was intentionally removed (outdated content, a leftover test article, etc.), as it serves `410 Gone` instead of `404`.
2. **Edit the content, if the body itself changed.** Update the matching files for that article's category/slug:
   - `public/md-articles/{category-slug}/{article-slug}.md`
   - `src/Blog/templates/page/blog-resource/{category-slug}/{article-slug}.html.twig`
   - `src/Blog/templates/page/JSON-LD/{category-slug}/{article-slug}.jsonld.twig` (only if it has hardcoded text outside of `article.*`/`meta.*` variables — most of its fields pull straight from the database and update automatically)
3. **Re-run the same commands as step 3 and step 4 above** (`bin/doctrine-fixtures`, then `bin/generate-feed` / `bin/sitemap` / `bin/generate-llms-full`) so the database and the generated artifacts reflect the change. `bin/create-uploads-dir` only needs to run again if you added a new image.

## How to move an article to a different category

An article's category isn't a field on the article itself — it's whichever top-level category object its entry sits under in `articles_cleaned.json`. Moving it is a structural move, not a value change, and the article's page/JSON-LD templates are resolved dynamically off the *current* category at render time (`GetPostResourceHandler` renders `page::blog-resource/{article.category.slug}/{article.slug}`, and the layout includes `@jsonld/{article.category.slug}/{article.slug}.jsonld.twig`) — there's no fallback if a file is missing at that path, so skipping any of the steps below leaves the article **404**ing at both the old and the new URL.

1. **Cut the article's JSON object from its current category's `articles` array and paste it into the target category's `articles` array**, in `src/App/src/Fixture/articles_cleaned.json`. The target category must already exist as a top-level entry. Nothing else in the object needs to change — `PostLoader` matches the existing `Post` by slug and updates its category on the next run.
2. **Physically move the three per-article files** from the old `{category-slug}` folder to the new one, keeping the same filename:
   - `public/md-articles/{old-category-slug}/{article-slug}.md` → `public/md-articles/{new-category-slug}/{article-slug}.md`
   - `src/Blog/templates/page/blog-resource/{old-category-slug}/{article-slug}.html.twig` → `.../{new-category-slug}/{article-slug}.html.twig`
   - `src/Blog/templates/page/JSON-LD/{old-category-slug}/{article-slug}.jsonld.twig` → `.../{new-category-slug}/{article-slug}.jsonld.twig`
3. **Check the moved `.html.twig` for a hardcoded `json_ld` block override.** Most articles leave that block untouched, so it resolves dynamically via the layout — but a few hardcode a literal path (e.g. `{% block json_ld %}{{ include('@jsonld/some-category/some-slug.jsonld.twig') }}{% endblock %}`). If yours does, update that literal path to the new category too.
4. **Re-run `bin/doctrine-fixtures`**, then the 3 generators (`bin/generate-feed`, `bin/sitemap`, `bin/generate-llms-full`), same as any other update.

Note: this changes the article's URL (`/{categorySlug}/{slug}/`), so the old URL will start 404ing — there is no redirect set up for a category move in this app.

## 5. Scheduled jobs (cron)

- **`bin/generate-packages`** - the only script here actually wired into a cron job. It rebuilds the Dotkernel packages listing from the GitHub organisation, which changes independently of this repo, so it runs on a schedule instead of at deploy time:
    ```text
    0 4 * * * cd /path/to/dotkernel.com && /usr/bin/php bin/generate-packages >> log/generate-packages.log 2>&1
    ```
    - Runs daily at **04:00**.
    - Exits non-zero without touching the data file if the run can't be trusted, so the previously generated listing keeps serving.
    - Logs to `log/generate-packages.log`.
- **`bin/generate-feed`** (RSS, `public/feed.xml`) — **manual only**, no cron. Run it as part of the publish flow in step 4, right after `bin/doctrine-fixtures`/`bin/create-uploads-dir`, whenever an article is added, edited, or its `post_status` changes.
- **`bin/sitemap`** (`public/sitemap.xml`) — **manual only**, no cron. Same trigger as `bin/generate-feed`: re-run after any change to `articles_cleaned.json`.
- **`bin/generate-llms-full`** (`public/llms-full.txt`) — **manual only**, no cron. Re-run after adding/editing a `.md` file under `public/md-articles/` or `public/md-pages/`, or after removing/renaming a draft's `.md` file (it doesn't check `post_status`, see step 1). The `public/md-pages/*.md` files are hand-maintained alongside the templates in `src/Page/templates/page/` — editing a page template means updating its `.md` and re-running this.
