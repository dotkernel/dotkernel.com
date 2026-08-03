<?php

/**
 * Dotkernel packages listing.
 *
 * Consumed by `bin/generate-packages`, which queries the GitHub organisation and writes the
 * result to `dataFile`. Credentials are intentionally NOT stored here - the GitHub token lives
 * in `config/autoload/local.php` under the `github` key, which is ignored by git.
 *
 * `local.php` is merged after every `*.global.php`, so any value below can be overridden locally.
 */

declare(strict_types=1);

return [
    'packages' => [
        /**
         * Repositories that must never appear on the packages page, matched on the bare
         * repository name (case-insensitive). The generator reports entries that matched
         * nothing, so a renamed repository does not silently reappear on the site.
         */
        'ignoreRepos'     => [
            'dotkernel.com',
        ],
        'dataFile'        => __DIR__ . '/../../data/packages.json',
        'includeArchived' => true,
        'timeout'         => 10,
        'connectTimeout'  => 5,
    ],
];
