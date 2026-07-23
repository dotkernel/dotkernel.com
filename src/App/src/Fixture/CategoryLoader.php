<?php

declare(strict_types=1);

namespace Light\App\Fixture;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Light\Blog\Entity\Category;
use RuntimeException;

use function file_get_contents;
use function json_decode;

class CategoryLoader extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/articles_cleaned.json';
        $contents = file_get_contents($jsonFile);

        if ($contents === false) {
            throw new RuntimeException("Unable to read file: {$jsonFile}");
        }

        $categories = json_decode($contents, true);

        $repository = $manager->getRepository(Category::class);

        foreach ($categories as $cat) {
            $category = $repository->findOneBy(['slug' => $cat['slug']]);

            if ($category === null) {
                $category = new Category();
                $category->setSlug($cat['slug']);
                $manager->persist($category);
                echo "CREATE: {$cat['name']}\n";
            } else {
                echo "UNCHANGED: {$cat['name']}\n";
            }

            $category->setName($cat['name']);
            $category->setVisibility($cat['isVisible'] ?? true);

            $this->addReference('category_' . $cat['slug'], $category);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }
}
