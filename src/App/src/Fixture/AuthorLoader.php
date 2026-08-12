<?php

declare(strict_types=1);

namespace Light\App\Fixture;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Light\Blog\Entity\Author;
use Light\Blog\Entity\Post;
use RuntimeException;

use function file_get_contents;
use function html_entity_decode;
use function json_decode;
use function preg_replace;
use function strtolower;
use function trim;

use const ENT_QUOTES;

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

        $authorRepository = $manager->getRepository(Author::class);
        $postRepository   = $manager->getRepository(Post::class);
        $seenAuthorIds    = [];

        foreach ($categories as $cat) {
            foreach ($cat['articles'] as $article) {
                $authorData = $article['author'] ?? null;
                if (! $authorData) {
                    continue;
                }

                $name   = $authorData['display_name'];
                $github = $authorData['github'] ?: null;

                $identityKey = $github ?? ('name:' . $name);
                if (isset($seenAuthorIds[$identityKey])) {
                    continue;
                }
                $seenAuthorIds[$identityKey] = true;

                $slug = $this->slugify($name);

                $postTitle    = html_entity_decode($article['post_title'] ?? '', ENT_QUOTES, 'UTF-8');
                $existingPost = $postTitle !== ''
                    ? $postRepository->findOneBy(['slug' => $this->slugify($postTitle)])
                    : null;

                $author = $existingPost?->getAuthor()
                    ?? ($github !== null
                        ? $authorRepository->findOneBy(['github' => $github])
                        : $authorRepository->findOneBy(['name' => $name]));

                if ($author === null) {
                    $author = new Author();
                    $author->setName($name);
                    $author->setSlug($slug);
                    $author->setGithub($github);
                    $manager->persist($author);
                    echo "CREATE: {$name}\n";
                } else {
                    $changed = false;
                    if ($author->getName() !== $name) {
                        $author->setName($name);
                        $changed = true;
                    }
                    if ($author->getSlug() !== $slug) {
                        $author->setSlug($slug);
                        $changed = true;
                    }
                    if ($author->getGithub() !== $github) {
                        $author->setGithub($github);
                        $changed = true;
                    }

                    echo $changed ? "UPDATE: {$name}\n" : "UNCHANGED: {$name}\n";
                }

                $this->addReference('author_' . $slug, $author);
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
