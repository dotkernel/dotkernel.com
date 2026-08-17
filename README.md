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

`opengraph_img` is the image shown as the social-media (Twitter/OG) preview card. Leave it `null` to fall back to the site-wide default image (`config/autoload/local.php` → `application.meta.image`). To set one, put the image file at `public/opengraph/article/your-image.png` and reference it here as a root-relative path: `"opengraph_img": "/opengraph/article/your-image.png"`. This is unrelated to the in-article images described in step 3 - it is placed by hand, not by `bin/create-uploads-dir`.

**Important:** you can set `"post_status": "draft"` instead of `"publish"` to keep an article out of sight - anything other than `publish`/`private` is treated as a draft by `PostLoader`, and `getPublishedPosts()` (used by both `bin/generate-feed` and `bin/sitemap`) only returns posts with `publish` status. After changing it, follow the same steps: re-run `bin/doctrine-fixtures`, then `bin/generate-feed` and `bin/sitemap`. This applies generally, not just to status changes - **any** edit to `articles_cleaned.json` (title, excerpt, status, date, etc.) needs `bin/doctrine-fixtures` re-run to update the database, followed by re-running the 3 generators in step 4 so `feed.xml`/`sitemap.xml`/`llms-full.txt` reflect it. One exception: `bin/generate-llms-full` reads straight from the `.md` files on disk and does **not** check `post_status` at all - a `draft` article's `.md` file will still be included in `llms-full.txt` unless you also remove or rename that file.

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
php bin/generate-llms-full
```

- `bin/generate-feed` rewrites `public/feed.xml` from the published posts in the database.
- `bin/sitemap` rewrites `public/sitemap.xml` from the published posts in the database.
- `bin/generate-llms-full` rewrites `public/llms-full.txt` by concatenating `public/md-articles/index.md` and every other `public/md-articles/*/*.md` file, sorted by path, then appending each `public/md-pages/*.md` file — the markdown versions of the static pages — labelled with a `md-pages/` prefix in the section header. Requires the `llms.sourceDir` / `llms.outputFile` keys in `config/autoload/local.php` (see `local.php.dist`); the `llms.pagesDir` key is optional, and omitting it leaves the page sections out.

These three have no ordering dependency on each other, only on step 3 being done first.

**`public/llms.txt` is not part of this - it is edited by hand, not generated.** It's a separate, curated index (one line per article, grouped by category) distinct from the full-text `llms-full.txt`. Whenever an article is added, add a matching entry under its category:

```markdown
- [Your article title](https://www.dotkernel.com/{category-slug}/{article-slug}/): One-sentence description, similar to the excerpt.
```

Also bump that category's post count in its heading (e.g. `## Dotkernel (65 posts)`). Entries are ordered alphabetically by title within each category.

None of this is wired into an automated deploy pipeline in this repository - there is no `deploy` script or CI job that runs these `bin/` scripts. `public/feed.xml`, `public/sitemap.xml`, and `public/llms-full.txt` are committed generated artifacts, so re-running these scripts leaves them modified in git until committed.

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
