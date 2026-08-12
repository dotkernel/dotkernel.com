<?php

declare(strict_types=1);

namespace Light\Blog\Factory\Tag;

use Doctrine\ORM\EntityManager;
use Light\Blog\Entity\Tag;
use Light\Blog\Repository\TagRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class TagResourceRepositoryFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): TagRepository
    {
        $entityManager = $container->get(EntityManager::class);

        $repository = $entityManager->getRepository(Tag::class);
        assert($repository instanceof TagRepository);

        return $repository;
    }
}
