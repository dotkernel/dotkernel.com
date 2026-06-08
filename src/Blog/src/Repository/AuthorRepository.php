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
    public function getCategories(): array
    {
        $qb = $this->getQueryBuilder()
            ->select('authors.name, authors.slug')
            ->from(Author::class, 'authors');

        return $qb->getQuery()->getResult();
    }
}
