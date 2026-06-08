<?php

declare(strict_types=1);

namespace Light\Blog\Entity;

use Doctrine\ORM\Mapping as ORM;
use Light\App\Entity\AbstractEntity;
use Light\Blog\Repository\ArticleRepository;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: 'articles')]
#[ORM\HasLifecycleCallbacks]
class Article extends AbstractEntity
{
    #[ORM\Column(name: 'title', type: 'string', length: 500)]
    private string $title;

    #[ORM\Column(name: 'slug', type: 'string', length: 500, unique: true)]
    private string $slug;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: false)]
    private Category $category;

    #[ORM\ManyToOne(targetEntity: Author::class)]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id', nullable: false)]
    private Author $author;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function setAuthor(Author $author): void
    {
        $this->author = $author;
    }

    /**
     * @return array{
     *     id: non-empty-string,
     *     title: string,
     *     slug: string,
     *     category: array{id: non-empty-string, name: string, slug: string},
     *     author: array{id: non-empty-string, name: string, slug: string, bio: string|null}
     * }
     */
    public function getArrayCopy(): array
    {
        return [
            'id'       => $this->id->toString(),
            'title'    => $this->title,
            'slug'     => $this->slug,
            'category' => $this->category->getArrayCopy(),
            'author'   => $this->author->getArrayCopy(),
        ];
    }
}
