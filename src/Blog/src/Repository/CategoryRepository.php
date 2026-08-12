<?php

declare(strict_types=1);

namespace Light\Blog\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
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

    /**
     * @return array<Category>
     */
    public function getCategoriesWithPublishedPosts(): array
    {
        $publishedCategoryIds = $this->getQueryBuilder()
            ->select('publishedCategory.id')
            ->from(Post::class, 'post')
            ->join('post.category', 'publishedCategory')
            ->where('post.status = :published');

        $qb = $this->getQueryBuilder()
            ->select('categories')
            ->from(Category::class, 'categories');

        $qb->where($qb->expr()->in('categories.id', $publishedCategoryIds->getDQL()))
            ->setParameter('published', PostStatusEnum::Published);

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
     * @param array<string, mixed> $params
     * @return DoctrinePaginator<Post>
     */
    public function getCategoryPost(Category $category, array $params): DoctrinePaginator
    {
        $qb = $this->getQueryBuilder()
            ->select('articles')
            ->from(Post::class, 'articles')
            ->where('articles.category = :category')
            ->andWhere('articles.status = :published')
            ->setParameter('category', $category)
            ->setParameter('published', PostStatusEnum::Published)
            ->orderBy('articles.postDate', $params['dir'])
            ->setFirstResult($params['offset'])
            ->setMaxResults($params['limit']);

        return new DoctrinePaginator($qb->getQuery());
    }
}
