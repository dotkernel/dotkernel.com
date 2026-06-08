<?php

declare(strict_types=1);

namespace Light\App\Fixture;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Light\Blog\Entity\Article;
use Light\Blog\Entity\Author;
use Light\Blog\Entity\Category;
use function assert;

class ArticlesLoader implements FixtureInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $author = $manager->getRepository(Author::class)->findOneBy(['name' => 'Author Name']);
        assert($author instanceof Author);

        $category = $manager->getRepository(Category::class)->findOneBy(['name' => 'Dotkernel']);
        assert($category instanceof Category);

        $article = new Article();
        $article->setTitle('Test Article');
        $article->setSlug('test-article');
        $article->setAuthor($author);
        $article->setCategory($category);

        $manager->persist($article);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AuthorsLoader::class,
            CategoriesLoader::class,
        ];
    }
}