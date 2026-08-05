<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Light\App\Entity\EntityInterface;
use Light\App\Repository\AbstractRepository;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;

class AbstractRepositoryTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testGetQueryBuilderReturnsTheEntityManagersQueryBuilder(): void
    {
        $queryBuilder  = $this->createStub(QueryBuilder::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())->method('createQueryBuilder')->willReturn($queryBuilder);

        $repository = $this->createRepository($entityManager);

        $this->assertSame($queryBuilder, $repository->getQueryBuilder());
    }

    /**
     * @throws Exception
     */
    public function testSaveResourcePersistsThenFlushes(): void
    {
        $resource      = $this->createStub(EntityInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $calls = [];
        $entityManager->expects($this->once())->method('persist')
            ->willReturnCallback(function (object $entity) use (&$calls, $resource): void {
                $this->assertSame($resource, $entity);
                $calls[] = 'persist';
            });
        $entityManager->expects($this->once())->method('flush')
            ->willReturnCallback(function () use (&$calls): void {
                $calls[] = 'flush';
            });

        $this->createRepository($entityManager)->saveResource($resource);

        $this->assertSame(['persist', 'flush'], $calls);
    }

    /**
     * @throws Exception
     */
    public function testDeleteResourceRemovesThenFlushes(): void
    {
        $resource      = $this->createStub(EntityInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $calls = [];
        $entityManager->expects($this->once())->method('remove')
            ->willReturnCallback(function (object $entity) use (&$calls, $resource): void {
                $this->assertSame($resource, $entity);
                $calls[] = 'remove';
            });
        $entityManager->expects($this->once())->method('flush')
            ->willReturnCallback(function () use (&$calls): void {
                $calls[] = 'flush';
            });

        $this->createRepository($entityManager)->deleteResource($resource);

        $this->assertSame(['remove', 'flush'], $calls);
    }

    /**
     * @throws Exception
     */
    private function createRepository(EntityManagerInterface $entityManager): AbstractRepository
    {
        return new AbstractRepository($entityManager, new ClassMetadata(EntityInterface::class));
    }
}
