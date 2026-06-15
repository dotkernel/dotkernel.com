<?php

declare(strict_types=1);

namespace Light\App\Fixture;

use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Light\Blog\Entity\Article;
use Light\Blog\Entity\Author;
use Light\Blog\Entity\Category;
use Light\Blog\Enum\ArticleStatusEnum;

class ArticlesLoader extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/articles_by_category.json';
        $categories = json_decode(file_get_contents($jsonFile), true);

        $usedSlugs = [];

        foreach ($categories as $cat) {
            /** @var Category $category */
            $category = $this->getReference('category_' . $cat['id'], Category::class);

            foreach ($cat['articles'] as $articleData) {
                $wpAuthorId = $articleData['post_author'] ?? null;
                if (!$wpAuthorId || !$this->hasReference('author_' . $wpAuthorId, Author::class)) {
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
                    'publish' => ArticleStatusEnum::Published,
                    'private' => ArticleStatusEnum::Private,
                    default   => ArticleStatusEnum::Draft,
                };

                $rawDate  = $articleData['post_date'] ?? '';
                $postDate = (!empty($rawDate) && $rawDate !== '0000-00-00 00:00:00')
                    ? new DateTimeImmutable($rawDate)
                    : new DateTimeImmutable();

                $article = new Article();
                $article->setTitle($title);
                $article->setSlug($slug);
                $article->setPostDate($postDate);
                $article->setStatus($status);
                $article->setCategory($category);
                $article->setAuthor($author);

                $manager->persist($article);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AuthorsLoader::class,
            CategoriesLoader::class,
        ];
    }

    private function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}