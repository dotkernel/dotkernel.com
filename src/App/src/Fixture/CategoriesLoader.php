<?php

declare(strict_types=1);

namespace Light\App\Fixture;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Light\Blog\Entity\Category;

class CategoriesLoader extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['name' => 'Uncategorized', 'slug' => 'uncategorized'],
            ['name' => 'Dotkernel', 'slug' => 'dotkernel'],
            ['name' => 'Javascript', 'slug' => 'javascript'],
            ['name' => 'Zend Framework', 'slug' => 'zend-framework'],
            ['name' => 'PHP Development', 'slug' => 'php-development'],
            ['name' => 'How to', 'slug' => 'how-to'],
            ['name' => 'PHP Troubleshooting', 'slug' => 'php-troubleshooting'],
            ['name' => 'Dotkernel 3', 'slug' => 'dotkernel3'],
            ['name' => 'Middleware', 'slug' => 'middleware'],
            ['name' => 'Zend Expressive', 'slug' => 'zend-expressive'],
            ['name' => 'Best Practice', 'slug' => 'best-practice'],
            ['name' => 'Version Control', 'slug' => 'version-control'],
            ['name' => 'Android', 'slug' => 'android'],
            ['name' => 'ZCE Tips', 'slug' => 'zce-tips'],
            ['name' => 'Documentation News', 'slug' => 'documentation-news'],
            ['name' => 'Dotkernel API', 'slug' => 'dotkernel-api'],
            ['name' => 'Laminas', 'slug' => 'laminas'],
            ['name' => 'PHPStorm', 'slug' => 'phpstorm'],
            ['name' => 'Licensing', 'slug' => 'licensing'],
            ['name' => 'Doctrine', 'slug' => 'doctrine'],
            ['name' => 'Architecture', 'slug' => 'architecture'],
            ['name' => 'Design Patterns', 'slug' => 'design-pattern'],
            ['name' => 'Headless Platform', 'slug' => 'headless-platform'],
        ];

        foreach ($categories as $data) {
            $category = new Category();
            $category->setName($data['name']);
            $category->setSlug($data['slug']);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
