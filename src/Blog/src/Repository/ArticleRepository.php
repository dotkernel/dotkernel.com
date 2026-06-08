<?php

declare(strict_types=1);

namespace Light\Blog\Repository;

use Light\App\Repository\AbstractRepository;
use Light\Blog\Entity\Article;

class ArticleRepository extends AbstractRepository
{
    /**
     * @return array<Article>
     */
    public function getArticles(): array
    {
        $qb = $this->getQueryBuilder()
            ->select('articles')
            ->from(Article::class, 'articles')
            ->leftJoin('articles.category', 'category');

        return $qb->getQuery()->getResult();
    }

    public function getArticleResource(string $slug): ?Article
    {
        $qb = $this->getQueryBuilder()
            ->select('articles')
            ->from(Article::class, 'articles')
            ->where('articles.slug = :slug')
            ->setParameter('slug', $slug);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
