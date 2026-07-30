---
title: "Error reporting endpoint in Dotkernel API"
description: "How the Dotkernel API error reporting endpoint lets frontend applications securely report bugs and data errors back to the API, including server-side and frontend setup."
author: "Florin Bidirean"
date_published: "2024-08-29"
canonical_url: "https://www.dotkernel.com/dotkernel-api/error-reporting-endpoint-in-dotkernel-api/"
category: "Dotkernel API"
language: "en"
---

# Error reporting endpoint in Dotkernel API

## TL;DR

Dotkernel API includes an error reporting endpoint that lets frontend developers securely report bugs and incorrect data processing back to the API, even when no fatal error shows up in the logs.
It works by sending a POST request to `/error-report` with a token in the header; the API validates the request against configured tokens, domains, and IPs before logging the message.
Setup involves generating a token, adding it to `config/autoload/error-handling.global.php`, and having the frontend send the `Error-Reporting-Token` and `Origin` headers.

Dotkernel API has received a lot of love from our developers, with regular updates to the platform for years.
We use Dotkernel API in our projects, so any bugs and issues are addressed as soon as they are found.
Still, it's not unlikely that some hidden issues remain in fringe use cases that we simply haven't explored.
The occurrence of bugs increases when the API is used in a complex frontend project.

Fatal errors are easily found in the API logs, but it's another matter altogether to deal with incorrect data processing that doesn't generate errors in the frontend that interfaces with the API.
The error reporting endpoint was designed to allow the frontend developers of your API to report any bugs they encounter in a secure way that is fully under your control.

## Example Case Usage

- Frontend developed in Angular.
- Frontend developer will use try-catch in the code in order to send frontend errors back to the API.

## How to Use It on the API Side

Error reporting is done by sending a POST request to the `/error-report` endpoint, together with a token in the header.
In the sections below we will detail how to configure error reporting in your API and how the endpoint is used by the frontend developers.

### Generating a Token and Adding It to Your API Config

First you need to generate a token for your request.
This is done by using the below command.

```bash
php ./bin/cli.php token:generate error-reporting
```

The resulting token has this format `0123456789abcdef0123456789abcdef01234567`.
Note: this example is provided just to let you know what to look for.

Copy the generated token in your `config/autoload/error-handling.global.php` file.
It should look similar to the example below.
Your API can have multiple tokens, if needed.

```php
return ,
        ...
    ]
]
```

### Validation Mechanism

Behind the scenes, the API validates your configuration and lets you know if any config items prevent the submission of the error report.
Below are the requirements for an application to be able to send error messages to Dotkernel API.

- Server-side requirements stored in `config/autoload/error-handling.global.php` (these can be set/overwritten in `config/autoload/local.php`):
  - All keys (`enabled`, `path`, `tokens`, `domain_whitelist` and `ip_whitelist`) must exist under `ErrorReportServiceInterface::class`.
  - The error reporting feature must be enabled by setting `ErrorReportServiceInterface::class` . `enabled` to `true`.
  - `ErrorReportServiceInterface::class` . `path` must have a value; if the destination file does not exist, it will be created automatically.
  - `ErrorReportServiceInterface::class` . `tokens` must contain at least one token.
  - At least one of `ErrorReportServiceInterface::class` . `domain_whitelist`/`ip_whitelist` must have at least one value.

Note: In `src/App/src/Service/ErrorReportService.php`, the method `checkRequest()` tries to validate the request by checking matches for `domain_whitelist` with `isMatchingDomain()` and for `ip_whitelist` with `isMatchingIpAddress()`.
If both return `false`, a `ForbiddenException` is thrown and the error message does not get stored.

- Application-side requirements:
  - Send the `Error-Reporting-Token` header with a valid token previously stored in `config/autoload/error-handling.global.php` in the `ErrorReportServiceInterface::class` . `tokens` array.
  - Send the `Origin` header set to the application's URL; this is the application that sends the error message.

Note:

- The tokens under `ErrorReportServiceInterface::class` . `tokens` do not expire.
- The log file stores the token value too, making it easy to identify which application sent the error message.

If your request passes all the checks, the message is saved in the log file specified in `ErrorReportServiceInterface::class` . `path`.

### Tips and Tricks

If there are multiple applications that report errors to your API, you can assign a different error reporting token for each.
The tokens support key-value pairs where:

- The key is an alias relevant to the assigned application that uses it.
- The value is the token itself.

Example:

```php
// ...
return ,
    ],
];
```

The log file will have entries similar to the below:

> Demo error message

The inclusion of the token helps you identify the source of the error message.
In our example, it's the application that uses the `0123456789abcdef0123456789abcdef01234567` token, which is assigned to the application `frontend`.

## How to Use It on the Frontend Side (Angular Example)

The API developer sends a generated token to the frontend developer who will save it in their `environment.staging.ts` and/or `environment.prod.ts`.
From then on, it's the frontend developer's job to set up an error reporting function similar to the one below.

```typescript
postError(body: object): Promise<any> {
     return new Promise((resolve, reject) => {
      return this.http.post(API_ENDPOINT + 'error-report', body , {headers: new HttpHeaders({'Error-Reporting-Token': 'TOKEN', 'Origin': 'https://example.com'})})).subscribe({
        next: (response: any) => {
          resolve(response);
        },
        error: (e: HttpErrorResponse) => reject(e),
        complete: () => console.info('Error on sending error'),
      });
    });
  }
```

Whenever an error is found, the frontend will call `postError()` with a relevant description under `message`.

```typescript
apiService.postError({message: 'ERROR MESSAGE'})
```

## Conclusion

The error reporting feature in Dotkernel API is a secured and highly configurable tool for users of your API to report any unwanted behavior.
More often than not, a detailed error report will help developers understand how to replicate the issue and fix it in due course.

This article is also included in the full API documentation [https://docs.dotkernel.org/api-documentation/v5/core-features/error-reporting](https://docs.dotkernel.org/api-documentation/v5/core-features/error-reporting).

## FAQ

**Q: What is the error reporting endpoint for?**
A: It lets frontend developers of an API report bugs and incorrect data processing back to the API in a secure, controlled way, which is especially useful for issues that don't show up as fatal errors in the API logs.

**Q: How do you generate a token for error reporting?**
A: Run `php ./bin/cli.php token:generate error-reporting`, then copy the resulting token into `config/autoload/error-handling.global.php`.

**Q: What server-side requirements must be met for error reporting to work?**
A: All required keys (`enabled`, `path`, `tokens`, `domain_whitelist`, `ip_whitelist`) must exist under `ErrorReportServiceInterface::class`, the feature must be enabled, `path` must have a value, `tokens` must contain at least one token, and at least one of `domain_whitelist`/`ip_whitelist` must have a value.

**Q: What headers must the frontend application send?**
A: The `Error-Reporting-Token` header with a valid stored token, and the `Origin` header set to the application's URL.

**Q: What happens if a request fails validation?**
A: The `checkRequest()` method checks the domain against `domain_whitelist` and the IP against `ip_whitelist`; if both checks fail, a `ForbiddenException` is thrown and the error message is not stored.

**Q: How is the error reporting endpoint called?**
A: By sending a POST request to the `/error-report` endpoint along with a valid token in the header.
