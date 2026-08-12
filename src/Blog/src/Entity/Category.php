<?php

declare(strict_types=1);

namespace Light\Blog\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Mapping as ORM;
use Light\App\Entity\AbstractEntity;
use Light\Blog\Enum\PostStatusEnum;
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

    /** @var Collection<int, Post>&Selectable<int, Post> */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Post::class)]
    private Collection $posts;

    public function __construct()
    {
        parent::__construct();

        $this->posts = new ArrayCollection();
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

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function setVisibility(bool $isVisible): void
    {
        $this->isVisible = $isVisible;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function getPublishedPostsCount(): int
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('status', PostStatusEnum::Published));

        return $this->posts->matching($criteria)->count();
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
            'postCount' => $this->posts->count(),
            'posts'     => $this->posts->map(fn (Post $post) => $post->getArrayCopy())->toArray(),
        ];
    }
}
