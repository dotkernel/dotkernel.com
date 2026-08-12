<?php

declare(strict_types=1);

namespace Light\Blog\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Light\App\Repository\AbstractRepository;
use Light\Blog\Entity\Post;
use Light\Blog\Entity\Tag;
use Light\Blog\Enum\PostStatusEnum;

class TagRepository extends AbstractRepository
{
    /**
     * @return array<Tag>
     */
    public function getTags(): array
    {
        $qb = $this->getQueryBuilder()
            ->select('tags')
            ->from(Tag::class, 'tags')
            ->orderBy('tags.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function getTagResource(string $slug): ?Tag
    {
        $qb = $this->getQueryBuilder()
            ->select('tags')
            ->from(Tag::class, 'tags')
            ->where('tags.slug = :slug')
            ->setParameter('slug', $slug);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array<string, mixed> $params
     * @return DoctrinePaginator<Post>
     */
    public function getTagPost(Tag $tag, array $params): DoctrinePaginator
    {
        $qb = $this->getQueryBuilder()
            ->select('articles')
            ->from(Post::class, 'articles')
            ->innerJoin('articles.postTags', 'postTags')
            ->where('postTags.tag = :tag')
            ->andWhere('articles.status = :published')
            ->setParameter('tag', $tag)
            ->setParameter('published', PostStatusEnum::Published)
            ->orderBy('articles.postDate', $params['dir'])
            ->setFirstResult($params['offset'])
            ->setMaxResults($params['limit']);

        return new DoctrinePaginator($qb->getQuery());
    }
}
