<?php

declare(strict_types=1);

namespace Light\App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Light\App\Entity\EntityInterface;

/**
 * @extends EntityRepository<object>
 */
class AbstractRepository extends EntityRepository
{
    public function deleteResource(EntityInterface $resource): void
    {
        $this->getEntityManager()->remove($resource);
        $this->getEntityManager()->flush();
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->getEntityManager()->createQueryBuilder();
    }

    public function saveResource(EntityInterface $resource): void
    {
        $this->getEntityManager()->persist($resource);
        $this->getEntityManager()->flush();
    }
}
