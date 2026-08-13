<?php

declare(strict_types=1);

namespace LightTest\Unit\App\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Light\App\DBAL\Types\UuidType;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidTypeTest extends UnitTest
{
    private UuidType $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->type = new UuidType();
    }

    /**
     * The type is registered with Doctrine under this name.
     */
    public function testTheTypeNameIsUuid(): void
    {
        $this->assertSame('uuid', $this->type::NAME);
    }

    /**
     * Identifiers use the database's native `uuid` column rather than `binary`, on every platform
     * that reaches this type - which is why MySQL, having no such type, is unsupported.
     *
     * @throws Exception
     */
    public function testGetSqlDeclarationIsTheNativeUuidColumnRegardlessOfPlatform(): void
    {
        $this->assertSame('UUID', $this->type->getSQLDeclaration([], $this->createStub(PostgreSQLPlatform::class)));
        $this->assertSame('UUID', $this->type->getSQLDeclaration([], $this->createStub(MariaDBPlatform::class)));
    }

    /**
     * @throws Exception
     */
    public function testGetSqlDeclarationIgnoresTheColumnDefinition(): void
    {
        $this->assertSame(
            'UUID',
            $this->type->getSQLDeclaration(
                ['length' => 16, 'fixed' => true],
                $this->createStub(AbstractPlatform::class)
            )
        );
    }

    /**
     * @throws Exception
     */
    public function testConvertToDatabaseValueSerialisesTheUuidToItsStringForm(): void
    {
        $uuid = Uuid::fromString('1ef9f1a4-1a2b-6c3d-8e4f-5a6b7c8d9e0f');

        $this->assertSame(
            '1ef9f1a4-1a2b-6c3d-8e4f-5a6b7c8d9e0f',
            $this->type->convertToDatabaseValue($uuid, $this->createStub(AbstractPlatform::class))
        );
    }

    /**
     * @throws Exception
     */
    public function testConvertToPhpValueRebuildsTheUuidFromItsStringForm(): void
    {
        $value = $this->type->convertToPHPValue(
            '1ef9f1a4-1a2b-6c3d-8e4f-5a6b7c8d9e0f',
            $this->createStub(AbstractPlatform::class)
        );

        // Ramsey hands back a lazy implementation of the interface, not a Uuid instance.
        $this->assertInstanceOf(UuidInterface::class, $value);
        $this->assertSame('1ef9f1a4-1a2b-6c3d-8e4f-5a6b7c8d9e0f', $value->toString());
    }

    /**
     * @throws Exception
     */
    public function testConvertHandlesNullBothWays(): void
    {
        $platform = $this->createStub(AbstractPlatform::class);

        $this->assertNull($this->type->convertToDatabaseValue(null, $platform));
        $this->assertNull($this->type->convertToPHPValue(null, $platform));
    }
}
