<?php

declare(strict_types=1);

namespace Light\App\Service;

use function preg_match;
use function preg_match_all;
use function preg_replace;
use function trim;

use const PREG_SET_ORDER;

/**
 * Pulls the trailing `## FAQ` section (a `**Q: ...**` / `A: ...` pair per question) out of a
 * Markdown article body, so it can be rendered as an accordion and fed into JSON-LD separately
 * from the rest of the body.
 */
final class FaqExtractor
{
    /**
     * @return array{faq: list<array{question: string, answer: string}>, body: string}
     */
    public static function extract(string $body): array
    {
        if (preg_match('/^## FAQ\s*$(.*)\z/ms', $body, $section, offset: 0) !== 1) {
            return ['faq' => [], 'body' => $body];
        }

        $faq = [];
        preg_match_all(
            '/\*\*Q:\s*(.+?)\*\*\s*\n\s*A:\s*(.+?)(?=\n\s*\n|\z)/s',
            $section[1],
            $matches,
            PREG_SET_ORDER
        );
        foreach ($matches as $match) {
            $faq[] = [
                'question' => trim($match[1]),
                'answer'   => trim(preg_replace('/\s+/', ' ', $match[2]) ?? ''),
            ];
        }

        $body = trim((string) preg_replace('/^## FAQ\s*$.*\z/ms', '', $body));

        return ['faq' => $faq, 'body' => $body];
    }
}
