<?php

declare(strict_types=1);

namespace Light\Blog\Repository;

use Light\App\Repository\AbstractRepository;
use Light\Blog\Entity\Category;
use Light\Blog\Entity\Post;
use Light\Blog\Enum\PostStatusEnum;

class CategoryRepository extends AbstractRepository
{
    /**
     * @return array<Category>
     */
    public function getCategories(): array
    {
        $qb = $this->getQueryBuilder()
            ->select('categories')
            ->from(Category::class, 'categories')
            ->where('categories.isVisible = :visible')
            ->setParameter('visible', true);

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
     * @return array<Post>
     */
    public function getCategoryPost(Category $category): array
    {
        $qb = $this->getQueryBuilder()
            ->select('articles')
            ->from(Post::class, 'articles')
            ->where('articles.category = :category')
            ->andWhere('articles.status = :published')
            ->orderBy('articles.postDate', 'DESC')
            ->setParameter('published', PostStatusEnum::Published)
            ->setParameter('category', $category);

        return $qb->getQuery()->getResult();
    }
}
