<?php

declare(strict_types=1);

namespace Light\App\Factory\Articles;

use Doctrine\ORM\EntityManager;
use Light\Blog\Entity\Article;
use Light\Blog\Repository\ArticleRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class ArticleRepositoryFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ArticleRepository
    {
        $entityManager = $container->get(EntityManager::class);

        $repository = $entityManager->getRepository(Article::class);
        assert($repository instanceof ArticleRepository);

        return $repository;
    }
}
