<?php

declare(strict_types=1);

namespace LightTest\Unit\Blog\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use Light\Blog\Entity\Category;
use Light\Blog\Enum\PostStatusEnum;
use Light\Blog\Repository\CategoryRepository;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;

class CategoryRepositoryTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testGetCategoriesWithPublishedPostsDoesNotFilterOnVisibility(): void
    {
        $categories = [new Category()];
        $query      = $this->createChainableQuery();
        $query->expects($this->once())->method('getResult')->willReturn($categories);

        $dql           = null;
        $entityManager = $this->createEntityManager(function (string $queryDql) use (&$dql, $query): Query {
            $dql = $queryDql;

            return $query;
        });

        $result = $this->createRepository($entityManager)->getCategoriesWithPublishedPosts();

        $this->assertSame($categories, $result);
        $this->assertIsString($dql);
        $this->assertStringNotContainsString('isVisible', $dql);
    }

    /**
     * Only published posts count: a category with just drafts, private or archived posts is not listed.
     *
     * @throws Exception
     */
    public function testGetCategoriesWithPublishedPostsOnlyConsidersPublishedPosts(): void
    {
        $query = $this->createChainableQuery();
        $query->expects($this->once())->method('getResult')->willReturn([]);

        $dql           = null;
        $entityManager = $this->createEntityManager(function (string $queryDql) use (&$dql, $query): Query {
            $dql = $queryDql;

            return $query;
        });

        $this->createRepository($entityManager)->getCategoriesWithPublishedPosts();

        $this->assertIsString($dql);
        $this->assertStringContainsString('post.status = :published', $dql);
        $this->assertStringContainsString('categories.id IN', $dql);
    }

    /**
     * @throws Exception
     */
    public function testGetCategoriesWithPublishedPostsBindsThePublishedStatus(): void
    {
        $query = $this->createChainableQuery();
        $query->method('getResult')->willReturn([]);
        $query->expects($this->once())->method('setParameters')->with($this->callback(
            function (ArrayCollection $parameters): bool {
                $parameter = $parameters->first();

                return $parameters->count() === 1
                    && $parameter instanceof Parameter
                    && $parameter->getName() === 'published'
                    && $parameter->getValue() === PostStatusEnum::Published;
            }
        ))->willReturnSelf();

        $entityManager = $this->createEntityManager(static fn (): Query => $query);

        $this->createRepository($entityManager)->getCategoriesWithPublishedPosts();
    }

    /**
     * @throws Exception
     */
    public function testGetCategoriesStillFiltersOnVisibility(): void
    {
        $query = $this->createChainableQuery();
        $query->expects($this->once())->method('getResult')->willReturn([]);

        $dql           = null;
        $entityManager = $this->createEntityManager(function (string $queryDql) use (&$dql, $query): Query {
            $dql = $queryDql;

            return $query;
        });

        $this->createRepository($entityManager)->getCategories();

        $this->assertIsString($dql);
        $this->assertStringContainsString('categories.isVisible = :visible', $dql);
    }

    /**
     * Entity manager whose query builders are real (so DQL is built for real) and whose
     * createQuery() hands the generated DQL to $onCreateQuery.
     *
     * @param callable(string): Query $onCreateQuery
     * @throws Exception
     */
    private function createEntityManager(callable $onCreateQuery): EntityManagerInterface
    {
        $entityManager = $this->createStub(EntityManagerInterface::class);
        $entityManager->method('createQueryBuilder')
            ->willReturnCallback(static fn (): QueryBuilder => new QueryBuilder($entityManager));
        $entityManager->method('getExpressionBuilder')->willReturn(new Expr());
        $entityManager->method('createQuery')
            ->willReturnCallback(static fn (string $dql = ''): Query => $onCreateQuery($dql));

        return $entityManager;
    }

    /**
     * @throws Exception
     */
    private function createChainableQuery(): Query&MockObject
    {
        $query = $this->createMock(Query::class);
        $query->method('setParameters')->willReturnSelf();
        $query->method('setParameter')->willReturnSelf();
        $query->method('setFirstResult')->willReturnSelf();
        $query->method('setMaxResults')->willReturnSelf();

        return $query;
    }

    private function createRepository(EntityManagerInterface $entityManager): CategoryRepository
    {
        return new CategoryRepository($entityManager, new ClassMetadata(Category::class));
    }
}
