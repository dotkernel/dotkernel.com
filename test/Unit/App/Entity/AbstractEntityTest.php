<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Entity;

use DateTimeImmutable;
use Light\App\Entity\AbstractEntity;
use Light\App\Entity\EntityInterface;
use LightTest\Unit\UnitTest;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AbstractEntityTest extends UnitTest
{
    public function testWillInstantiate(): void
    {
        $entity = new class extends AbstractEntity {
            public function getId(): UuidInterface
            {
                return Uuid::uuid7();
            }

            /**
             * @return array<string, mixed>
             */
            public function getArrayCopy(): array
            {
                return [];
            }

            public function getCreated(): ?DateTimeImmutable
            {
                return null;
            }

            public function getCreatedFormatted(string $dateFormat = 'Y-m-d H:i:s'): string
            {
                return '';
            }

            public function getUpdated(): ?DateTimeImmutable
            {
                return null;
            }

            public function getUpdatedFormatted(string $dateFormat = 'Y-m-d H:i:s'): ?string
            {
                return null;
            }
        };

        $this->assertContainsOnlyInstancesOf(EntityInterface::class, [$entity]);
        $this->assertContainsOnlyInstancesOf(UuidInterface::class, [$entity->getId()]);
    }
}
