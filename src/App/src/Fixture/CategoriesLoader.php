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
            ['name' => 'Architecture',        'slug' => 'architecture'],
            ['name' => 'Best Practice',       'slug' => 'best-practice'],
            ['name' => 'Dotkernel',           'slug' => 'dotkernel'],
            ['name' => 'Dotkernel API',       'slug' => 'dotkernel-api'],
            ['name' => 'Middleware',          'slug' => 'middleware'],
            ['name' => 'Headless Platform',   'slug' => 'headless-platform'],
            ['name' => 'How to',           'slug' => 'how-to'],
            ['name' => 'Design Patterns',     'slug' => 'design-pattern'],
            ['name' => 'PHP Development',     'slug' => 'php-development'],
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