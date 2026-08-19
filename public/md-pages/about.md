---
title: "About us | The team behind Dotkernel"
description: "Dotkernel is built by the dev team at Apidemia - a team that reviews every change together, gives its work back to the PHP community under MIT, and puts guardrails around the tools it adopts."
canonical_url: "https://www.dotkernel.com/about/"
language: "en"
---

# The team behind Dotkernel

About us . Apidemia

Dotkernel started as an internal tool for handling complex architectures, built because we needed it ourselves.
We released it under MIT because the PHP community gave us the components it stands on.
Everything here is made by a team that has to live with its own decisions.

- [Talk to us](https://www.dotkernel.com/contact/)
- [View on GitHub](https://github.com/dotkernel)
- [Join the discussion](https://github.com/orgs/dotkernel/discussions)

| | |
| --- | --- |
| Team | Apidemia |
| License | MIT |
| Released continuously since | 2018 |

## How a change ships

Propose (plan before code) -> Build (house structure) -> Review (another pair of eyes) -> QA gate (cs . PHPStan . tests) -> Document (for the next person) -> Release (MIT, in public).

## A team that shares the whole codebase

Nobody here owns a private corner of the code.
Every module follows the same structure, every handler is named the same way, and every application is assembled from the same explicit wiring - so any developer on the team can open any part of the platform and know where they are.
That is a deliberate design choice, and it is what makes reviewing each other's work possible rather than polite.

Getting better at this is part of the job, not something we hope happens after hours.
Our developers read the upstream source of the components we depend on, write the documentation the next person will need, and turn every repeated mistake into a written rule instead of a reprimand.
When a review catches the same thing twice, that is a gap in what we have written down - and we fix the writing.

Extending the power of Mezzio by Laminas.

- Every change reviewed by someone else
- One structure across every application
- Learning time counted as real work
- Decisions written down, not remembered

## Six things we are not willing to trade

None of these are aspirations we printed on a wall.
Each one shows up somewhere you can check - in a repository, in a license, in a config file, or in the way a release is handled.

### Better together than alone — How we work . Teamwork

The codebase is the team's, not a collection of territories.

Work is planned out loud before it is written, reviewed before it is merged, and documented before it is called done.
Consistency is what makes that cheap: because a module in one application looks like a module in every other, a reviewer spends their attention on the decision being made rather than on finding their way around.

### Developers who keep growing — People . Improvement

Nobody stays where they started.

Our developers learn by reading the source of the components they build on, by writing the guides and reference docs the rest of the team relies on, and by taking the review notes that come back to them as material rather than criticism.
Every breaking change we publish ships with the article explaining it - writing that explanation is how we make sure we actually understood the change.

### Giving back to PHP — Community . Open source

MIT licensed, and answered in public.

Dotkernel stands on Mezzio, Laminas and Doctrine, so our work goes back out under a permissive license with nothing held behind a paid tier.
The team answers questions on our own components and on the Laminas ones underneath them, publishes the support status of every `dot-*` package instead of leaving it to guesswork, and keeps the discussion where anyone can read it.

- [Packages lifecycle](https://www.dotkernel.com/dotkernel-packages-oss-lifecycle/)
- [Discussions](https://github.com/orgs/dotkernel/discussions)

### Every need you can imagine — Customers . Delivery

The unglamorous requirement is the one that decides a project.

Our skeletons come out of real client work, which is why they answer questions nobody asks in a demo: sharing a database with an application that was there first, running several connections at once, anonymizing an account instead of deleting the row, hearing about a bug that never threw an exception.
We would rather carry that detail for you than let you discover it in your second sprint.

### AI with guardrails on — Tooling . Responsible AI

Adopted everywhere, trusted nowhere by default.

AI assistance runs through every part of how we work, under rules we wrote down first.
Secrets are never loaded rather than merely redacted, so credentials and signing keys stay out of the context window entirely.
Sessions start by proposing a plan instead of editing files.
Linters report and never rewrite.
A package is never named from memory - it is verified against what is actually installed.
And a human reviews every diff, because accountability does not delegate.

- [How we do it](https://www.dotkernel.com/dotboost/)

### Best product, best tools — Craft . Quality

Chosen for what they will still be worth in three years.

We build on interfaces from the PHP Framework Interop Group so a component from one vendor can be replaced by another, keep static analysis at a strict level, ship the test suite with the skeleton rather than promising it later, and stay current with the PHP versions our dependencies support.
When a tool stops being the right answer, we say so - publicly, with a migration path.

- [See the architecture](https://www.dotkernel.com/architecture/)

## Where you can check any of this

### The QA gate

Coding standards, static analysis at a strict rule level and the test suite all run before a change is merged - and "make the checker quiet" is never an accepted fix.

### Documentation as a deliverable

A feature is not finished until the next developer can pick it up from what is written, which is why [docs.dotkernel.org](https://docs.dotkernel.org/) is maintained alongside the code.

### Deprecations, not surprises

Endpoints announce their own retirement through response headers, so a client finds out from the API rather than from a changelog nobody read.

### Support status in public

Every package publishes where it sits in its lifecycle, so choosing one is never a bet on whether it is still maintained.

### Secrets stay out of the tooling

Local configuration, environment files and OAuth keys are unreadable to our AI tooling by configuration, not by convention.

### Supported by JetBrains

Our open-source tooling is supported through the [JetBrains open source programme](https://jb.gg/OpenSourceSupport) - one of the ways this work stays sustainable.

[Work with us ->](https://www.dotkernel.com/contact/)

## Open source, in production

Built by a team that has to live with its own decisions.

Dotkernel is developed and led by the dev team at Apidemia.
If you are weighing up a platform, hiring a team, or just want to argue about middleware, we would rather have the conversation than the brochure.

[Talk to us ->](https://www.dotkernel.com/contact/)
