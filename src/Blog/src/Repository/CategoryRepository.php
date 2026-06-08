<?php

declare(strict_types=1);

namespace Light\Blog\Repository;

use Light\App\Repository\AbstractRepository;
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
}
