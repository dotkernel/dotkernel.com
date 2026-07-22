<?php

declare(strict_types=1);

namespace Light\Blog\Entity;

use Doctrine\ORM\Mapping as ORM;
use Light\App\Entity\AbstractEntity;
use Light\Blog\Repository\AuthorRepository;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
#[ORM\Table(name: 'author')]
#[ORM\HasLifecycleCallbacks]
class Author extends AbstractEntity
{
    #[ORM\Column(name: 'name', type: 'text')]
    private string $name;

    #[ORM\Column(name: 'slug', type: 'text', unique: true)]
    private string $slug;

    #[ORM\Column(name: 'github', type: 'text', unique: true, nullable: true)]
    private ?string $github = null;

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

    public function getGithub(): ?string
    {
        return $this->github;
    }

    public function setGithub(?string $github): void
    {
        $this->github = $github;
    }

    /**
     * @return array{
     *     id: non-empty-string,
     *     name: string,
     *     slug: string,
     *     github: string|null
     * }
     */
    public function getArrayCopy(): array
    {
        return [
            'id'     => $this->id->toString(),
            'name'   => $this->name,
            'slug'   => $this->slug,
            'github' => $this->github,
        ];
    }
}
