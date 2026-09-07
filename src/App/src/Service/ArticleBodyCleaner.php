<?php

declare(strict_types=1);

namespace Light\App\Service;

use function preg_replace;
use function trim;

/**
 * Strips the leading `# Title` heading (already rendered separately from the article's DB
 * title) and the `## TL;DR` section (an LLM-summary artifact, not meant for human display) from
 * a Markdown article body.
 */
final class ArticleBodyCleaner
{
    public static function clean(string $body): string
    {
        $body = (string) preg_replace('/\A#[ \t][^\n]*\n/', '', $body, 1);
        $body = (string) preg_replace('/^## TL;DR\s*$.*?(?=^## |\z)/ms', '', $body, 1);

        return trim($body);
    }
}
