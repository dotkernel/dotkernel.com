<?php

declare(strict_types=1);

namespace LightTest\Unit\Blog\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Light\Blog\DBAL\Types\PostStatusEnumType;
use Light\Blog\Enum\PostStatusEnum;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;

class PostStatusEnumTypeTest extends UnitTest
{
    private PostStatusEnumType $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->type = new PostStatusEnumType();
    }

    public function testGetNameReturnsTheTypeConstant(): void
    {
        $this->assertSame('post_status_enum', $this->type->getName());
        $this->assertSame(PostStatusEnumType::NAME, $this->type->getName());
    }

    public function testGetEnumClassReturnsTheBackingEnum(): void
    {
        $this->assertSame(PostStatusEnum::class, $this->type->getEnumClass());
    }

    public function testGetEnumCasesReturnsEveryCaseOfTheBackingEnum(): void
    {
        $this->assertSame(PostStatusEnum::cases(), $this->type->getEnumCases());
    }

    public function testGetEnumValuesReturnsEveryCaseValue(): void
    {
        $this->assertSame(['draft', 'published', 'private', 'archived'], $this->type->getEnumValues());
    }

    /**
     * PostgreSQL declares a named enum type of its own, so the column reuses that name.
     *
     * @throws Exception
     */
    public function testGetSqlDeclarationUsesTheTypeNameOnPostgres(): void
    {
        $this->assertSame(
            'post_status_enum',
            $this->type->getSQLDeclaration([], $this->createStub(PostgreSQLPlatform::class))
        );
    }

    /**
     * SQLite has no enum type - the test suite runs on it, so this branch matters.
     *
     * @throws Exception
     */
    public function testGetSqlDeclarationFallsBackToTextOnSqlite(): void
    {
        $this->assertSame(
            'TEXT',
            $this->type->getSQLDeclaration([], $this->createStub(SQLitePlatform::class))
        );
    }

    /**
     * @throws Exception
     */
    public function testGetSqlDeclarationListsEveryValueOnPlatformsWithNativeEnums(): void
    {
        $this->assertSame(
            "ENUM('draft', 'published', 'private', 'archived')",
            $this->type->getSQLDeclaration([], $this->createStub(MariaDBPlatform::class))
        );
    }

    /**
     * @throws Exception
     */
    public function testConvertToDatabaseValueUnwrapsTheEnumToItsBackingValue(): void
    {
        $this->assertSame(
            'published',
            $this->type->convertToDatabaseValue(PostStatusEnum::Published, $this->platform())
        );
    }

    /**
     * @throws Exception
     */
    public function testConvertToPhpValueUnwrapsTheEnumToItsBackingValue(): void
    {
        $this->assertSame(
            'draft',
            $this->type->convertToPHPValue(PostStatusEnum::Draft, $this->platform())
        );
    }

    /**
     * Rows written before the type existed hold plain strings, and null is a valid column value.
     *
     * @throws Exception
     */
    public function testConvertPassesThroughValuesThatAreNotEnums(): void
    {
        $platform = $this->platform();

        $this->assertSame('published', $this->type->convertToDatabaseValue('published', $platform));
        $this->assertNull($this->type->convertToDatabaseValue(null, $platform));
        $this->assertSame('published', $this->type->convertToPHPValue('published', $platform));
        $this->assertNull($this->type->convertToPHPValue(null, $platform));
    }

    /**
     * @throws Exception
     */
    public function testGetValueReturnsTheBackingValueOfAnEnumAndLeavesAnythingElseAlone(): void
    {
        $this->assertSame('archived', $this->type->getValue(PostStatusEnum::Archived));
        $this->assertSame(42, $this->type->getValue(42));
    }

    /**
     * @throws Exception
     */
    private function platform(): AbstractPlatform
    {
        return $this->createStub(AbstractPlatform::class);
    }
}
