<?php

declare(strict_types=1);

namespace Light\App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Stdlib\ArraySerializableInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

use function is_array;
use function method_exists;
use function ucfirst;

#[ORM\MappedSuperclass]
abstract class AbstractEntity implements ArraySerializableInterface
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true, nullable: false)]
    protected UuidInterface $id;

    #[ORM\Column(name: 'created', type: 'datetime_immutable', nullable: false)]
    protected DateTimeImmutable $created;

    #[ORM\Column(name: 'updated', type: 'datetime_immutable', nullable: true)]
    protected ?DateTimeImmutable $updated = null;

    public function __construct()
    {
        $this->id = Uuid::uuid7();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getCreated(): ?DateTimeImmutable
    {
        return $this->created;
    }

    public function getCreatedFormatted(string $dateFormat = 'Y-m-d H:i:s'): string
    {
        return $this->created->format($dateFormat);
    }

    public function getUpdated(): ?DateTimeImmutable
    {
        return $this->updated;
    }

    public function getUpdatedFormatted(string $dateFormat = 'Y-m-d H:i:s'): ?string
    {
        if ($this->updated instanceof DateTimeImmutable) {
            return $this->updated->format($dateFormat);
        }

        return null;
    }

    #[ORM\PrePersist]
    public function created(): void
    {
        $this->created = new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function touch(): void
    {
        $this->updated = new DateTimeImmutable();
    }

    /**
     * @param array<non-empty-string, mixed> $array
     */
    public function exchangeArray(array $array): void
    {
        foreach ($array as $property => $values) {
            if (is_array($values)) {
                $method = 'add' . ucfirst($property);
                if (! method_exists($this, $method)) {
                    continue;
                }
                foreach ($values as $value) {
                    $this->$method($value);
                }
            } else {
                $method = 'set' . ucfirst($property);
                if (! method_exists($this, $method)) {
                    continue;
                }
                $this->$method($values);
            }
        }
    }
}
