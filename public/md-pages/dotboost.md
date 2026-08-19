---
title: "dotboost | Claude Code configuration for Dotkernel projects"
description: "dotboost is a drop-in .claude/ directory that teaches Claude Code the conventions of any Dotkernel application - ten commands, seventeen skills, six hooks, and permission guardrails that keep secrets out of the context."
canonical_url: "https://www.dotkernel.com/dotboost/"
language: "en"
---

# Dotboost

Developer tooling . AI context

Drop-in configuration that teaches Claude Code the conventions of any Dotkernel application - API, Admin, Frontend, Light, Queue, or a project derived from one of them.
Every skill detects the variant first and applies the matching dialect, so generated code follows your architecture instead of guessing at it.

- [View on GitHub](https://github.com/dotkernel/dotboost)
- [Claude Code docs](https://docs.claude.com/en/docs/claude-code/overview)

| | |
| --- | --- |
| Payload | One .claude/ directory |
| Commands | Ten /dk-* |
| Skills | Seventeen |

## What happens in a session

Session start (variant briefing) -> Skill loads (by description) -> Plan mode (proposes first) -> Guard hooks (Edit . Bash) -> Normalise (BOM . CRLF . EOF) -> Lint (`php -l` . markdown).

## Not an application - a configuration payload

Dotboost is not itself a Dotkernel application.
Its entire payload is the `.claude/` directory: settings, hooks, a status line, a review subagent, ten `/dk-*` commands and seventeen skills.
You install it by copying that directory into the Dotkernel project you are working on.

There is no routing table telling Claude which skill to pick - each one loads from its own description.
Ask where a new Doctrine entity goes and it should reach for the module-structure skill rather than answering from general framework knowledge.

Maintained by **Borsan Sergiu**.

- Detects the variant before it answers
- Secrets never enter the context window
- Guards catch compound commands globs miss
- Copy it in - nothing to build or install

## The reason to adopt the file

The permission rules in `settings.json` fall into four tiers.
The first one is the headline: these are `Read` denies, not write protection, so the contents never enter the context window at all.

### Your secrets - Tier . Never read

Not redacted - never loaded.

`.env`, every `*.local.php`, `config/autoload/local.php`, `local.test.php` and `data/oauth/`.
Database credentials, OAuth signing keys and environment secrets stay out of the transcript.

### Generated & vendored - Tier . Never written

The files a diff should never touch by accident.

Dependency manifests - `composer.json`, `composer.lock`, `package.json`, `package-lock.json` - plus `vendor/`, `node_modules/`, `data/`, `log/`, `public/uploads/` and anything under `Migration/`.

### Installs & destructive git - Tier . Never run

Commands that change your tree without a diff.

`composer require`, `remove`, `update`, `install`, `global`; the npm, yarn and pnpm install verbs; `git push`, `reset --hard`, `clean`, `submodule`; and `rm -rf`.

### Your call - Tier . Ask & allow

Preference, not policy - override per machine.

**Ask**: migrations, `bin/doctrine`, database clients, git commits and merges, pipeline and authorization config, the QA config files.
**Allow**: the Composer QA scripts, `vendor/bin` tools, `php -l`, and read-only git.
Layer changes in `settings.local.json`, never the shared file.

### Where globs cannot see - Hooks . Six

Path patterns miss what a shell can hide.

`guard-bash.sh` inspects the command itself, so `cd src && composer require foo` is caught where a path-based rule would let it through.
`guard-protected-paths.sh` covers edits, and redirects a blocked `*.local.php` edit to its `.dist` template.

### Linters that do not rewrite - Hooks . Report only

Deliberately no `phpcbf`, no `--fix`.

`php-lint.sh` and `markdown-lint.sh` report and stop, because reformatting a file right after it is written invalidates the in-memory copy and breaks the next targeted edit.
Bulk formatting belongs at the end of a task.
Both skip silently when their tool is missing.

`settings.json` is a committed file carrying opinions, not only guardrails: sessions start in **plan mode**, the terminal UI is full screen, the theme is dark, and Composer runs without a memory ceiling.
Know what you are adopting.

## Where the hooks overrule the tiers

Some commands marked *ask* in `settings.json` are refused outright by `guard-bash.sh`, because the hook sees compound commands the permission globs cannot.
The prompt you would expect never appears - you get a refusal.
This is deliberate, and worth knowing before it surprises you.

| Command | settings.json says | guard-bash.sh does |
| --- | --- | --- |
| git rebase | ask | blocks |
| doctrine-migrations migrate / execute | ask | blocks |
| fixtures:execute, schema:drop, schema:update | ask, via bin/cli.php | blocks |
| composer development-enable / -disable | - | blocks |
| pip install, git checkout --, git filter-branch | - | blocks |

Everything else under *ask* - commits, database clients, the config and QA files - prompts as documented.
To get a blocked command back you edit the hook; relaxing `settings.local.json` will not reach it.

## Ten `/dk-*` commands

Each one runs only when you ask for it.

| Command | What it does |
| --- | --- |
| /dk-bootstrap | Fresh clone to a running install. |
| /dk-module | Plan a new module - dot-maker first. |
| /dk-route | Add a fully wired endpoint or page. |
| /dk-trace | Trace a request through pipeline, handler and response. |
| /dk-test | Write and run tests. |
| /dk-document | Write or update a feature doc. |
| /dk-check | Run and fix the QA gate. |
| /dk-deprecate | Make an evolution-pattern breaking change. |
| /dk-review | Pre-PR convention review - read-only by design. |
| /dk-hygiene | Encoding and line-ending audit. |

## Seventeen skills, loaded on demand

Sixteen `dotkernel-*` skills plus `dependency-policy`.
Each is a living document - when a review turns up the same mistake twice, that is a missing line in a skill.

| Skill | Covers |
| --- | --- |
| application-variants | Detecting API vs Admin vs Frontend vs Light vs Queue. |
| module-structure | Where code goes: application module vs Core, and the wiring. |
| handler-naming | Both naming dialects, routes, authorization keys. |
| doctrine-entities | Entities, enums and DBAL types, repositories, migrations. |
| input-validation | InputFilters, Inputs, forms, CSRF, query whitelisting. |
| responses | HAL and collections, or templates and redirects; errors. |
| openapi | swagger-php attributes, for apps that publish OpenAPI. |
| feature-docs | Feature docs: template, where they live, staleness. |
| testing | Unit and functional patterns, test config, coverage matrix. |
| evolution-pattern | Sunset headers instead of versioning. |
| security | Auth, authorization, secrets, CORS, dependencies. |
| dot-maker | `composer make …` and the manual steps after it. |
| core-submodule | Core layering rules and git submodule mechanics. |
| psr-standards | PSR-1/3/4/6/7/11/12/15/16/17 as applied here. |
| qa-gate | cs-check, static analysis, tests, and forbidden "fixes". |
| troubleshooting | A symptom to cause table. |
| dependency-policy | The ladder from installed packages to hand-rolled code, and the proposal format. |

A review subagent, `dotkernel-reviewer`, runs the convention review in its own context so the main one stays clean.

## Never name a package from memory

The ladder is: already in `composer.lock`, then `dotkernel/*`, then `laminas/*` and `mezzio/*`, then a vetted community package, and only then hand-rolled code.
Stop at the first hit.

A skill's description decides whether it *can* load, not whether Claude stops to think before naming a package.
The rule that makes it reach for the skill has to be always loaded - which means it lives in your project's own `CLAUDE.md`, not in the skill.

- [The block to paste](https://github.com/dotkernel/dotboost#add-this-to-your-projects-claudemd)

### Three things about how that behaves

- The `dotkernel/*` manifest is generated on first use, not shipped - the first package question runs the sync script itself. It needs `curl`, `jq` and network; without them Claude is told to call a package name **unverified** rather than assert it.
- Neither that script nor `composer show --available` is in the allow or deny list, so both prompt. That is deliberate - expect a prompt the first time.
- "How do I send mail from here?" is a package question. Implicit ones count.

## Copy one directory into your project

The payload has to sit at the root of your project.
Two routes to the same files - pick whichever suits the machine you are on.

### 1 . Clone it inside the project

Shallow, because only the current state of `main` is any use in a target project.
The clone goes away right after the copy.

```shell
git clone --depth 1 \
  https://github.com/dotkernel/dotboost.git .dotboost
cp -r .dotboost/.claude .claude
rm -rf .dotboost
```

### 1b . Or download the zip

No git needed, which is the point.
The archive unpacks to `dotboost-main/` and does carry the dotfiles.

```shell
curl -L -o dotboost.zip https://github.com/\
dotkernel/dotboost/archive/refs/heads/main.zip
unzip -q dotboost.zip
cp -r dotboost-main/.claude .claude
```

### 2 . Restore the executable bits

Nothing is committed executable, and a zip extracted on Windows carries no permission bits at all.
The sync script is the one called by its own name.

```shell
chmod +x .claude/hooks/*.sh .claude/statusline.sh \
  .claude/skills/dependency-policy/scripts/*.sh
```

### 3 . Keep local overrides out of git

Then copy the example file if you want personal settings that survive an update.

```shell
echo '.claude/settings.local.json' \
  >> .git/info/exclude
```

### 4 . Optional: markdown linting

Not bundled - install it yourself, globally or as a project devDependency.
Until the binary resolves, the hook exits silently.

```shell
npm install -g markdownlint-cli2
```

### 5 . Paste the CLAUDE.md block

The dependency policy and, if you want feature docs actually read, the feature-docs block alongside it.

If the project already has its own `.claude/settings.json` or `.claude/commands/`, merge by hand rather than running `cp -r` blind - same-named files are overwritten.
To update later, run the whole install again over the top; anything you changed in the project's copy is lost, which is exactly why personal changes belong in `settings.local.json`.

## The parts you notice on day two

### Feature documentation - Docs . /dk-document

The part a cleared session cannot reconstruct from `src/`.

One markdown file per feature - what it does, why, the routes and the roles that reach them, the data added, how to exercise it.
Where it lands is detected, not assumed: `documentation/features/` when that directory exists, `docs/features/` otherwise, never a second documentation root beside an existing one.
The frontmatter is load-bearing - `/dk-review` greps `routes:` and `handlers:` to decide whether a new route in the diff is documented.

### Real usage, not guesses - Status line

The same numbers `/usage` reports.

5h and 7d account usage in the status bar, read from the rate-limit payload Claude Code 2.1+ passes on stdin.
Pure bash and awk - no `jq`, no python, no transcript scanning or guessed token budgets, so it works in Git Bash on Windows.

### A briefing, every time - Session start

What it detected, before you ask anything.

Which variant, the root namespace, the authorization style, the branch, which config files are still missing - and a CRLF warning when it finds one.
The same hook prints the feature-docs directory and a doc count, so the files are discoverable even without the CLAUDE.md block.

### CRLF, handled - Windows . Line endings

Dotkernel repos ship `* text eol=lf`.

Belt and braces, configure the client so nothing converts: `core.autocrlf false`, `core.eol lf`, `core.safecrlf warn`.
Already committed CRLF? Renormalise once with `git add --renormalize .`.
`normalize-file.sh` keeps new writes clean.

## Every variant, one payload

The application-variants skill detects which Dotkernel application it is looking at - including a project derived from one - and every other skill applies the matching dialect.
Handler naming, response shape and authorization keys all differ between them, which is the whole reason the detection runs first.

### Detected variants

`API` `Admin` `Frontend` `Light` `Queue`

Before adapting the skills to a new Dotkernel application, the advice from its maintainer is to spend an hour reading that repo and correcting them against what is actually there.
A skill written from framework docs rather than the codebase produces confident wrong answers, which is worse than no skill.

### API - Variant . Detected

Handlers, HAL collections, OpenAPI attributes.

A REST API on a PSR-15 middleware pipeline, with OAuth 2.0, RBAC, HAL payloads and an OpenAPI 3.0 specification wired up on install.

- [Read more](https://www.dotkernel.com/api/)
- [GitHub](https://github.com/dotkernel/api)

### Admin - Variant . Detected

Forms, CSRF, route-name authorization keys.

Table-based record management with RBAC guards, CSRF-protected forms and 2FA, over the shared Core module.

- [Read more](https://www.dotkernel.com/admin/)
- [GitHub](https://github.com/dotkernel/admin)

### Frontend - Variant . Detected

Action controllers and action-level guards.

A web starter skeleton - user accounts, a contact form, sessions and RBAC-guarded controller actions, rendered on the server.

- [Read more](https://www.dotkernel.com/frontend/)
- [GitHub](https://github.com/dotkernel/frontend)

### Light - Variant . Detected

Minimal modules, config-declared template routes.

The smallest complete Mezzio application - routing, pipeline and Twig, six direct dependencies and no database layer.

- [Read more](https://www.dotkernel.com/light/)
- [GitHub](https://github.com/dotkernel/light)

### Queue - Variant . Detected

Message handlers and background workers.

Background workers on Symfony Messenger - a TCP listener, Valkey streams, retries and a dead letter queue for what still fails.

- [Read more](https://www.dotkernel.com/queue/)
- [GitHub](https://github.com/dotkernel/queue)

## Open source, in production

Context your AI tools do not have to guess at.

Dotboost is maintained by Borsan Sergiu and released as open source alongside the rest of the Dotkernel ecosystem.
Treat the skills as living documents - the repository is where corrections belong.

[Read the full README ->](https://github.com/dotkernel/dotboost)
