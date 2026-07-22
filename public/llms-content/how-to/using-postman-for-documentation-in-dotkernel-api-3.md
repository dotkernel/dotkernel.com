---
title: "Using Postman for documentation in DotKernel API 3"
description: "A walkthrough of using Postman to create, maintain, and export API documentation for DotKernel API 3, including collections, environments, requests, and built-in security features."
author: "Alex Karajos"
date_published: "2021-06-14"
canonical_url: "https://www.dotkernel.com/how-to/using-postman-for-documentation-in-dotkernel-api-3/"
category: "How to's"
language: "en"
---

# Using Postman for documentation in DotKernel API 3

## TL;DR

Starting from version 3.0, DotKernel API documents its endpoints using Postman, via a provided collection and environment file that get imported into the tool.
Postman organizes work into a Workspace, Collections, Environments, and Requests, and the DotKernel API collection ships with built-in security: global Bearer Token authorization inherited from the collection root, and automatic ACCESS_TOKEN/REFRESH_TOKEN storage on the Admin/Security and User/Security folders.
After making changes, the collection and environment files can be re-exported to overwrite the application's documentation files.

## Using Postman Documentation in DotKernel API 3

Starting from version 3.0 DotKernel API provides its documentation using [Postman](https://www.postman.com/).
In this article we will go into the details of creating and maintaining your application's documentation.

## Prerequisites

In order to follow the steps, you will need the following:

- a working installation of DotKernel API 3
- download and install Postman from their [download page](https://www.postman.com/downloads/)

TIP: If you don't have a Postman account, we recommend you to create one.
This way Postman is able to backup your workspace in the cloud and synchronize it across multiple devices.

## Introduction

For better understanding on how Postman works, we need to run through the "building blocks" of Postman:

- Workspace: is the main container that holds all your collections, APIs, environments etc
- Collection: holds all requests and dependencies of a project (for example DotKernel API's collection containing all requests)
- Environment: holds a set of environment-specific variables (for example DotKernel API's environment file containing the application URL specific to the development environment)
- Request: the definition of an API endpoint, including request method, name, description, URL, body, parameters and an example

There are more terms to cover, like Mock servers, Monitors etc but they are not relevant yet in our case.

### Importing Documentation Files

Open Postman (and log in if you have an account), then follow the below steps:

- open the import modal by clicking `File` -> `Import` (or `Ctrl + O`)
- inside the modal, make sure you're on the `File` tab, then click `Upload files`
- navigate to `documentation` directory of your DotKernel API 3 application and select both of the following files:
  - DotKernel_API.postman_collection.json
  - DotKernel_API.postman_environment.json
- after clicking `Open` the modal should show the following:

```
Select files to import . 2/2 selected
NAME                 FORMAT                         IMPORT AS
DotKernel_API        Postman Collection v2.1        Collection
DotKernel_API        Postman Environment            Environment
```

- at the bottom of the import modal, clicking `Import` will import the selected files and close the modal.

Under My Workspace you should see a tab called Collections.
Clicking on it should display all your collections, one of them called `DotKernel_API`.
This collection holds all of the provided requests and serves as the documentation of your application.
DotKernel API 3 collection and environment files are now imported and ready to use.
For more information on importing files into Postman, please consult their [docs](https://learning.postman.com/docs/getting-started/importing-and-exporting-data/).

### Interacting with the Environment

On the top right corner of Postman, you'll see the Environment quick look button (it looks like an eye).
On its left side, there's a dropdown with all available environments, one of them being called `DotKernel_API` - click on it to set it as the active environment.
After this, clicking the Environment quick look button will bring up a modal containing all variables found in the environment:

```
DotKernel_API                                                                              Edit
VARIABLE               INITIAL VALUE                CURRENT_VALUE
APPLICATION_URL        http://localhost:8080        http://localhost:8080
```

If your application runs on a different host/port, click the Edit button to modify the initial/current value of the `APPLICATION_URL` variable.
Once in Edit mode, you can also create new variables.
You only have to enter the variable name and its initial value - current value will be automatically filled in when the variable is used.

## Usage

### Creating a New Request

Inside the collection, right-click on any folder and click `Add Request`.
The new request is automatically opened in a new tab, where you will:

- enter request name by replacing New Request with a short and descriptive name
- enter request description (first button under the Environment quick look button)
- enter request URL - you should use the already existing variable `APPLICATION_URL` by wrapping it in double curly braces, then append the request path.
Example: `{{ APPLICATION_URL }}/test` becomes `http://localhost:8080/test` when you send the request.
- select request method
- (optionally) inspect tabs:
  - Params: manage query parameters
  - Authorization: select authorization type
  - Headers: manage request headers
  - Body: manage request body
  - Pre-request script: provide JavaScript that Postman should run before sending the request
  - Tests: provide JavaScript that Postman should run after receiving the response
  - Settings: manage miscellaneous request settings
- save the request using the Save button (or `Ctrl + S`)

### Sending a Request

Once opened, you can immediately send a request by clicking the Send button.
Optionally, you can save the response as an example (click on Save Response, then select Save as example), so other developers will have a visual representation of the response without making a request.
This opens a new tab, where you can manage the example.

## Features

The collection provided by DotKernel API 3 comes with some built-in security features.

### Global Security

Due to the hierarchy of the Collection, all requests are children of the Collection's root folder, so they automatically inherit its Authorization settings.
If you edit the Collection's root folder, you will find the following tabs:

- Authorization
  - Type is set to `Bearer Token`
  - Token reads the environment variable `ACCESS_TOKEN`
- Pre-request Script: here you can add JavaScript that should run on ALL requests before they run
- Tests: here you can add JavaScript that should run on ALL requests after their response is returned
- Variables: here you can manage global variables

To disable this feature globally, set Authorization Type to `No Auth` or whatever value suits your application's requirements.
To disable this feature at folder/request level, edit the folder/request and set Type to `No Auth` under the Authorization tab.

### Automatic Token Storage

Once an authorization token is re/generated, two environment variables are set/updated:

- `ACCESS_TOKEN`: the token that gets sent in the Authorization header
- `REFRESH_TOKEN`: the token required to regenerate an ACCESS_TOKEN

This is achieved by applying the following modifications to both Admin/Security and User/Security folders:

- setting Type to `No Auth` under the Authorization tab (because the child requests under these folders should not send Authorization header)
- adding a script under the Tests tab, that reads the response and - if successful - set/updates the above mentioned environment variables

## Exporting Documentation

TIP: You can inspect your application's documentation at any moment by clicking on the three horizontal dots (you will find them on hover next to your collection's name) then click on View documentation.

### Exporting Collection

Click on the same three horizontal dots, then select Export.
You will be presented an Export collection modal asking you to choose the format of the export.
Unless specified, choose the recommended version, then click Export.
When asked to save the exported file, choose to overwrite your application's collection file (documentation/DotKernel_API.postman_collection.json).

### Export Environment

This step is optional.
It is needed only if you made modifications to the environment, else you can skip it.
Start by clicking on the Environment quick look button, then click Edit.
Once the edit environment tab opens, locate the three horizontal dots next to the Share button on the right of the Postman window.
Click them and select Export.
When asked to save the exported file, choose to overwrite your application's environment file (documentation/DotKernel_API.postman_environment.json).

## FAQ

**Q: What are Postman's main "building blocks"?**
A: Workspace (the main container holding all collections, APIs and environments), Collection (holds all requests and dependencies of a project, such as DotKernel API's collection), Environment (holds environment-specific variables, like the application URL), and Request (the definition of an API endpoint, including method, name, description, URL, body, parameters and an example).

**Q: How do you import DotKernel API's documentation files into Postman?**
A: Open the import modal via `File -> Import` (or `Ctrl + O`), go to the `File` tab, click `Upload files`, then select both `DotKernel_API.postman_collection.json` and `DotKernel_API.postman_environment.json` from the application's `documentation` directory, and click `Import`.

**Q: What variable controls which host/port the requests target?**
A: The `APPLICATION_URL` environment variable, found under the `DotKernel_API` environment, defaults to `http://localhost:8080` and can be edited via the Environment quick look button if the application runs on a different host/port.

**Q: What built-in security features does the collection provide?**
A: Global security: all requests inherit Bearer Token authorization (reading the `ACCESS_TOKEN` environment variable) from the Collection's root folder, which can be disabled globally or per folder/request.
Automatic token storage: the Admin/Security and User/Security folders use Test scripts to set/update the `ACCESS_TOKEN` and `REFRESH_TOKEN` environment variables whenever a token is re/generated.

**Q: How do you export the collection after making changes?**
A: Click the three horizontal dots next to the collection's name, select Export, choose the recommended format, click Export, and overwrite the application's collection file (documentation/DotKernel_API.postman_collection.json).
