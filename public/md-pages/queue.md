---
title: "Dotkernel Queue | Asynchronous task queue for PHP"
description: "Dotkernel Queue runs time-consuming work on background workers instead of inside the request cycle. A Swoole TCP daemon, Valkey streams and Symfony Messenger, with retries, a dead letter queue and reporting commands."
canonical_url: "https://www.dotkernel.com/queue/"
language: "en"
---

# Dotkernel Queue

Background workers . Symfony Messenger

Some operations are time-consuming and resource-intensive, and they have no business inside a request.
Dotkernel Queue accepts them over a TCP connection, stores them, and runs them on background workers - so your platform answers the next request instead of waiting for a video to finish encoding.

- [Read the docs](https://docs.dotkernel.org/queue-documentation/)
- [View on GitHub](https://github.com/dotkernel/queue)

| | |
| --- | --- |
| Listener | Swoole on TCP |
| Store | Valkey streams |
| Worker | Symfony Messenger |

## Message lifecycle

Your application (JSON payload) -> TCP :8556 (IP whitelisted) -> Swoole daemon (accepts & stores) -> messages stream (Valkey . FIFO) -> Messenger worker (your handler) -> failed stream (after 3 retries).

## A separate machine for the slow work

Dotkernel Queue is built on **Symfony Messenger**, integrated into Mezzio and Laminas applications through the `netglue/laminas-messenger` adapter for the Laminas Service Manager container.
Work runs decoupled from the regular request-response cycle, on background workers that can live on their own hardware.

The main platform returns its response and stays responsive to new requests.
Tasks with long execution times are scheduled to run when resources are available, oldest first.

Extending the power of Mezzio by Laminas.

- High request rate without overloading
- FIFO - the oldest message runs first
- Retries with exponential backoff
- Failures parked, never blocking the queue

## Work that should never block a response

Tasks suited to the queue take extended periods to execute and may be interrupted by PHP limits such as `max_execution_time`; depend on external systems that add authentication delays, slow replies, or fail outright when a server is offline; or simply are not part of the PHP response at all.

### Data processing

Big data analytics, scientific simulations, mathematical computations - anything where the data size or the algorithm sets the clock.

### File & media processing

Video and image processing, compressing and decompressing large files.

### Networking

Uploading and downloading large files, where bandwidth rather than your code is the limit.

### Database operations

Imports, exports and migrations that would otherwise hold a web request open.

### System & infrastructure

OS updates, software compilation, CI pipelines triggered from your application.

### Transactional email

The classic case: hand the message to the queue and let the worker compose and send it while the server moves to the next task.

[Sending emails ->](https://docs.dotkernel.org/queue-documentation/v2/how-to/send-emails/)

## Two daemons, one stream

A listener that accepts messages fast, and a worker that processes them carefully.
They are separate systemd services, so you can restart either one without losing the other.

### The TCP listener — Ingest . Swoole

Accepts connections and gets out of the way.

An active daemon listens for TCP connections on a port - 8556 by default - and stores incoming messages immediately.
This supports a large number of requests per second without overloading, because accepting a message is all it does.

### The messages stream — Store . Valkey

An open-source key/value datastore built for this shape of work.

Messages land in the `messages` stream, the main queue.
The worker consumes from it in FIFO order - the oldest request first, then newer ones - and processes each according to your application logic.
Valkey is BSD-licensed and handles caching, message queues, and primary datastore workloads.

### The worker — Process . Messenger

Your handler, running outside the request cycle.

A Messenger worker consumes the stream and hands each message to your handler.
Because it is a separate service on a separate machine if you want one, a slow job costs you worker time rather than user-facing latency.

### Dead letter queue — Resilience . DLQ

Failures get parked, not retried forever.

Each transport defines a retry strategy.
When a message exceeds its allowed retries it is forwarded automatically to the failure transport and stored in the `failed` stream - so a message that cannot succeed never blocks the ones behind it.

### Whitelisted senders — Security . Firewall

The port is open to your servers, not to the internet.

The documented setup adds a firewall rich rule that accepts traffic on the queue's port only from the source addresses you name.
Fast to evaluate, and a small attack surface for a service that takes instructions.

### Metrics you can query — Observability . Logs

Logging that answers the operational questions.

Processed and failed messages are logged, which is what makes the reporting commands possible: queue length, processing time per job, error rates, and throughput in jobs per second.

## Backoff, configured in one place

The transport's `retry_strategy` decides how hard the worker tries before a message is handed to the dead letter queue.
These are the shipped defaults.

| Setting | Default | What it controls |
| --- | --- | --- |
| `max_retries` | 3 | Attempts before the message moves to the failure transport. |
| `delay` | 1000 | Initial wait before retrying a failed message, in milliseconds. |
| `multiplier` | 2 | Each retry's delay is multiplied by this factor - exponential backoff. |
| `max_delay` | 0 | Ceiling on the wait between retries; 0 means unlimited or default behaviour. |
| `failure_transport` | `failed` | Where messages go once they exceed the retry limit. |

Both transports are Redis-protocol DSNs pointing at a stream - `messages` for new work, `failed` for what could not be processed - with the serializer configurable per transport.

## Console commands, over CLI or TCP

The same three commands answer from the shell on the queue server or from a socket on another machine.
Every flag is optional; when both `--start` and `--end` are given, `--limit` is ignored.

| Command | Returns | Invocation |
| --- | --- | --- |
| `failed` | Log entries for messages that failed to process. | `php bin/cli.php failed --start="yyyy-mm-dd" --end="yyyy-mm-dd" --limit=int` |
| `processed` | Log entries for messages processed successfully. | `php bin/cli.php processed --start="yyyy-mm-dd" --end="yyyy-mm-dd" --limit=int` |
| `inventory` | Everything currently queued in the `messages` stream. | `php bin/cli.php inventory` |
| `control` | A test message, logged as processed - the quickest end-to-end check. | `echo "control" \| socat -t1 - TCP:host:port` |

Valkey itself stays inspectable: `valkey-cli` gives you `PING`, `INFO`, `KEYS *`, and stream commands such as `XRANGE streamName - +` to read entries oldest to newest, or `XTRIM streamName MAXLEN 0` to empty a stream while keeping the key.

## Two ways to hand over a message

The **procedural** approach opens a TCP connection, writes the JSON payload, and closes the socket.
It is simple and quick to implement - and harder to reuse as the project grows.
Make sure the message ends with a newline, or the server will keep waiting for the rest of it.

The **object-oriented** approach wraps the queue in a service - a small `NotificationSystem` module under Core, built on `clue/socket-raw`, with its connection details injected from configuration.
Handlers then inject the service and call a method that describes the intent, not the transport.

- [Integration guide](https://docs.dotkernel.org/queue-documentation/v2/how-to/communication-with-queue/)

### Smoke-test the listener

From your local machine, with the daemon running and your IP whitelisted:

```shell
echo "Hello" | socat -T1 - TCP:SERVER-IP:8556
```

The `-T1` timeout is optional but wise: without it, a server that never replies leaves `socat` waiting indefinitely.

## A queue server, from a bare box

The documented path is AlmaLinux 9 or 10 with root access, adapted accordingly for other operating systems.
Everything runs as a non-root user.

### 1 . Prepare the server

Update the OS, create a user with sudo permissions, and install the utilities you will need - including `socat` for testing.

```shell
dnf update -y && useradd dotkernel
```

### 2 . Install the runtime

PHP from the Remi repository, plus the Swoole and Redis PECL extensions.
Verify both are loaded before moving on.

```shell
dnf module enable php:remi-8.5
dnf install php-pecl-swoole6 php-pecl-redis
```

### 3 . Install Valkey

Enable and start the service, then confirm it answers.

```shell
dnf install valkey
valkey-cli ping
```

### 4 . Clone and configure

Clone the queue branch, then copy each `.dist` configuration file into place - local, log, messenger and swoole - and fill them in.

```shell
git clone -b default-queue https://github.com/dotkernel/queue.git
composer install --no-dev
```

### 5 . Register the daemons

Set the paths in the shipped unit files, copy them into `/etc/systemd/system/`, then enable and start both.

```shell
systemctl enable --now swoole.service
systemctl enable --now messenger.service
```

### 6 . Close the door

Allow SSH before starting the firewall, then permit the queue port only from the addresses that should reach it.

```shell
firewall-cmd --permanent --add-rich-rule='rule family="ipv4" source address="YOUR_IP" port port="8556" protocol="tcp" accept'
```

Full commands, expected output and the reasoning behind each step are in the [server setup](https://docs.dotkernel.org/queue-documentation/v2/server-setup/) and [installation](https://docs.dotkernel.org/queue-documentation/v2/installation/) guides.

## The third deployable

Queue processes work produced by the rest of the platform.
Share the `Core` module with it - copied in or added as a submodule - and your worker has the same entities, services and mail configuration as the application that queued the job.

### Shared domain layer

`Core\Admin` `Core\App` `Core\Security` `Core\Setting` `Core\User`

Keep Core in sync between the main project and the queue and every class, service and configuration needed for message processing is already there - which is what makes a worker able to compose an email from your own templates.

### API — Pair with . HTTP surface

Dispatch work from an endpoint, answer immediately.

A REST API on a PSR-15 middleware pipeline, with OAuth 2.0, RBAC, HAL payloads and an OpenAPI 3.0 specification wired up on install.

- [Read more](https://www.dotkernel.com/api/)
- [GitHub](https://github.com/dotkernel/api)

### Admin — Pair with . Back office

Queue a bulk operation from an admin screen.

Table-based record management with RBAC guards, CSRF-protected forms and 2FA, over the same Core module.

- [Read more](https://www.dotkernel.com/admin/)
- [GitHub](https://github.com/dotkernel/admin)

### Frontend — Pair with . Public-facing

Queue the email your signup form triggers.

A web starter skeleton - user accounts, a contact form, sessions and RBAC-guarded controller actions, rendered on the server.

- [Read more](https://www.dotkernel.com/frontend/)
- [Demo](https://v5.dotkernel.net/)

### Light — Smaller . Minimal

A site with nothing to run in the background.

The smallest complete Mezzio application - routing, pipeline and Twig, six direct dependencies and no database layer.

- [Read more](https://www.dotkernel.com/light/)
- [Demo](https://light.dotkernel.net/)

### Dotboost — Tooling . AI context

Teach your AI tools this architecture.

Drop-in Claude Code configuration - ten commands, seventeen skills and permission guardrails that keep your secrets out of the context window.

- [Read more](https://www.dotkernel.com/dotboost/)
- [GitHub](https://github.com/dotkernel/dotboost)

The queue's own dependencies are the same small `dot-*` components the rest of the platform uses - each publishing its own support status on the [packages lifecycle](https://www.dotkernel.com/dotkernel-packages-oss-lifecycle/) page.

## Open source, in production

Let the slow work happen somewhere else.

Dotkernel Queue is developed and led by the dev team at Apidemia - built to keep real platforms responsive under real load, and released as open source for the community.

[Talk to us ->](https://www.dotkernel.com/contact/)
