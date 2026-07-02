<?php

declare(strict_types=1);

namespace Light\App\Fixture;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Light\Blog\Entity\Author;
use RuntimeException;

use function file_get_contents;
use function json_decode;
use function preg_replace;
use function strtolower;
use function trim;

class AuthorLoader extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/articles_cleaned.json';
        $contents = file_get_contents($jsonFile);

        if ($contents === false) {
            throw new RuntimeException("Unable to read file: {$jsonFile}");
        }

        $categories = json_decode($contents, true);

        $seenAuthorIds = [];

        foreach ($categories as $cat) {
            foreach ($cat['articles'] as $article) {
                $authorData = $article['author'] ?? null;
                if (! $authorData) {
                    continue;
                }

                $wpAuthorId = $authorData['user_email'];
                if (isset($seenAuthorIds[$wpAuthorId])) {
                    continue;
                }
                $seenAuthorIds[$wpAuthorId] = true;

                $name  = $authorData['display_name'];
                $email = $authorData['user_email'];
                $slug  = $this->slugify($name);

                $author = new Author();
                $author->setName($name);
                $author->setSlug($slug);
                $author->setEmail($email);
                $author->setBio(null);

                $manager->persist($author);
                $this->addReference('author_' . $wpAuthorId, $author);
            }
        }

        $manager->flush();
    }

    private function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
        return trim($text, '-');
    }

    public function getOrder(): int
    {
        return 1;
    }
}
