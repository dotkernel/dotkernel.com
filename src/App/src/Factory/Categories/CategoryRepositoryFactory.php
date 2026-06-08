<?php

declare(strict_types=1);

namespace Light\App\Factory\Categories;

use Doctrine\ORM\EntityManager;
use Light\Blog\Entity\Category;
use Light\Blog\Repository\CategoryRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use function assert;

class CategoryRepositoryFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): CategoryRepository
    {
        $entityManager = $container->get(EntityManager::class);

        $repository = $entityManager->getRepository(Category::class);
        assert($repository instanceof CategoryRepository);

        return $repository;
    }
}