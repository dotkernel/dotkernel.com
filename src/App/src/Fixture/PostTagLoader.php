<?php

declare(strict_types=1);

namespace Light\App\Fixture;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Light\Blog\Entity\Post;
use Light\Blog\Entity\PostTag;
use Light\Blog\Entity\Tag;
use RuntimeException;

use function file_get_contents;
use function in_array;
use function json_decode;

class PostTagLoader extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/articles_cleaned.json';
        $contents = file_get_contents($jsonFile);

        if ($contents === false) {
            throw new RuntimeException("Unable to read file: {$jsonFile}");
        }

        $categories = json_decode($contents, true);

        $repository = $manager->getRepository(PostTag::class);
        $postIndex  = 0;

        foreach ($categories as $cat) {
            foreach ($cat['articles'] as $articleData) {
                $postIndex++;

                if (! $this->hasReference('post_' . $postIndex, Post::class)) {
                    continue;
                }

                /** @var Post $post */
                $post = $this->getReference('post_' . $postIndex, Post::class);

                $currentTagSlugs = [];

                foreach ($articleData['tags'] ?? [] as $tagData) {
                    $currentTagSlugs[] = $tagData['slug'];

                    /** @var Tag $tag */
                    $tag = $this->getReference('tag_' . $tagData['slug'], Tag::class);

                    $postTag = $repository->findOneBy(['post' => $post, 'tag' => $tag]);

                    if ($postTag === null) {
                        $postTag = new PostTag();
                        $postTag->setPost($post);
                        $postTag->setTag($tag);
                        $manager->persist($postTag);
                        echo "CREATE: {$post->getTitle()} - {$tag->getName()}\n";
                    } else {
                        echo "UNCHANGED: {$post->getTitle()} - {$tag->getName()}\n";
                    }
                }

                foreach ($repository->findBy(['post' => $post]) as $existingPostTag) {
                    if (in_array($existingPostTag->getTag()->getSlug(), $currentTagSlugs, true)) {
                        continue;
                    }

                    echo "REMOVE: {$post->getTitle()} - {$existingPostTag->getTag()->getName()}\n";
                    $manager->remove($existingPostTag);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TagLoader::class,
            PostLoader::class,
        ];
    }

    public function getOrder(): int
    {
        return 4;
    }
}
