---
title: "WURFL PHP API license incompatible with Dotkernel"
description: "The WURFL PHP API's license changed from GNU/GPL to AGPL in version 1.3.0, making it a trial-only library incompatible with keeping Dotkernel free."
author: "admin"
date_published: "2011-06-08"
canonical_url: "https://www.dotkernel.com/dotkernel/wurfl-php-api-license-incompatible-with-dotkernel/"
category: "Dotkernel"
language: "en"
---

# WURFL PHP API license incompatible with Dotkernel

## TL;DR

The WURFL PHP API was integrated into Dotkernel long ago under a GNU/GPL license, compatible with Zend Framework's new BSD license and Dotkernel's OSL 3.0 license.
On June 6th, 2011, WURFL PHP API version 1.3.0 changed its license to AGPL, turning it into a "trial only" library for product evaluation.
Dotkernel had updated to this version in the 1.5.0 release candidate without noticing the license change.

## What happened

We integrated the WURFL PHP API into the Dotkernel code base a long time ago.
At that time its license was GNU/GPL, perfectly compatible with the Zend Framework license (new BSD) and Dotkernel (OSL 3.0).
On June 6th, 2011, the WURFL PHP API library was updated to version 1.3.0, and Dotkernel followed suit in its 1.5.0 release candidate, without noticing that the license had changed from GNU/GPL to AGPL - suddenly turning it into a "trial only" library for "Product evaluation" only.

See the [official announcement](http://www.scientiamobile.com/site/page/view/products#licenses).

## What Dotkernel plans to do

We respect the work of Luca Passani and Steve Kamerman, but we must keep [Dotkernel Application Framework](http://www.dotkernel.com) free, without such license limitations.
The only things to do at this stage are:

- Remove WURFL PHP API library version 1.3.0 from the Dotkernel code base.
- Integrate back version 1.2.1, which is still GNU/GPL.
- Keep collecting device information and contributing to the WURFL XML data, which remains free - this data file is and must be the industry standard.
- Consider forking WURFL PHP API 1.2.1 to a new library.
- Consider changing the library's name to avoid trademark issues.

*Later edit: the correct link to the announcement is [here](http://tech.groups.yahoo.com/group/wmlprogramming/message/34031).*

## FAQ

**Q: Why did the WURFL PHP API license become incompatible with Dotkernel?**
A: The WURFL PHP API used to be licensed under GNU/GPL, compatible with both Zend Framework's new BSD license and Dotkernel's OSL 3.0 license.
On June 6th, 2011, version 1.3.0 of the library changed its license to AGPL, turning it into a trial-only library for product evaluation, which is incompatible with keeping Dotkernel free.

**Q: Which version introduced the license change, and when?**
A: Version 1.3.0 of the WURFL PHP API, released June 6th, 2011, changed the license from GNU/GPL to AGPL.
Dotkernel had updated to this version in its 1.5.0 release candidate without noticing the license change.

**Q: What did Dotkernel do in response to the license change?**
A: Dotkernel planned to remove WURFL PHP API version 1.3.0 from its codebase and reintegrate version 1.2.1, which was still GNU/GPL.
It also intended to keep contributing to the free WURFL XML data, and considered forking the 1.2.1 library and renaming it to avoid trademark issues.

## Resources

- Official announcement of the license change: http://www.scientiamobile.com/site/page/view/products#licenses
- Corrected announcement link: http://tech.groups.yahoo.com/group/wmlprogramming/message/34031
- Dotkernel Application Framework: http://www.dotkernel.com
