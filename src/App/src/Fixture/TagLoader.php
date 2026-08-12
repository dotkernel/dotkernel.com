<?php

declare(strict_types=1);

namespace Light\App\Fixture;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Light\Blog\Entity\Tag;
use RuntimeException;

use function file_get_contents;
use function json_decode;

class TagLoader extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/articles_cleaned.json';
        $contents = file_get_contents($jsonFile);

        if ($contents === false) {
            throw new RuntimeException("Unable to read file: {$jsonFile}");
        }

        $categories = json_decode($contents, true);

        $repository = $manager->getRepository(Tag::class);
        $seenSlugs  = [];

        foreach ($categories as $cat) {
            foreach ($cat['articles'] as $article) {
                foreach ($article['tags'] ?? [] as $tagData) {
                    $slug = $tagData['slug'];
                    $name = $tagData['name'];

                    if (isset($seenSlugs[$slug])) {
                        continue;
                    }
                    $seenSlugs[$slug] = true;

                    $tag = $repository->findOneBy(['slug' => $slug]);

                    if ($tag === null) {
                        $tag = new Tag();
                        $tag->setSlug($slug);
                        $tag->setName($name);
                        $manager->persist($tag);
                        echo "CREATE: {$name}\n";
                    } else {
                        $changed = false;
                        if ($tag->getName() !== $name) {
                            $tag->setName($name);
                            $changed = true;
                        }

                        echo $changed ? "UPDATE: {$name}\n" : "UNCHANGED: {$name}\n";
                    }

                    $this->addReference('tag_' . $slug, $tag);
                }
            }
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 1;
    }
}
