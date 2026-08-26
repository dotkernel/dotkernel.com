<?php

declare(strict_types=1);

namespace Light\Page\Service;

use function explode;
use function is_file;

class PageService implements PageServiceInterface
{
    public function __construct(private readonly string $mdPagesPath)
    {
    }

    public function resolveMarkdownFilePath(string $routeName): ?string
    {
        $slug = explode('::', $routeName, 2)[1] ?? '';
        if ($slug === '') {
            return null;
        }

        $filePath = $this->mdPagesPath . '/' . $slug . '.md';

        return is_file($filePath) ? $filePath : null;
    }
}
