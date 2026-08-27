---
title: "Naming pattern for PSR-15 handlers in Dotkernel applications"
description: "This naming pattern is used in Dotkernel Admin v6 and will also be implemented in the next releases for Frontend and Light."
author: "Florin Bidirean"
date_published: "2025-04-17"
canonical_url: "https://www.dotkernel.com/design-pattern/naming-pattern-for-psr-15-handlers-in-dotkernel-applications/"
category: "Design Patterns"
language: "en"
---

# Naming pattern for PSR-15 handlers in Dotkernel applications

> This naming pattern is used in Dotkernel Admin v6 and will also be implemented in the next releases for Frontend and Light.

The bigger a project is, the more time it will take to develop and the more people will be assigned to it. Each developer will use slight variations when it comes to naming files, which is not going to be consistent, sometimes not even with work he has done on the same project in the previous month.

It might seem ok to use the file names 'CreateUser.php' as much as 'AddUser.php' or 'InsertUser.php', since they perform the same action. But consider that you might have 'CreateUser.php', 'AddProduct.php' and 'InsertOrder.php' in the same application. It's still readable, but chaotic. We will show you a better way to handle file naming, using naming patterns.

## What is a naming pattern?

A naming pattern helps you organize and quickly identify your files by using relevant strings in file names like:

- What a file refers to.
- The action a file performs.
- How a file relates to other files.
- The author of the file's contents.
- The creation date or the event the file refers to.

Naming patterns can be defined for different types of files, since `image` files might need other information than `php` files or simple `txt` files used for logs.

Here is a list of items to consider:

- The file names should be kept as **short** as possible, while retaining relevant items to help outline the file's purpose.
- **Abbreviations** are ok to use, but special characters should be avoided, excepting dash and underscore which are fine, no matter the operating system you use.
- **Versioning and metadata** can also help visually.
- Grouping files into **folders** is also recommended, especially when you are dealing with files that are related.
- If **category names** are relevant to use, you can standardize their names by using a shortened version, maybe with 2-3 letters.

After defining your naming pattern, the most important item by far is to **communicate the pattern to the team**. A top-level README file with the documentation should be kept handy for any developer who creates new files.

## The naming pattern for Dotkernel Handlers

HTTP request handlers are at the core of any web application. They receive a request, process it and return a response.

Even the first paragraph above mentions several elements that are relevant. The naming pattern for our `Handlers` contains:

- The **method** or verb used by the handler (e.g. GET, POST).
- The **resource** name (e.g. Admin, Account).
- The performed **action** (e.g. CreateForm, List).
- An optional **Form** if the handler returns a form that will perform another action when submitted.
- The string **Handler**.

In this way, the developer can easily figure out what each handler does, just by looking at the name.

We have chosen this wording for the performed actions (or CRUD):

- **Create**
- **Get** for Read
- **Edit** for Update
- **Delete**

## A practical example

Let's assume your application requires you to create products managed by admin users. So how do you go about naming a new set of files for this purpose?

First, you need a form to fill out. The form is returned by a `handler` called with `get`. Its purpose is to perform a `create` action for an `product`, hence you name it: `GetProductCreateFormHandler`.

Then you send the form data to be saved as a new product. The second `handler` will `create` the new `product` account via `post`, so you get: `PostProductCreateHandler`.

You will likely need to update and delete products further down the line, so you create the handlers: `GetProductEditFormHandler`, `PostProductEditHandler` and `PostProductDeleteHandler`. The final handler that makes sense in this group is `GetProductListHandler` that will list all the products you have created, with filtering included.

It takes only a minute to build the proper name for each handler, which takes you and other team members no more than a second to figure out what it does. You will thank yourself in the future.

## FAQ

**Q: What is a naming pattern?**
A: A naming pattern helps you organize and quickly identify your files by using relevant strings in file names, such as what a file refers to, the action it performs, how it relates to other files, its author, and its creation date.

**Q: What elements make up the naming pattern for Dotkernel Handlers?**
A: The method or verb used by the handler (e.g. GET, POST), the resource name (e.g. Admin, Account), the performed action (e.g. CreateForm, List), an optional Form suffix, and the string Handler.

**Q: What wording is used for the performed actions (CRUD)?**
A: Create, Get for Read, Edit for Update, and Delete.

**Q: Where is this naming pattern used?**
A: It is used in Dotkernel Admin v6 and will also be implemented in the next releases for Frontend and Light.

**Q: What is a practical example of this naming pattern?**
A: For a product resource managed by admin users, you would get handlers such as `GetProductCreateFormHandler`, `PostProductCreateHandler`, `GetProductEditFormHandler`, `PostProductEditHandler`, `PostProductDeleteHandler`, and `GetProductListHandler`.

## Resources

- [PSR-15](https://www.php-fig.org/psr/psr-15/)
- [Dotkernel Application Repositories](https://github.com/dotkernel)
