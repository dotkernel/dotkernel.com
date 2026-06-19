<?php

declare(strict_types=1);

namespace Light\App\Fixture;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Light\Blog\Entity\Category;

use function file_get_contents;
use function json_decode;

class CategoryLoader extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $jsonFile   = __DIR__ . '/articles_cleaned.json';
        $categories = json_decode(file_get_contents($jsonFile), true);

        foreach ($categories as $cat) {
            $category = new Category();
            $category->setName($cat['name']);
            $category->setSlug($cat['slug']);
            $category->setVisibility($cat['isVisible'] ?? true);

            $manager->persist($category);
            $this->addReference('category_' . $cat['slug'], $category);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }
}
