<?php

declare(strict_types=1);

namespace Light\Blog\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Light\App\Entity\AbstractEntity;
use Light\Blog\Repository\TagRepository;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\Table(name: 'tag')]
#[ORM\HasLifecycleCallbacks]
class Tag extends AbstractEntity
{
    #[ORM\Column(name: 'name', type: 'string', length: 191)]
    private string $name;

    #[ORM\Column(name: 'slug', type: 'string', length: 191, unique: true)]
    private string $slug;

    /** @var Collection<int, PostTag> */
    #[ORM\OneToMany(mappedBy: 'tag', targetEntity: PostTag::class)]
    private Collection $postTags;

    public function __construct()
    {
        parent::__construct();

        $this->postTags = new ArrayCollection();
    }

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
     * @return Collection<int, PostTag>
     */
    public function getPostTags(): Collection
    {
        return $this->postTags;
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
