<?php

declare(strict_types=1);

namespace Light\Blog\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Light\App\Repository\AbstractRepository;
use Light\Blog\Entity\Author;
use Light\Blog\Entity\Post;
use Light\Blog\Enum\PostStatusEnum;

class PostRepository extends AbstractRepository
{
    /**
     * @param array<string, mixed> $params
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
            ->orderBy('articles.postDate', $params['dir'])
            ->setFirstResult($params['offset'])
            ->setMaxResults($params['limit']);

        return new DoctrinePaginator($qb->getQuery());
    }

    public function getArticleResource(string $slug, ?string $categorySlug = null): ?Post
    {
        $qb = $this->getQueryBuilder()
            ->select('articles')
            ->from(Post::class, 'articles')
            ->where('articles.slug = :slug')
            ->andWhere('articles.status = :published')
            ->setParameter('slug', $slug)
            ->setParameter('published', PostStatusEnum::Published);

        if ($categorySlug !== null) {
            $qb->leftJoin('articles.category', 'category')
                ->andWhere('category.slug = :categorySlug')
                ->setParameter('categorySlug', $categorySlug);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array<string, mixed> $params
     * @return DoctrinePaginator<Post>
     */
    public function getArticleByAuthor(Author $author, array $params): DoctrinePaginator
    {
        $qb = $this->getQueryBuilder()
            ->select('posts')
            ->from(Post::class, 'posts')
            ->where('posts.author = :author')
            ->setParameter('author', $author)
            ->orderBy('posts.postDate', $params['dir'])
            ->setFirstResult($params['offset'])
            ->setMaxResults($params['limit']);

        return new DoctrinePaginator($qb->getQuery());
    }

    /**
     * @return array{previous: Post|null, next: Post|null}
     */
    public function getAdjacentPosts(Post $post): array
    {
        $previous = $this->getQueryBuilder()
            ->select('articles')
            ->from(Post::class, 'articles')
            ->where('articles.status = :published')
            ->andWhere('articles.postDate < :postDate')
            ->setParameter('published', PostStatusEnum::Published)
            ->setParameter('postDate', $post->getPostDate())
            ->orderBy('articles.postDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $next = $this->getQueryBuilder()
            ->select('articles')
            ->from(Post::class, 'articles')
            ->where('articles.status = :published')
            ->andWhere('articles.postDate > :postDate')
            ->setParameter('published', PostStatusEnum::Published)
            ->setParameter('postDate', $post->getPostDate())
            ->orderBy('articles.postDate', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return ['previous' => $previous, 'next' => $next];
    }

    /**
     * @return array<int, Post>
     */
    public function getRecentPosts(int $limit = 5): array
    {
        $qb = $this->getQueryBuilder()
            ->select('articles')
            ->from(Post::class, 'articles')
            ->where('articles.status = :published')
            ->setParameter('published', PostStatusEnum::Published)
            ->orderBy('articles.postDate', 'DESC')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
