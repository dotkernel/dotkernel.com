# Dotkernel packages lifecycle page

The page lists Dotkernel's open-source packages with their support status.
It renders a pre-generated JSON file, so the request path never calls GitHub.

## Where this lives

- Published page: <https://www.dotkernel.com/dotkernel-packages-oss-lifecycle/>
- Data file: <https://www.dotkernel.com/dotkernel-packages.json>
- Source repository: <https://github.com/dotkernel/dotkernel.com>

Every path below is relative to the root of `dotkernel/dotkernel.com`.
If the current working directory is a different project, the change belongs in that repository, not here - clone it or switch to that checkout before editing.

## The pieces

| Concern             | Path in `dotkernel/dotkernel.com`                                         |
|---------------------|---------------------------------------------------------------------------|
| Route               | `src/App/src/RoutesDelegator.php`                                         |
| Handler (read-only) | `src/App/src/Handler/GetPackagesViewHandler.php`                          |
| Template            | `src/Blog/templates/page/dotkernel-packages-oss-lifecycle.html.twig`      |
| Card styles         | `src/App/assets/scss/components/_custom.scss` (`Package lifecycle cards`) |
| Generator           | `src/App/src/Service/PackageGenerator.php`                                |
| GitHub transport    | `src/App/src/Service/GitHubClient.php`                                    |
| Config              | `config/autoload/packages.global.php`                                     |
| CLI entry point     | `bin/generate-packages`                                                   |
| Data file           | `public/dotkernel-packages.json` (gitignored)                             |

Two rules follow from this split, and breaking either is the usual cause of a confusing result:

- The handler only reads.
A change to the generator shows up on the page only after the data file is rebuilt.
- The data file is gitignored, so it does not travel with a deploy.
Production depends on the cron run.

## How a repository gets onto the page

A repository is a published package when it contains an `OSSMETADATA` file with an `osslifecycle` value.

```text
osslifecycle=active
```

There is no allow-list to edit.
To keep a repository off the page, add its bare name (case-insensitive) to `ignoreRepos` in `config/autoload/packages.global.php`.

Recognized values, which are also the display order (`LIFECYCLE_ORDER`): `active`, `maintenance`, `security-only`, `archived`.
Anything else is kept, sorted last, and rendered with the muted `package-card--unknown` treatment.
For equal lifecycles, the sorting is by repository name.

Each entry in the data file is built from three sources:

- `name`, `url`, `description`, `archived` - the `/orgs/{org}/repos` listing, so they cost no extra request.
- `lifecycle` - the repository's `OSSMETADATA`.
- `php` - `require.php` in the repository's `composer.json`; if no value is found, it defaults to `null`.

`description` and `php` are `null` for anything unusable rather than an empty string, and the template omits the element instead of rendering a blank one.

## Regenerating

From the root of a `dotkernel/dotkernel.com` checkout:

```bash
php bin/generate-packages
```

Credentials are saved in `config/autoload/local.php` under the `github` key (`authBearer`, `userAgent`, `org`) - see `local.php.dist`.
If no token is set, unauthenticated requests still work but have a much lower rate limit.

The run is deliberately conservative: it writes to a `.tmp` sibling and renames, and it aborts with exit code 1 if more than 20% of per-repository requests fail (`FAILURE_THRESHOLD`), leaving the previous listing in place.
A non-zero exit means the page keeps serving yesterday's data, which is the intended outcome.

## Reading the output

- `Skipped by ignoreRepos: …` - informational, those repositories were excluded on purpose.
- `WARNING: ignoreRepos entries matched nothing (renamed or deleted?)` - stale `ignoreRepos` entries.
Prune them, or a renamed repository silently reappears on the site.
- `WARNING: <repo>: could not read OSSMETADATA (…)` - the request failed and counts toward the 20% threshold.
The repository is left off this run.
- `WARNING: <repo>: OSSMETADATA present but no osslifecycle value found, skipped` - the file exists but has no parsable `osslifecycle=`; fix it in that repository.
- `WARNING: <repo>: could not read composer.json (…)` - costs the PHP badge constraint, not the package.

## After changing anything in the page

- If the payload changed shape, rerun `php bin/generate-packages`, since the local data file predates the change.
- If the SCSS is updated, run `npm run build`.
`public/css/app.css` is tracked, so the compiled file must be committed with the source.
- Run the test battery using `vendor/bin/phpunit`, `vendor/bin/phpcs`, and `vendor/bin/phpstan analyse`.
Coverage lives in `test/Unit/App/Service/PackageGeneratorTest.php`, `test/Unit/App/Factory/PackageGeneratorFactoryTest.php`, and `test/Unit/App/Handler/GetPackagesViewHandlerTest.php`.
- If the data file is renamed, update `packages.global.php`, `.gitignore`, the "View as JSON" link in the template, and the fallback default in `src/App/src/Factory/PackageGeneratorFactory.php`.
