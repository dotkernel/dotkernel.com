<?php

declare(strict_types=1);

namespace Light\App\Fixture;

use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Light\Blog\Entity\Author;
use Light\Blog\Entity\Category;
use Light\Blog\Entity\Post;
use Light\Blog\Enum\PostStatusEnum;
use RuntimeException;

use function file_get_contents;
use function html_entity_decode;
use function json_decode;
use function preg_replace;
use function strtolower;
use function trim;

use const ENT_QUOTES;

class PostLoader extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/articles_cleaned.json';
        $contents = file_get_contents($jsonFile);

        if ($contents === false) {
            throw new RuntimeException("Unable to read file: {$jsonFile}");
        }

        $categories = json_decode($contents, true);

        $repository = $manager->getRepository(Post::class);
        $usedSlugs  = [];

        foreach ($categories as $cat) {
            /** @var Category $category */
            $category = $this->getReference('category_' . $cat['slug'], Category::class);

            foreach ($cat['articles'] as $articleData) {
                $wpAuthorId = $articleData['author']['github'] ?? null;
                if (! $wpAuthorId || ! $this->hasReference('author_' . $wpAuthorId, Author::class)) {
                    echo "SKIP (no author): {$articleData['post_title']}\n";
                    continue;
                }

                /** @var Author $author */
                $author = $this->getReference('author_' . $wpAuthorId, Author::class);
                $title  = html_entity_decode($articleData['post_title'], ENT_QUOTES, 'UTF-8');
                $slug   = $this->slugify($title);

                if (isset($usedSlugs[$slug])) {
                    $usedSlugs[$slug]++;
                    $slug .= '-' . $usedSlugs[$slug];
                } else {
                    $usedSlugs[$slug] = 1;
                }

                $status = match ($articleData['post_status']) {
                    'publish' => PostStatusEnum::Published,
                    'private' => PostStatusEnum::Private,
                    default   => PostStatusEnum::Draft,
                };

                $rawDate  = $articleData['post_date'] ?? '';
                $postDate = ! empty($rawDate) && $rawDate !== '0000-00-00 00:00:00'
                    ? new DateTimeImmutable($rawDate)
                    : new DateTimeImmutable();

                $excerpt = $articleData['excerpt'] ?? '';

                $article = $repository->findOneBy(['slug' => $slug]);

                if ($article === null) {
                    $article = new Post();
                    $article->setSlug($slug);
                    $article->setTitle($title);
                    $article->setPostDate($postDate);
                    $article->setStatus($status);
                    $article->setCategory($category);
                    $article->setAuthor($author);
                    $article->setExcerpt($excerpt);

                    $manager->persist($article);
                    echo "CREATE: {$title}\n";
                } else {
                    $changed = false;

                    if ($article->getTitle() !== $title) {
                        $article->setTitle($title);
                        $changed = true;
                    }
                    if ($article->getPostDate()->format('Y-m-d H:i:s') !== $postDate->format('Y-m-d H:i:s')) {
                        $article->setPostDate($postDate);
                        $changed = true;
                    }
                    if ($article->getStatus() !== $status) {
                        $article->setStatus($status);
                        $changed = true;
                    }
                    if ($article->getCategory() !== $category) {
                        $article->setCategory($category);
                        $changed = true;
                    }
                    if ($article->getAuthor() !== $author) {
                        $article->setAuthor($author);
                        $changed = true;
                    }
                    if ($article->getExcerpt() !== $excerpt) {
                        $article->setExcerpt($excerpt);
                        $changed = true;
                    }

                    echo ($changed ? "UPDATE: {$title}\n" : "UNCHANGED: {$title}\n");
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AuthorLoader::class,
            CategoryLoader::class,
        ];
    }

    private function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
        return trim($text, '-');
    }

    public function getOrder(): int
    {
        return 3;
    }
}