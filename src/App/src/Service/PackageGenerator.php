<?php

declare(strict_types=1);

namespace Light\App\Service;

use DateTimeImmutable;
use DateTimeInterface;
use JsonException;
use RuntimeException;

use function array_flip;
use function array_key_exists;
use function array_keys;
use function count;
use function dirname;
use function file_get_contents;
use function file_put_contents;
use function is_array;
use function is_dir;
use function is_file;
use function is_string;
use function json_decode;
use function json_encode;
use function mkdir;
use function preg_match;
use function rename;
use function sprintf;
use function strtolower;
use function trim;
use function unlink;
use function usort;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const PHP_INT_MAX;

/**
 * Builds the packages listing from a GitHub organisation.
 *
 * A repository is considered a published package when it contains an `OSSMETADATA` file; that
 * file is also the source of the lifecycle value, so repositories opt in simply by adding it.
 *
 * @phpstan-type PackageData array{
 *     name: string,
 *     url: string,
 *     lifecycle: string,
 *     php: string|null,
 *     archived: bool,
 * }
 * @phpstan-type ReportData array{
 *     written: int,
 *     skipped: list<string>,
 *     ignoredMisses: list<string>,
 *     warnings: list<string>,
 * }
 */
readonly class PackageGenerator
{
    /**
     * Display order for the generated listing. Anything unrecognised sorts last.
     */
    private const array LIFECYCLE_ORDER = [
        'active'        => 0,
        'maintenance'   => 1,
        'security-only' => 2,
        'archived'      => 3,
    ];

    /**
     * Fraction of failed requests above which the run is abandoned rather than writing a
     * partial listing over a known-good one.
     */
    private const float FAILURE_THRESHOLD = 0.2;

    private const string GITHUB_ROOT = 'https://github.com/';

    /**
     * @param list<string> $ignoreRepos
     */
    public function __construct(
        private GitHubClientInterface $client,
        private string $dataFile,
        private string $org,
        private array $ignoreRepos,
        private bool $includeArchived,
    ) {
    }

    public function getDataFile(): string
    {
        return $this->dataFile;
    }

    /**
     * Queries the organisation and rewrites the data file.
     *
     * @return ReportData
     * @throws RuntimeException When the listing cannot be retrieved, too many per-repository
     *                          requests fail, or the data file cannot be written.
     * @throws JsonException
     */
    public function write(): array
    {
        $ignore     = $this->buildIgnoreLookup();
        $ignoreHits = [];
        $packages   = [];
        $skipped    = [];
        $warnings   = [];
        $attempts   = 0;
        $failures   = 0;

        $repositories = $this->client->getAllPages(
            sprintf('/orgs/%s/repos?per_page=100&type=public', $this->org)
        );

        foreach ($repositories as $repository) {
            $name = isset($repository['name']) && is_string($repository['name'])
                ? trim($repository['name'])
                : '';

            if ($name === '') {
                continue;
            }

            $key = strtolower($name);
            if (array_key_exists($key, $ignore)) {
                $ignoreHits[$key] = true;
                $skipped[]        = $name;
                continue;
            }

            $archived = (bool) ($repository['archived'] ?? false);
            if ($archived && ! $this->includeArchived) {
                continue;
            }

            $attempts++;
            try {
                $metadata = $this->client->get(
                    sprintf('/repos/%s/%s/contents/OSSMETADATA', $this->org, $name),
                    GitHubClientInterface::ACCEPT_RAW
                );
            } catch (RuntimeException $exception) {
                $failures++;
                $warnings[] = sprintf('%s: could not read OSSMETADATA (%s)', $name, $exception->getMessage());
                continue;
            }

            if ($metadata === null) {
                // No OSSMETADATA: not a published package.
                continue;
            }

            $lifecycle = $this->parseLifecycle($metadata);
            if ($lifecycle === null) {
                $warnings[] = sprintf('%s: OSSMETADATA present but no osslifecycle value found, skipped', $name);
                continue;
            }

            $php = null;
            $attempts++;
            try {
                $composer = $this->client->get(
                    sprintf('/repos/%s/%s/contents/composer.json', $this->org, $name),
                    GitHubClientInterface::ACCEPT_RAW
                );
                if ($composer !== null) {
                    $php = $this->parsePhpConstraint($composer);
                }
            } catch (RuntimeException $exception) {
                $failures++;
                $warnings[] = sprintf('%s: could not read composer.json (%s)', $name, $exception->getMessage());
            }

            $packages[] = [
                'name'      => $name,
                'url'       => self::GITHUB_ROOT . $this->org . '/' . $name,
                'lifecycle' => $lifecycle,
                'php'       => $php,
                'archived'  => $archived,
            ];
        }

        if ($attempts > 0 && $failures / $attempts > self::FAILURE_THRESHOLD) {
            throw new RuntimeException(sprintf(
                'Aborting without writing: %d of %d requests failed, which exceeds the %d%% threshold.',
                $failures,
                $attempts,
                (int) (self::FAILURE_THRESHOLD * 100)
            ));
        }

        usort($packages, static function (array $left, array $right): int {
            $leftRank  = self::LIFECYCLE_ORDER[$left['lifecycle']] ?? PHP_INT_MAX;
            $rightRank = self::LIFECYCLE_ORDER[$right['lifecycle']] ?? PHP_INT_MAX;

            return [$leftRank, $left['name']] <=> [$rightRank, $right['name']];
        });

        $this->save($packages);

        $ignoredMisses = [];
        foreach (array_keys($ignore) as $ignored) {
            if (! array_key_exists($ignored, $ignoreHits)) {
                $ignoredMisses[] = $ignored;
            }
        }

        return [
            'written'       => count($packages),
            'skipped'       => $skipped,
            'ignoredMisses' => $ignoredMisses,
            'warnings'      => $warnings,
        ];
    }

    /**
     * Returns the generated listing, or null when it is missing or unreadable.
     *
     * @return array<string, mixed>|null
     */
    public function read(): ?array
    {
        if (! is_file($this->dataFile)) {
            return null;
        }

        $contents = file_get_contents($this->dataFile);
        if ($contents === false || trim($contents) === '') {
            return null;
        }

        $decoded = json_decode($contents, true);
        if (! is_array($decoded) || ! isset($decoded['packages']) || ! is_array($decoded['packages'])) {
            return null;
        }

        return $decoded;
    }

    /**
     * @param list<PackageData> $packages
     * @throws RuntimeException
     * @throws JsonException
     */
    private function save(array $packages): void
    {
        $payload = [
            'generated_at' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM),
            'org'          => $this->org,
            'packages'     => $packages,
        ];

        $json = json_encode(
            $payload,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );

        $directory = dirname($this->dataFile);
        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException(sprintf('Unable to create directory %s.', $directory));
        }

        // Write to a sibling file and rename, so a crash mid-write cannot leave truncated JSON
        // behind for the request handler to read.
        $temporaryFile = $this->dataFile . '.tmp';
        if (file_put_contents($temporaryFile, $json) === false) {
            throw new RuntimeException(sprintf('Unable to write %s.', $temporaryFile));
        }

        if (! rename($temporaryFile, $this->dataFile)) {
            unlink($temporaryFile);
            throw new RuntimeException(sprintf('Unable to move %s into place.', $temporaryFile));
        }
    }

    /**
     * @return array<string, int>
     */
    private function buildIgnoreLookup(): array
    {
        $names = [];
        foreach ($this->ignoreRepos as $repository) {
            $normalised = strtolower(trim($repository));
            if ($normalised !== '') {
                $names[] = $normalised;
            }
        }

        return array_flip($names);
    }

    private function parseLifecycle(string $contents): ?string
    {
        if (preg_match('/osslifecycle\s*=\s*([^\s#]+)/i', $contents, $matches) !== 1) {
            return null;
        }

        $lifecycle = strtolower(trim($matches[1]));

        return $lifecycle === '' ? null : $lifecycle;
    }

    private function parsePhpConstraint(string $contents): ?string
    {
        $decoded = json_decode($contents, true);
        if (! is_array($decoded) || ! isset($decoded['require']) || ! is_array($decoded['require'])) {
            return null;
        }

        $php = $decoded['require']['php'] ?? null;
        if (! is_string($php) || trim($php) === '') {
            return null;
        }

        return trim($php);
    }
}
