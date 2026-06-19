<?php

declare(strict_types=1);

namespace Light\Blog\Entity;

use Doctrine\ORM\Mapping as ORM;
use Light\App\Entity\AbstractEntity;
use Light\Blog\Repository\CategoryRepository;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'category')]
#[ORM\HasLifecycleCallbacks]
class Category extends AbstractEntity
{
    #[ORM\Column(name: 'name', type: 'text')]
    private string $name;

    #[ORM\Column(name: 'slug', type: 'text')]
    private string $slug;

    #[ORM\Column(name: 'isVisible', type: 'boolean')]
    private bool $isVisible = true;

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

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function setVisibility(bool $isVisible): void
    {
        $this->isVisible = $isVisible;
    }

    /**
     * @return array{
     *     id: non-empty-string,
     *     name: string,
     *     isVisible: bool,
     *     slug: string
     * }
     */
    public function getArrayCopy(): array
    {
        return [
            'id'        => $this->id->toString(),
            'name'      => $this->name,
            'isVisible' => $this->isVisible,
            'slug'      => $this->slug,
        ];
    }
}
