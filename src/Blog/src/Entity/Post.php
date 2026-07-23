<?php

declare(strict_types=1);

namespace Light\Blog\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Light\App\Entity\AbstractEntity;
use Light\Blog\Enum\PostStatusEnum;
use Light\Blog\Repository\PostRepository;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table(name: 'post')]
#[ORM\HasLifecycleCallbacks]
class Post extends AbstractEntity
{
    #[ORM\Column(name: 'title', type: 'text')]
    private string $title;

    #[ORM\Column(name: 'slug', type: 'text', unique: true)]
    private string $slug;

    #[ORM\Column(name: 'post_date', type: 'datetime_immutable')]
    private DateTimeImmutable $postDate;

    #[ORM\Column(name: 'excerpt', type: 'text')]
    private string $excerpt;

    #[ORM\Column(
        name: 'status',
        type: 'post_status_enum',
        enumType: PostStatusEnum::class,
        options: ['default' => PostStatusEnum::Draft]
    )]
    private PostStatusEnum $status = PostStatusEnum::Draft;

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

    public function getPostDate(): DateTimeImmutable
    {
        return $this->postDate;
    }

    public function setPostDate(DateTimeImmutable $postDate): void
    {
        $this->postDate = $postDate;
    }

    public function getStatus(): PostStatusEnum
    {
        return $this->status;
    }

    public function setStatus(PostStatusEnum $status): void
    {
        $this->status = $status;
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

    public function getExcerpt(): string
    {
        return $this->excerpt;
    }

    public function setExcerpt(string $excerpt): void
    {
        $this->excerpt = $excerpt;
    }

    /**
     * @return array{
     *     id: non-empty-string,
     *     title: string,
     *     slug: string,
     *     status: string,
     *     excerpt: string,
     *     postDate: string,
     *     category: array{id: non-empty-string, name: string, slug: string},
     *     author: array{id: non-empty-string, name: string, slug: string, github: string|null}
     * }
     */
    public function getArrayCopy(): array
    {
        return [
            'id'       => $this->id->toString(),
            'title'    => $this->title,
            'slug'     => $this->slug,
            'status'   => $this->status->value,
            'excerpt'  => $this->excerpt,
            'postDate' => $this->postDate->format('Y-m-d H:i:s'),
            'category' => $this->category->getArrayCopy(),
            'author'   => $this->author->getArrayCopy(),
        ];
    }
}
