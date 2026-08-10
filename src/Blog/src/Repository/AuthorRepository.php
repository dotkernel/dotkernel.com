<?php

declare(strict_types=1);

namespace Light\Blog\Repository;

use Light\App\Repository\AbstractRepository;
use Light\Blog\Entity\Author;
use Light\Blog\Entity\Post;
use Light\Blog\Enum\PostStatusEnum;

class AuthorRepository extends AbstractRepository
{
    /**
     * @return array<Author>
     */
    public function getAuthor(): array
    {
        $qb = $this->getQueryBuilder()
            ->select('author')
            ->from(Author::class, 'author');

        return $qb->getQuery()->getResult();
    }

    /**
     * Authors with at least one published post, i.e. authors whose page actually has content.
     *
     * @return array<Author>
     */
    public function getAuthorsWithPublishedPosts(): array
    {
        $publishedAuthorIds = $this->getQueryBuilder()
            ->select('publishedAuthor.id')
            ->from(Post::class, 'post')
            ->join('post.author', 'publishedAuthor')
            ->where('post.status = :published');

        $qb = $this->getQueryBuilder()
            ->select('author')
            ->from(Author::class, 'author');

        $qb->where($qb->expr()->in('author.id', $publishedAuthorIds->getDQL()))
            ->setParameter('published', PostStatusEnum::Published);

        return $qb->getQuery()->getResult();
    }

    public function getAuthorResource(string $slug): ?Author
    {
        $qb = $this->getQueryBuilder()
            ->select('author')
            ->from(Author::class, 'author')
            ->where('author.slug = :slug')
            ->setParameter('slug', $slug);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
