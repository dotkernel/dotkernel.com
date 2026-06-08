<?php

namespace Light\Blog\Repository;

use Light\App\Repository\AbstractRepository;
use Light\Blog\Entity\Author;
class AuthorRepository extends AbstractRepository
{
    public function getCategories(): array
    {
        $qb = $this->getQueryBuilder()
            ->select('authors.name, authors.slug')
            ->from(Author::class, 'authors');

        return $qb->getQuery()->getResult();
    }

}