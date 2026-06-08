<?php

declare(strict_types=1);

namespace Light\Blog\Repository;

use Laminas\Diactoros\Response\EmptyResponse;
use Light\App\Repository\AbstractRepository;
use Light\Blog\Entity\Article;
use Light\Blog\Entity\Category;

class CategoryRepository extends AbstractRepository
{
    /**
     * @return array<Category>
     */
    public function getCategories(): array
    {
        $qb = $this->getQueryBuilder()
            ->select('categories.name, categories.slug')
            ->from(Category::class, 'categories');

        return $qb->getQuery()->getResult();
    }

    public function getCategoryResource(string $slug): ?Category
    {
        $qb = $this->getQueryBuilder()
            ->select('categories')
            ->from(Category::class, 'categories')
            ->where('categories.slug = :slug')
            ->setParameter('slug', $slug);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return array<Article>
     */
    public function getCategoryArticles(Category $category): array
    {
        $qb = $this->getQueryBuilder()
            ->select('articles')
            ->from(Article::class, 'articles')
            ->where('articles.category = :category')
            ->setParameter('category', $category);

        return $qb->getQuery()->getResult();
    }
}
