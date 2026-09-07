<?php

declare(strict_types=1);

namespace Light\App\Service;

use Symfony\Component\Yaml\Yaml;

use function preg_match;
use function trim;

/**
 * Splits a Markdown file's leading `---` YAML front matter block from its body.
 */
final class FrontMatter
{
    /**
     * @return array{meta: array<string, mixed>, body: string}
     */
    public static function parse(string $raw): array
    {
        if (preg_match('/\A---\r?\n(.*?)\r?\n---\r?\n?(.*)\z/s', $raw, $matches) !== 1) {
            return ['meta' => [], 'body' => trim($raw)];
        }

        /** @var array<string, mixed> $meta */
        $meta = Yaml::parse($matches[1]) ?? [];

        return ['meta' => $meta, 'body' => trim($matches[2])];
    }
}
