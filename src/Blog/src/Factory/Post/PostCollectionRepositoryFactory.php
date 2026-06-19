<?php

declare(strict_types=1);

namespace Light\Blog\Factory\Post;

use Doctrine\ORM\EntityManager;
use Light\Blog\Entity\Post;
use Light\Blog\Repository\PostRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class PostCollectionRepositoryFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): PostRepository
    {
        $entityManager = $container->get(EntityManager::class);

        $repository = $entityManager->getRepository(Post::class);
        assert($repository instanceof PostRepository);

        return $repository;
    }
}
