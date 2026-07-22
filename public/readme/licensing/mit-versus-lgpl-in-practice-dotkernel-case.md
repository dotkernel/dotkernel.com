---
title: "MIT versus LGPL in practice: Dotkernel case"
description: "How DotKernel handled discovering an LGPL v3 dependency in one of its MIT-licensed packages."
author: "Florin Bidirean"
date_published: "2024-04-15"
canonical_url: "https://www.dotkernel.com/licensing/mit-versus-lgpl-in-practice-dotkernel-case/"
category: "Licensing"
language: "en"
---

# MIT versus LGPL in practice: Dotkernel case

## TL;DR

DotKernel discovered that an upstream dependency, matomo/device-detector, was licensed under LGPL v3 - a more restrictive license than the MIT license DotKernel uses for its own projects. Because the more restrictive license would have to apply to the whole application, DotKernel implemented a workaround: it stopped bundling that dependency by default and documented the licensing implications.

## Detailing the problem

The package in question, [matomo/device-detector](https://github.com/matomo-org/device-detector), requires developers to share their derivative code publicly (LGPL v3). This conflicted with DotKernel's contractual obligations to keep client code business and enterprise friendly, and risked legal issues.

## The solution

DotKernel decided to discard the use of third-party packages licensed under LGPL v3. The matomo/device-detector package had been used in [dotkernel/dot-user-agent-sniffer](https://github.com/dotkernel/dot-user-agent-sniffer) to identify the user agent and use the results in internal reports.

All of [DotKernel's](https://github.com/dotkernel) packages are licensed under MIT, which has no restrictions on the source code other than keeping the license and copyright notice in a file within each package. Other non-restrictive licenses deemed acceptable (used by some dependencies instead of MIT) include Apache-2.0, BSD-2-Clause, BSD-3-Clause, ISC, MPL-2.0 and OSL-3.0.

## The resolution

Starting with version 3.4.0, the dotkernel/dot-user-agent-sniffer package must follow the LGPL v3 license. The [admin application](https://github.com/dotkernel/admin) will not use the features from matomo/device-detector, but will contain instructions on how to add the package for developers who intend to use it.

## Important note

Any application using dotkernel/dot-user-agent-sniffer with a version lower than 3.4.0 is still a legal liability. DotKernel will not abandon the issue, and is looking into a solution to bring back the device detector functionality in the future under a less restrictive license. For now, dotkernel/dot-user-agent-sniffer is not included in any DotKernel application by default.

## FAQ

**Q: Why did DotKernel need a workaround for this package?**
A: An analysis found that matomo/device-detector was licensed under LGPL v3, which requires sharing derivative code publicly. Since DotKernel projects use MIT, the more restrictive license would have to apply to the whole application, conflicting with contractual obligations to keep client code business friendly.

**Q: Where was the LGPL v3 package being used?**
A: In DotKernel's dot-user-agent-sniffer package, to identify the user agent and use the results in internal reports.

**Q: What solution did DotKernel adopt?**
A: Discarding the use of third-party LGPL v3 packages. All DotKernel packages are MIT licensed, and other acceptable non-restrictive licenses include Apache-2.0, BSD-2-Clause, BSD-3-Clause, ISC, MPL-2.0 and OSL-3.0.

**Q: What changed in dot-user-agent-sniffer as a result?**
A: Starting with version 3.4.0 it must follow LGPL v3. The admin application will not use matomo/device-detector's features, but will document how to add it for developers who want it.

**Q: Is it still risky to use an older version of dot-user-agent-sniffer?**
A: Yes - any application using a version lower than 3.4.0 is still a legal liability. DotKernel will not include the package in its applications by default while it looks for a less restrictive replacement.

## Resources

- [LGPL v3 license](https://www.gnu.org/licenses/lgpl-3.0.en.html)
- [MIT license](https://opensource.org/license/mit)
- [matomo/device-detector](https://github.com/matomo-org/device-detector)
- [dotkernel/dot-user-agent-sniffer](https://github.com/dotkernel/dot-user-agent-sniffer)
- [DotKernel GitHub organization](https://github.com/dotkernel)
- [DotKernel admin application](https://github.com/dotkernel/admin)
- [Rob Allen: Check the licenses of your Composer dependencies](https://akrabat.com/check-licenses-of-composer-dependencies/)
