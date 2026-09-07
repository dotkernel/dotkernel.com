---
title: "MIT versus LGPL in practice: Dotkernel case"
description: "How Dotkernel handled discovering an LGPL v3 dependency in one of its MIT-licensed packages."
author: "Florin Bidirean"
date_published: "2024-04-15"
canonical_url: "https://www.dotkernel.com/licensing/mit-versus-lgpl-in-practice-dotkernel-case/"
category: "Licensing"
language: "en"
---

# MIT versus LGPL in practice: Dotkernel case

## TL;DR
Dotkernel discovered that an upstream dependency, matomo/device-detector, was licensed under LGPL v3 - a more restrictive license than the MIT license Dotkernel uses for its own projects.
Because the more restrictive license would have to apply to the whole application, Dotkernel implemented a workaround: it stopped bundling that dependency by default and documented the licensing implications.

After a recent analysis, we discovered that **one of the upstream packages** we use is **licensed under [LGPL v3](https://www.gnu.org/licenses/lgpl-3.0.en.html)**. Even though we at **Dotkernel** use the **[MIT](https://opensource.org/license/mit)** license for our open source projects, the more restrictive license must be applied to the whole application. We implemented a workaround detailed below.

## Detailing the problem

The package in question is [**matomo/device-detector**](https://github.com/matomo-org/device-detector) which requires the developers to **share their derivative code publicly**. This goes against our contractual obligations to the client to keep their custom code business and enterprise friendly. The conflict that results in this scenario may cause **legal issues** in the future.

## The solution

Some companies explicitly steer clear of scenarios of this nature and Dotkernel has decided to do the same. Our **solution** for Dotkernel applications and libraries is to **discard the use of 3rd party packages with LGPL v3**. In this particular case we used the **matomo/device-detector** package in our [**dotkernel/dot-user-agent-sniffer**](https://github.com/dotkernel/dot-user-agent-sniffer) package for the purpose of identifying the user agent and using the results in internal reports.

ALL of **[Dotkernel's](https://github.com/dotkernel)** packages are licensed under **MIT** which **has no restrictions** regarding the **source code**, other than keeping the license and copyright notice in a file within each package. Other non-restrictive licenses include Apache-2.0, BSD-2-Clause, BSD-3-Clause, ISC, MPL-2.0 and OSL-3.0. Some of them are used by the dependencies in Dotkernel’s packages instead of MIT, but are still deemed acceptable.

## The resolution

The **dotkernel/dot-user-agent-sniffer** package **must follow the LGPL v3 license**, beginning with version 3.4.0. Our [admin application](https://github.com/dotkernel/admin) will not use the features from matomo/device-detector, but will contain instructions on how to add the package, if other developers intend to use it.

## Important note

**Warning: Any application using dotkernel/dot-user-agent-sniffer with a version lower than 3.4.0 is still a legal liability**. We at Dotkernel will not abandon the issue as is, but are looking into a solution to bring back the device detector functionality in the future under a less restrictive license. For now we will not include dotkernel/dot-user-agent-sniffer in any of our applications by default.

Rob Allen has created an automation to check the licenses of installed packages. You can follow his article [here](https://akrabat.com/check-licenses-of-composer-dependencies/).

## FAQ

**Q: Why did Dotkernel need a workaround for this package?**
A: An analysis found that an upstream package, matomo/device-detector, was licensed under LGPL v3, which requires developers to share their derivative code publicly. Since Dotkernel's own projects use the MIT license, and the more restrictive license would have to apply to the whole application, this conflicted with contractual obligations to keep client code business and enterprise friendly.

**Q: Where was the LGPL v3 package being used?**
A: The matomo/device-detector package was used in Dotkernel's dot-user-agent-sniffer package to identify the user agent and use the results in internal reports.

**Q: What solution did Dotkernel adopt?**
A: Dotkernel decided to discard the use of third-party packages licensed under LGPL v3. All of Dotkernel's own packages are licensed under MIT, which has no restrictions on the source code other than keeping the license and copyright notice in a file within each package. Other non-restrictive licenses considered acceptable include Apache-2.0, BSD-2-Clause, BSD-3-Clause, ISC, MPL-2.0 and OSL-3.0.

**Q: What changed in dot-user-agent-sniffer as a result?**
A: Starting with version 3.4.0, the dotkernel/dot-user-agent-sniffer package must follow the LGPL v3 license. The admin application will not use the features from matomo/device-detector, but will contain instructions for developers who intend to add the package themselves.

**Q: Is it still risky to use an older version of dot-user-agent-sniffer?**
A: Yes. Any application using dotkernel/dot-user-agent-sniffer with a version lower than 3.4.0 is still a legal liability. Dotkernel states it will not include dotkernel/dot-user-agent-sniffer in any of its applications by default for now, while it looks into bringing back the device detector functionality under a less restrictive license in the future.
