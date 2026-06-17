<?php

declare(strict_types=1);

namespace Light\Blog\Repository;

use Light\App\Repository\AbstractRepository;
use Light\Blog\Entity\Article;
use Light\Blog\Entity\Category;
use Light\Blog\Enum\ArticleStatusEnum;

class CategoryRepository extends AbstractRepository
{
    /**
     * @return array<Category>
     */
    public function getCategories(): array
    {
        $qb = $this->getQueryBuilder()
            ->select('categories.name, categories.slug, categories.isVisible')
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
            ->andWhere('articles.status = :published')
            ->setParameter('published', ArticleStatusEnum::Published)
            ->setParameter('category', $category);

        return $qb->getQuery()->getResult();
    }
}
