<?php

declare(strict_types=1);

namespace Light\Page\Service;

interface PageServiceInterface
{
    /**
     * Resolves the markdown file backing a `page::{slug}` route (e.g. `page::api` -> `api.md`),
     * or null when the route isn't a static page or has no markdown counterpart.
     */
    public function resolveMarkdownFilePath(string $routeName): ?string;
}
