<?php

declare(strict_types=1);

namespace Light\Blog\Entity;

use Doctrine\ORM\Mapping as ORM;
use Light\App\Entity\AbstractEntity;
use Light\Blog\Repository\CategoryRepository;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'categories')]
#[ORM\HasLifecycleCallbacks]
class Category extends AbstractEntity
{
    #[ORM\Column(name: 'name', type: 'string', length: 500)]
    private string $name;

    #[ORM\Column(name: 'slug', type: 'string', length: 500)]
    private string $slug;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return array{
     *     id: non-empty-string,
     *     name: string,
     *     slug: string
     * }
     */
    public function getArrayCopy(): array
    {
        return [
            'id'   => $this->id->toString(),
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }
}
