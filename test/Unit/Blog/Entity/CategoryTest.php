<?php

declare(strict_types=1);

namespace LightTest\Unit\Blog\Entity;

use Light\Blog\Entity\Category;
use Light\Blog\Entity\Post;
use Light\Blog\Enum\PostStatusEnum;
use LightTest\Unit\UnitTest;

class CategoryTest extends UnitTest
{
    public function testGetPublishedPostsCountOnlyCountsPublishedPosts(): void
    {
        $category = new Category();

        $published = new Post();
        $published->setStatus(PostStatusEnum::Published);

        $draft = new Post();
        $draft->setStatus(PostStatusEnum::Draft);

        $private = new Post();
        $private->setStatus(PostStatusEnum::Private);

        $category->getPosts()->add($published);
        $category->getPosts()->add($draft);
        $category->getPosts()->add($private);

        self::assertSame(1, $category->getPublishedPostsCount());
    }
}
