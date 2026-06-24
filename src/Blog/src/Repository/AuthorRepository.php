<?php

declare(strict_types=1);

namespace Light\Blog\Repository;

use Light\App\Repository\AbstractRepository;
use Light\Blog\Entity\Author;

class AuthorRepository extends AbstractRepository
{
    /**
     * @return array<Author>
     */
    public function getAuthor(): array
    {
        $qb = $this->getQueryBuilder()
            ->select('author.name, author.slug')
            ->from(Author::class, 'authors');

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
