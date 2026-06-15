<?php

declare(strict_types=1);

namespace Light\Blog\Entity;

use Doctrine\ORM\Mapping as ORM;
use Light\App\Entity\AbstractEntity;
use Light\Blog\Repository\AuthorRepository;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
#[ORM\Table(name: 'authors')]
#[ORM\HasLifecycleCallbacks]
class Author extends AbstractEntity
{
    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(name: 'slug', type: 'string', length: 255, unique: true)]
    private string $slug;

    #[ORM\Column(name: 'bio', type: 'text', nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(name: 'email', type: 'string', length: 255, unique: true)]
    private string $email;

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

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): void
    {
        $this->bio = $bio;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return array{
     *     id: non-empty-string,
     *     name: string,
     *     slug: string,
     *     email: string,
     *     bio: string|null
     * }
     */
    public function getArrayCopy(): array
    {
        return [
            'id'   => $this->id->toString(),
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'bio'  => $this->bio,
        ];
    }
}
