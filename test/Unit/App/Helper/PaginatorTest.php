<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Helper;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Light\App\Helper\Paginator;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;

class PaginatorTest extends UnitTest
{
    public function testGetParamsReturnsDefaultsWhenNoParamsGiven(): void
    {
        $this->assertSame([
            'offset' => 0,
            'limit'  => 10,
            'page'   => 1,
            'sort'   => 'title',
            'dir'    => 'desc',
        ], Paginator::getParams([], 'title'));
    }

    public function testGetParamsUsesTheGivenDefaultDir(): void
    {
        $this->assertSame('asc', Paginator::getParams([], 'title', 'asc')['dir']);
    }

    public function testGetParamsOverridesSortWhenProvided(): void
    {
        $this->assertSame('name', Paginator::getParams(['sort' => 'name'], 'title')['sort']);
    }

    public function testGetParamsIgnoresAnEmptySort(): void
    {
        $this->assertSame('title', Paginator::getParams(['sort' => ''], 'title')['sort']);
    }

    public function testGetParamsIgnoresANonStringSort(): void
    {
        $this->assertSame('title', Paginator::getParams(['sort' => 123], 'title')['sort']);
    }

    public function testGetParamsOverridesDirWhenValid(): void
    {
        $this->assertSame('asc', Paginator::getParams(['dir' => 'asc'], 'title')['dir']);
    }

    public function testGetParamsIgnoresAnInvalidDir(): void
    {
        $this->assertSame('desc', Paginator::getParams(['dir' => 'sideways'], 'title')['dir']);
    }

    public function testGetParamsWithAllSetsALargeLimitAndIgnoresPagingParams(): void
    {
        $result = Paginator::getParams(
            ['all' => '1', 'limit' => 5, 'offset' => 20, 'page' => 3],
            'title'
        );

        $this->assertSame([
            'offset' => 0,
            'limit'  => 1_000,
            'page'   => 1,
            'sort'   => 'title',
            'dir'    => 'desc',
        ], $result);
    }

    public function testGetParamsUsesTheGivenLimit(): void
    {
        $this->assertSame(25, Paginator::getParams(['limit' => 25], 'title')['limit']);
    }

    public function testGetParamsIgnoresANonPositiveLimit(): void
    {
        $this->assertSame(10, Paginator::getParams(['limit' => 0], 'title')['limit']);
        $this->assertSame(10, Paginator::getParams(['limit' => -5], 'title')['limit']);
    }

    public function testGetParamsComputesThePageFromAnEvenlyDivisibleOffset(): void
    {
        $result = Paginator::getParams(['limit' => 10, 'offset' => 20], 'title');

        $this->assertSame(20, $result['offset']);
        $this->assertSame(3, $result['page']);
    }

    /**
     * The implementation does not round the derived page, so an offset that is not a multiple
     * of the limit produces a fractional page.
     */
    public function testGetParamsProducesAFractionalPageForAnUnevenOffset(): void
    {
        $result = Paginator::getParams(['limit' => 10, 'offset' => 5], 'title');

        $this->assertSame(1.5, $result['page']);
    }

    public function testGetParamsIgnoresANonPositiveOffset(): void
    {
        $this->assertSame(0, Paginator::getParams(['offset' => -5], 'title')['offset']);
    }

    public function testGetParamsComputesTheOffsetFromThePage(): void
    {
        $result = Paginator::getParams(['limit' => 10, 'page' => 3], 'title');

        $this->assertSame(3, $result['page']);
        $this->assertSame(20, $result['offset']);
    }

    public function testGetParamsIgnoresANonPositivePage(): void
    {
        $this->assertSame(1, Paginator::getParams(['page' => 0], 'title')['page']);
    }

    public function testGetParamsPageOverridesAnOffsetGivenAlongsideIt(): void
    {
        $result = Paginator::getParams(['limit' => 10, 'offset' => 50, 'page' => 2], 'title');

        $this->assertSame(2, $result['page']);
        $this->assertSame(10, $result['offset']);
    }

    /**
     * @throws Exception
     */
    public function testWrapperAddsCountItemsAndFilters(): void
    {
        $paginator = $this->createPaginator(5, ['a', 'b']);

        $result = Paginator::wrapper(
            $paginator,
            ['offset' => 0, 'limit' => 10, 'page' => 1, 'sort' => 'title', 'dir' => 'desc'],
            ['status' => 'active']
        );

        $this->assertSame(5, $result['count']);
        $this->assertSame(['a', 'b'], $result['items']);
        $this->assertSame(['status' => 'active'], $result['filters']);
    }

    /**
     * @throws Exception
     */
    public function testWrapperComputesPaginationMetadataForAMiddlePage(): void
    {
        $paginator = $this->createPaginator(25, []);

        $result = Paginator::wrapper(
            $paginator,
            ['offset' => 10, 'limit' => 10, 'page' => 2, 'sort' => 'title', 'dir' => 'desc']
        );

        $this->assertSame(2, $result['currentPage']);
        $this->assertSame(1, $result['firstPage']);
        $this->assertSame(1, $result['previousPage']);
        $this->assertSame(3, $result['lastPage']);
        $this->assertFalse($result['isOutOfBounds']);
        $this->assertSame(3, $result['nextPage']);
        $this->assertFalse($result['isFirstPage']);
        $this->assertFalse($result['isLastPage']);
        $this->assertTrue($result['hasPreviousPage']);
        $this->assertTrue($result['hasNextPage']);
        $this->assertSame([1, 2, 3], $result['pages']);
    }

    /**
     * @throws Exception
     */
    public function testWrapperMarksOutOfBoundsWhenCurrentPageExceedsLastPage(): void
    {
        $paginator = $this->createPaginator(5, []);

        $result = Paginator::wrapper(
            $paginator,
            ['offset' => 100, 'limit' => 10, 'page' => 11, 'sort' => 'title', 'dir' => 'desc']
        );

        $this->assertTrue($result['isOutOfBounds']);
        $this->assertTrue($result['isLastPage']);
        $this->assertFalse($result['hasNextPage']);
        $this->assertSame(1, $result['lastPage']);
        $this->assertSame(1, $result['previousPage']);
        $this->assertSame([1], $result['pages']);
        $this->assertSame(0, $result['previousOffset']);
    }

    /**
     * @throws Exception
     */
    public function testWrapperHandlesZeroResults(): void
    {
        $paginator = $this->createPaginator(0, []);

        $result = Paginator::wrapper(
            $paginator,
            ['offset' => 0, 'limit' => 10, 'page' => 1, 'sort' => 'title', 'dir' => 'desc']
        );

        $this->assertSame(1, $result['lastPage']);
        $this->assertTrue($result['isLastPage']);
        $this->assertFalse($result['hasNextPage']);
    }

    /**
     * @param list<mixed> $items
     * @return DoctrinePaginator<mixed>
     * @throws Exception
     */
    private function createPaginator(int $count, array $items): DoctrinePaginator
    {
        $query = $this->createStub(Query::class);
        $query->method('getResult')->willReturn($items);

        $paginator = $this->createStub(DoctrinePaginator::class);
        $paginator->method('count')->willReturn($count);
        $paginator->method('getQuery')->willReturn($query);

        return $paginator;
    }
}
