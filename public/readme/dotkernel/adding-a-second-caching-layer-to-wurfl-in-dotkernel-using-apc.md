---
title: "Adding a second caching layer to WURFL in Dotkernel using APC"
description: "How adding a small, custom APC-based caching layer on top of WURFL's own cache cut response time by an order of magnitude."
author: "Adrian"
date_published: "2011-10-14"
canonical_url: "https://www.dotkernel.com/dotkernel/adding-a-second-caching-layer-to-wurfl-in-dotkernel-using-apc/"
category: "Dotkernel"
language: "en"
---

# Adding a second caching layer to WURFL in Dotkernel using APC

## TL;DR
On a high-traffic project using WURFL, profiling showed WURFL's default filesystem cache was costing up to a few hundred milliseconds per request. Adding a small, custom second cache layer on top of WURFL, built on APC, cut response time by an order of magnitude, down to 20-30ms.

## The problem

On one recent project that used WURFL, response time was an important factor. Profiling revealed that the greatest chunk of response time (up to a few hundred milliseconds) was taken up by WURFL. The default filesystem cache turned out to be too slow for a relatively high-traffic application.

## How WURFL's caching works

1. The device data is stored at first in a large, zipped XML file, with one entry for each device.
2. When first called, WURFL unzips the file and reads each device entry.
3. It then serializes the data and writes it to the cache, using an MD5 signature for the file name (or key name if the cache is not on the filesystem).
4. When a user agent is looked up, its MD5 signature is computed and then searched in the cache.
5. Because the data is stored as a tree, with each device inheriting the properties of the nodes above it, **each look-up requires a number of files to be read and their capabilities merged** to get all the capabilities of the requested device.

WURFL also has cache providers for APC and memcache, which were tried, but the results weren't impressive.

## The solution

The team realized their approach was wrong for their use case — the WURFL entry for a device has lots of fields that weren't actually used.

The solution was adding a **second cache layer** on top of WURFL's own cache, which only cached the fields that were actually needed. This second layer used **APC**, storing arrays of data in **User Cache Entries**.

This small change (under 10 lines of code) decreased response time by an **order of magnitude**, down to 20-30ms.

## FAQ

**Q: Why was WURFL's default caching too slow for this project?**
A: Profiling revealed that WURFL's default filesystem cache was taking up to a few hundred milliseconds of response time, which was too slow for a relatively high-traffic application.

**Q: How does WURFL's caching work by default?**
A: Device data is stored in a large zipped XML file. On first use, WURFL unzips the file, serializes each device's data, and writes it to cache using an MD5 signature of the user agent as the key. Because devices are stored as a tree inheriting properties from parent nodes, each lookup requires reading and merging several files.

**Q: Did WURFL's built-in APC or memcache cache providers solve the problem?**
A: No. The team tried WURFL's existing cache providers for APC and memcache, but the results weren't impressive.

**Q: What was the actual solution?**
A: Adding a second cache layer on top of WURFL's own cache, using APC and storing arrays of only the specific fields they actually needed in User Cache Entries. The change was under 10 lines of code.

**Q: What performance improvement did this bring?**
A: Response time decreased by an order of magnitude, down to about 20-30ms.
