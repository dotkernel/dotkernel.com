<?php
declare(strict_types=1);

namespace Light\Blog\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Light\App\Helper\Paginator;
use Light\App\Repository\AbstractRepository;
use Light\Blog\Entity\Article;
use Light\Blog\Enum\ArticleStatusEnum;

class ArticleRepository extends AbstractRepository
{
    /**
     * @return DoctrinePaginator<Article>
     */
    public function getArticles(array $params): DoctrinePaginator
    {
        $qb = $this->getQueryBuilder()
            ->select('articles')
            ->from(Article::class, 'articles')
            ->leftJoin('articles.category', 'category')
            ->where('articles.status = :published')
            ->setParameter('published', ArticleStatusEnum::Published)
            ->orderBy($params['sort'], $params['dir'])
            ->setFirstResult($params['offset'])
            ->setMaxResults($params['limit']);

        return new DoctrinePaginator($qb->getQuery());
    }

    public function getArticleResource(string $slug): ?Article
    {
        $qb = $this->getQueryBuilder()
            ->select('articles')
            ->from(Article::class, 'articles')
            ->where('articles.slug = :slug')
            ->andWhere('articles.status = :published')
            ->setParameter('slug', $slug)
            ->setParameter('published', ArticleStatusEnum::Published);

        return $qb->getQuery()->getOneOrNullResult();
    }
}