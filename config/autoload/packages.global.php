<?php

/**
 * Dotkernel packages listing.
 *
 * Consumed by `bin/generate-packages`, which queries the GitHub organisation and writes the
 * result to `dataFile`, and the same data to `markdownFile` for `llms.txt`/`llms-full.txt` to
 * pick up. Credentials are intentionally NOT stored here - the GitHub token lives in
 * `config/autoload/local.php` under the `github` key, which is ignored by git.
 *
 * `local.php` is merged after every `*.global.php`, so any value below can be overridden locally.
 */

declare(strict_types=1);

return [
    'packages' => [
        /**
         * ignored repositories
         * matched on the bare repository name (case-insensitive)
         */
        'ignoreRepos'     => [
            '.github',
            'admin',
            'admin-documentation',
            'apidemia.com',
            'api',
            'api-documentation',
            'app-packages',
            'api-tools-migration',
            'core',
            'development',
            'documentation',
            'documentation-theme',
            'dotboost',
            'dotkernel',
            'dotkernel.com',
            'dotkernel.github.io',
            'dotkernel.org',
            'dotkernel-v1',
            'dot-opensearch',
            'dot-privy',
            'dot-queue',
            'dot-sso-entra',
            'frontend',
            'frontend-documentation',
            'fullview',
            'headless-documentation',
            'light',
            'light-documentation',
            'mezzio-hal',
            'ng-admin',
            'ngx-admin',
            'php-llm-examples',
            'pingu',
            'plugin-mail-transporter',
            'pong',
            'presentation',
            'queue',
            'queue-documentation',
            'template',
            'template-expressive',
            'tutorial-101',
            'vue',
            'workflow-automatic-releases',
            'workflow-continuous-integration',
            'workshops',
            'ws-entities-collections',
            'zend',
            'zend-expressive-hal',
            'zf1',
        ],
        'dataFile'        => __DIR__ . '/../../public/dotkernel-packages.json',
        'markdownFile'    => __DIR__ . '/../../public/dotkernel-packages-oss-lifecycle.md',
        'includeArchived' => true,
        'timeout'         => 10,
        'connectTimeout'  => 5,
    ],
];
