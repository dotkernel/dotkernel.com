<?php

declare(strict_types=1);

namespace Light\Blog\Entity;

use Doctrine\ORM\Mapping as ORM;
use Light\App\Entity\AbstractEntity;
use Light\Blog\Repository\PostTagRepository;

#[ORM\Entity(repositoryClass: PostTagRepository::class)]
#[ORM\Table(name: 'post_tag')]
#[ORM\UniqueConstraint(name: 'post_tag_unique', columns: ['post_id', 'tag_id'])]
#[ORM\HasLifecycleCallbacks]
class PostTag extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'postTags')]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'id', nullable: false)]
    private Post $post;

    #[ORM\ManyToOne(targetEntity: Tag::class, inversedBy: 'postTags')]
    #[ORM\JoinColumn(name: 'tag_id', referencedColumnName: 'id', nullable: false)]
    private Tag $tag;

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): void
    {
        $this->post = $post;
    }

    public function getTag(): Tag
    {
        return $this->tag;
    }

    public function setTag(Tag $tag): void
    {
        $this->tag = $tag;
    }

    /**
     * @return array{
     *     id: non-empty-string,
     *     post: array{id: non-empty-string, title: string, slug: string},
     *     tag: array{id: non-empty-string, name: string, slug: string}
     * }
     */
    public function getArrayCopy(): array
    {
        return [
            'id'   => $this->id->toString(),
            'post' => [
                'id'    => $this->post->getId()->toString(),
                'title' => $this->post->getTitle(),
                'slug'  => $this->post->getSlug(),
            ],
            'tag'  => $this->tag->getArrayCopy(),
        ];
    }
}
