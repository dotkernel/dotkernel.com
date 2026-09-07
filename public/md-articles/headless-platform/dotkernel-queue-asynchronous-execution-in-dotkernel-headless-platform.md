---
title: "Dotkernel Queue - Asynchronous Execution in Dotkernel Headless Platform"
description: "Overview of Dotkernel Queue, a Symfony Messenger based component for running time-consuming or resource-intensive operations asynchronously via background workers in the Dotkernel Headless Platform."
author: "Florin Bidirean"
date_published: "2025-09-04"
canonical_url: "https://www.dotkernel.com/headless-platform/dotkernel-queue-asynchronous-execution-in-dotkernel-headless-platform/"
category: "Headless Platform"
language: "en"
---

# Dotkernel Queue - Asynchronous Execution in Dotkernel Headless Platform

## TL;DR
Dotkernel Queue is a component built on Symfony Messenger (via the netglue/laminas-messenger adapter) that lets time-consuming or resource-intensive operations run asynchronously on background workers instead of inside the normal PHP request-response cycle.
An active daemon listens for TCP connections, stores incoming messages in Redis, and processes them in FIFO order, with logging, IP-whitelisting security, a configurable retry mechanism, reporting metrics, and a Dead Letter Queue for messages that fail.
Priorities and parallel execution are planned future features.

[Dotkernel Queue](https://github.com/dotkernel/queue) is a component based on [Symfony Messenger](https://github.com/symfony/messenger) that is used to queue asynchronous tasks. [netglue/laminas-messenger](https://github.com/netglue/laminas-messenger) is an adapter that integrates Symfony Messenger with the [Laminas Service Manager](https://docs.laminas.dev/laminas-servicemanager/) container for Mezzio/Laminas applications.

Some everyday **operations are time-consuming and resource-intensive**, so it's best if they run on separate machines, decoupled from the regular request-response cycle. With the addition of **asynchronous execution** performed by background workers, you can ensure that these operations are not lost, terminated by PHP because of timeouts, or interrupted by new requests. The most important benefit is that you **prevent the main platform from overloading**, allowing it to return a response, remain responsive for new requests, while the heavy lifting is scheduled to run later.

> The main goal for the queue is to NOT have the main platform wait for a response, because the queue will execute the task sometime in the future. The regular request-response cycle must be completed swiftly by the main platform, possibly with a confirmation message. After that, the main platform can check with the queue regularly to see if the requested task was completed.

## Why Use Dotkernel Queue?

You might be inclined to go straight to Symfony Messenger and remove the middle man. There are several benefits that you gain from using Dotkernel Queue:

- It uses the Symfony Messenger mechanisms at its heart. Symfony Messenger will be kept updated as new updates are released.
- netglue/laminas-messenger turns the component into a middleware which is compatible with Mezzio/Laminas.
- It is seamlessly compatible with the Dotkernel Headless Platform, because it has the same file structure and supports the same Core submodule. The Core submodule is shared by other Dotkernel applications suite (Frontend, API, Admin) and ensures consistency across your platform.
- Dotkernel Queue is a standalone application in its own right, so it can also be added more easily in your existing platform.
- We implemented queue features that we use in our own projects, so we can quarantee their worth in live projects.

## Why Decouple Operations from the HTTP Request-Response?

By default, the **PHP settings** impose a **maximum execution time** of 30 seconds via the `max_execution_time` parameter. You could extend the interval, but certainly not to hours or days for a simple request. Even that 30 second interval is unreasonably long in any website which normally expects a response in a few seconds or even under 1 second. The solution is to **delegate the execution** of those tasks to an outside system specifically designed for this purpose.

## What Are Tasks with Long Execution?

Normally, the tasks delegated to the queue:

- Take extended periods of time to execute and may be interrupted by PHP limitations.
- Are external calls which introduces delays from authentication, awaiting responses from potentially multiple interrogations, or may even fail completely because the 3rd party server is offline.
- Are not part of the main PHP request-response cycle.

Many tasks can take an extended interval of time to finish execution:

- **Data Processing** like big data analytics, scientific simulations, or mathematical computations.
- **File Handling & Media Processing** like video and image processing, or compression/decompression of large files.
- **Networking** like sending email, newsletters, notifications using 3rd party providers.
- **Database Operations** like imports/exports or migrations.
- **System & Infrastructure Tasks** like OS updates, software compilation, CI pipelines.

There are multiple reasons for the long execution times:

- Data size - gigabytes, terabytes etc.
- Complex algorithms.
- Hardware limitations - CPU speed, memory, storage, network bandwidth.
- External dependencies - waiting on APIs or human input.

The tasks listed above take a long time no matter how many gigahertz, gigabytes and gigaflops you throw at them. Even NASA computers need minutes to compute the launch window to put a rocket in orbit.

## How the Dotkernel Queue Works

The queue system has an active **daemon** that listens for TPC connections on a specific port and **stores incoming messages** into Redis. This method supports a large number of requests per second without overloading. Operations are then scheduled for execution when resources are available. The order of the execution uses the **FIFO** (First-In, First-Out) method where the oldest request is processed first, followed by newer requests.

![](/uploads/article/019f8a80-cc97-70d1-a431-cb612f641c19/Queue-process2.png)

## Main Features

We feel that a queue would be incomplete without at least these features. Several more features are in the works or scheduled for the future - we will discuss these in the next segment. We are using this queuing component in our live projects, so every feature has been tried and tested extensively in production before making it in this open-source version of Dotkernel Queue.

### Logging

Logging is a vital feature that allows developers to monitor operations in a platform and investigate issues. It can help pinpoint the cause of an error which leads to faster bug fixes and less downtime.

Detailed queue logs ensure the execution of long operations is maintainable and allows debugging. You must code your project-specific core logic to satisfy your business requirements.

### Security

Access control is secured by the **firewall** which only allows requests from whitelisted IPs. This ensures that the queue responds as fast as possible, without delays from e.g. generating and authenticating dynamic tokens.

Of course, a better security setup is to keep the Dotkernel Queue server accessible only via the internal network. In his case, you don't need the firewall, which simplifies the initial queue setup.

### Retry Mechanism

If message processing fails, the **internal retry feature** comes into play to guarantee reliable and stable execution. The system can be configured to retry failing tasks a certain number of times - while logging the cause - before giving up and reporting a failure in execution.

After configuring the numer of retries for the queue, you are responsible for the additional handling of failed messages. In some cases, it makes sense to give up on retrying an operation from the first failure, e.g. if an email is invalid, it won't fix itself on the next try. Other times, the cause of the error is temporary, e.g. a database is overloaded, but the queries are valid, so they just need to run later.

### Reporting

The logs enable developers to investigate various metrics via console commands like:

- Queue length - How many jobs are in the to-do list.
- Processing time per job.
- Error rates - How many messages failed.
- Throughput - Jobs/sec processed.

### Dead Letter Queue (DLQ)

A **Dead-Letter Queue** is a separate message queue that **temporarily stores messages that failed execution** due to errors. The reason for the errors can vary from having incomplete messages, to the 3rd party receiver not being able to process the request or not being available at all (being unresponsive).

Certain errors may move the messages into the DLQ on the first try which ensures that the main queue is not overflowing with or outright blocked by messages that will never be processed in their current state. At the same time, it provides troubleshooters a central location to help identify the causes for errors. The queue will allow troubleshooters to apply fixes, then either manually push the messages back into the main queue, or delete them altogether.

## Future Features

We are looking into ways to improve Dotkernel Queue and add new features as they are needed in the field.

### Priorities

Currently, the queue system works on the FIFO method, like we mentioned before. **Priorities** determine how soon a task is to begin execution - immediately or delayed. Certain tasks may be fast enough that they can be handled as they come in, while other tasks are purposefully pushed further down the line. Priorities are **set to be integrated** into Dotkernel Queue in the near future.

### Parallel Execution

**Execution** is made for each task, **one at a time**. We are investigating **parallel execution** of operations via multiple workers, where applicable. For example, preprocessing a report may be executed in parallel with sending emails because they are separate systems (email processing is external, while the report uses data from the internal database).

## Additional Resources

- [Dotkernel Queue Documentation](https://docs.dotkernel.org/queue-documentation/v1/overview/)
- [Dotkernel Repository](https://github.com/dotkernel/)
- [Symfony Messenger Documentation](https://symfony.com/doc/current/messenger.html)
- [Symfony Messenger systemd](https://jolicode.com/blog/symfony-messenger-systemd)
- [How To Prioritize Messages When Building Asynchronous Applications With Symfony Messenger](https://sensiolabs.com/blog/2025/how-to-prioritize-messages-when-building-asynchronous-applications-with-symfony-messenger)

## FAQ

**Q: What is Dotkernel Queue?**
A: Dotkernel Queue is a component based on Symfony Messenger used to queue asynchronous tasks. It uses the netglue/laminas-messenger adapter to integrate Symfony Messenger with the Laminas Service Manager container for Mezzio/Laminas applications.

**Q: Why use Dotkernel Queue instead of using Symfony Messenger directly?**
A: Dotkernel Queue builds on Symfony Messenger's mechanisms so it stays updated as Symfony Messenger evolves, uses netglue/laminas-messenger to work as Mezzio/Laminas-compatible middleware, shares the same file structure and Core submodule as the Dotkernel Headless Platform for seamless compatibility, works as a standalone application that can be added to an existing platform, and includes features already tried and tested in the team's own live projects.

**Q: Why should long-running operations be decoupled from the HTTP request-response cycle?**
A: By default, PHP imposes a maximum execution time of 30 seconds via the max_execution_time parameter, which is too long for a typical request. Instead of extending that limit, the solution is to delegate such tasks to an outside system designed for this purpose, so the main platform can return a response quickly and stay responsive to new requests.

**Q: How does the Dotkernel Queue process messages?**
A: An active daemon listens for TCP connections on a specific port and stores incoming messages into Redis, which supports a large number of requests per second without overloading. Messages are then scheduled for execution using the FIFO (First-In, First-Out) method, so the oldest request is processed first.

**Q: What happens when a queued message fails to process?**
A: A configurable retry mechanism retries failing tasks a set number of times, while logging the cause, before giving up and reporting a failure. Messages that ultimately fail can be moved into a Dead Letter Queue, a separate store for failed messages, where troubleshooters can identify the cause, apply a fix, and either push the message back into the main queue or delete it.

**Q: What features is Dotkernel Queue planning to add in the future?**
A: Two features are being investigated: Priorities, which would let tasks begin execution sooner or later instead of strictly following FIFO order, and Parallel Execution, which would let independent operations run at the same time via multiple workers, such as preprocessing a report in parallel with sending emails.
