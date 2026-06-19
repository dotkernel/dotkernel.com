<?php

declare(strict_types=1);

namespace Light\Blog\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Light\App\Repository\AbstractRepository;
use Light\Blog\Entity\Post;
use Light\Blog\Enum\PostStatusEnum;

class PostRepository extends AbstractRepository
{
    /**
     * @return DoctrinePaginator<Post>
     */
    public function getArticles(array $params): DoctrinePaginator
    {
        $qb = $this->getQueryBuilder()
            ->select('articles')
            ->from(Post::class, 'articles')
            ->leftJoin('articles.category', 'category')
            ->where('articles.status = :published')
            ->setParameter('published', PostStatusEnum::Published)
            ->orderBy($params['sort'], $params['dir'])
            ->setFirstResult($params['offset'])
            ->setMaxResults($params['limit']);

        return new DoctrinePaginator($qb->getQuery());
    }

    public function getArticleResource(string $slug): ?Post
    {
        $qb = $this->getQueryBuilder()
            ->select('articles')
            ->from(Post::class, 'articles')
            ->where('articles.slug = :slug')
            ->andWhere('articles.status = :published')
            ->setParameter('slug', $slug)
            ->setParameter('published', PostStatusEnum::Published);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
