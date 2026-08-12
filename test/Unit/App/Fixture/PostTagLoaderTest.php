<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Fixture;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use Light\App\Fixture\PostTagLoader;
use Light\Blog\Entity\Post;
use Light\Blog\Entity\PostTag;
use Light\Blog\Entity\Tag;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;

use function bin2hex;
use function dirname;
use function file_put_contents;
use function is_a;
use function is_dir;
use function is_file;
use function json_encode;
use function mkdir;
use function random_bytes;
use function rmdir;
use function sprintf;
use function sys_get_temp_dir;
use function unlink;

use const DIRECTORY_SEPARATOR;

class PostTagLoaderTest extends UnitTest
{
    private string $jsonFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jsonFile = sprintf(
            '%s%slight-post-tag-loader-%s%sarticles_cleaned.json',
            sys_get_temp_dir(),
            DIRECTORY_SEPARATOR,
            bin2hex(random_bytes(8)),
            DIRECTORY_SEPARATOR
        );

        mkdir(dirname($this->jsonFile), 0775, true);
    }

    protected function tearDown(): void
    {
        if (is_file($this->jsonFile)) {
            unlink($this->jsonFile);
        }

        $directory = dirname($this->jsonFile);
        if (is_dir($directory)) {
            rmdir($directory);
        }

        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testLoadRemovesPostTagLinksForTagsNoLongerListedOnTheArticle(): void
    {
        // The article now only lists "htaccess" — "admin" was removed from the JSON.
        $this->writeArticles([
            ['post_title' => 'A post', 'tags' => [['slug' => 'htaccess', 'name' => 'htaccess']]],
        ]);

        $post = $this->createStub(Post::class);
        $post->method('getTitle')->willReturn('A post');

        $htaccessTag = $this->createStub(Tag::class);
        $htaccessTag->method('getSlug')->willReturn('htaccess');
        $htaccessTag->method('getName')->willReturn('htaccess');

        $adminTag = $this->createStub(Tag::class);
        $adminTag->method('getSlug')->willReturn('admin');
        $adminTag->method('getName')->willReturn('admin');

        $existingHtaccessLink = $this->createStub(PostTag::class);
        $existingHtaccessLink->method('getTag')->willReturn($htaccessTag);

        $existingAdminLink = $this->createStub(PostTag::class);
        $existingAdminLink->method('getTag')->willReturn($adminTag);

        // The DB still has links to both tags from a previous run.
        $repository = $this->createStub(EntityRepository::class);
        $repository->method('findOneBy')->willReturn($existingHtaccessLink);
        $repository->method('findBy')->willReturn([$existingHtaccessLink, $existingAdminLink]);

        $manager = $this->createEntityManager($repository);
        $manager->expects($this->once())->method('remove')->with($existingAdminLink);
        $manager->expects($this->once())->method('flush');

        $referenceRepository = $this->createReferenceRepository($manager);
        $referenceRepository->addReference('post_1', $post);
        $referenceRepository->addReference('tag_htaccess', $htaccessTag);

        $loader = new PostTagLoader($this->jsonFile);
        $loader->setReferenceRepository($referenceRepository);
        $loader->load($manager);
    }

    /**
     * @throws Exception
     */
    public function testLoadDoesNotRemoveLinksForTagsStillListedOnTheArticle(): void
    {
        $this->writeArticles([
            ['post_title' => 'A post', 'tags' => [['slug' => 'htaccess', 'name' => 'htaccess']]],
        ]);

        $post = $this->createStub(Post::class);
        $post->method('getTitle')->willReturn('A post');

        $htaccessTag = $this->createStub(Tag::class);
        $htaccessTag->method('getSlug')->willReturn('htaccess');
        $htaccessTag->method('getName')->willReturn('htaccess');

        $existingHtaccessLink = $this->createStub(PostTag::class);
        $existingHtaccessLink->method('getTag')->willReturn($htaccessTag);

        $repository = $this->createStub(EntityRepository::class);
        $repository->method('findOneBy')->willReturn($existingHtaccessLink);
        $repository->method('findBy')->willReturn([$existingHtaccessLink]);

        $manager = $this->createEntityManager($repository);
        $manager->expects($this->never())->method('remove');

        $referenceRepository = $this->createReferenceRepository($manager);
        $referenceRepository->addReference('post_1', $post);
        $referenceRepository->addReference('tag_htaccess', $htaccessTag);

        $loader = new PostTagLoader($this->jsonFile);
        $loader->setReferenceRepository($referenceRepository);
        $loader->load($manager);
    }

    /**
     * @param list<array{post_title: string, tags: list<array{slug: string, name: string}>}> $articles
     */
    private function writeArticles(array $articles): void
    {
        file_put_contents($this->jsonFile, json_encode([
            ['slug' => 'category', 'articles' => $articles],
        ]));
    }

    /**
     * @param EntityRepository<PostTag> $repository
     * @throws Exception
     */
    private function createEntityManager(EntityRepository $repository): EntityManagerInterface&MockObject
    {
        $manager = $this->createMock(EntityManagerInterface::class);
        $manager->method('getRepository')->willReturn($repository);
        $manager->method('contains')->willReturn(true);

        $unitOfWork = $this->createStub(UnitOfWork::class);
        $unitOfWork->method('isInIdentityMap')->willReturn(false);
        $manager->method('getUnitOfWork')->willReturn($unitOfWork);

        // PHPUnit stubs are dynamically-generated *subclasses* of the entity they stub — mirror
        // what Doctrine's real getClassMetadata() does for proxies: resolve back to the real
        // mapped class, otherwise ReferenceRepository stores/looks up references under the
        // wrong (generated) class name and every hasReference() check silently returns false.
        $manager->method('getClassMetadata')->willReturnCallback(function (string $class) {
            $realClass = match (true) {
                is_a($class, Post::class, true) => Post::class,
                is_a($class, Tag::class, true) => Tag::class,
                is_a($class, PostTag::class, true) => PostTag::class,
                default => $class,
            };

            $metadata = $this->createStub(ClassMetadata::class);
            $metadata->method('getName')->willReturn($realClass);
            return $metadata;
        });

        return $manager;
    }

    private function createReferenceRepository(EntityManagerInterface $manager): ReferenceRepository
    {
        return new ReferenceRepository($manager);
    }
}
