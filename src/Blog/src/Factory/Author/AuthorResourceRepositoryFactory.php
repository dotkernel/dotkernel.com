<?php

declare(strict_types=1);

namespace Light\Blog\Factory\Author;

use Doctrine\ORM\EntityManager;
use Light\Blog\Entity\Author;
use Light\Blog\Repository\AuthorRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class AuthorResourceRepositoryFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): AuthorRepository
    {
        $entityManager = $container->get(EntityManager::class);

        $repository = $entityManager->getRepository(Author::class);
        assert($repository instanceof AuthorRepository);

        return $repository;
    }
}
