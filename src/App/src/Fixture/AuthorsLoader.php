<?php

declare(strict_types=1);

namespace Light\App\Fixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Light\Blog\Entity\Author;

class AuthorsLoader implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $authors = [
            [
                'name' => 'Author Name',
                'slug' => 'author-name',
                'bio'  => 'PHP developer and open source contributor at Dotkernel.',
            ],
        ];

        foreach ($authors as $data) {
            $author = new Author();
            $author->setName($data['name']);
            $author->setSlug($data['slug']);
            $author->setBio($data['bio']);
            $manager->persist($author);
        }

        $manager->flush();
    }
}
