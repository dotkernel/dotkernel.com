<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Fixture;

use DateTimeImmutable;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use Light\App\Fixture\AuthorLoader;
use Light\App\Fixture\CategoryLoader;
use Light\App\Fixture\PostLoader;
use Light\Blog\Entity\Author;
use Light\Blog\Entity\Category;
use Light\Blog\Entity\Post;
use Light\Blog\Enum\PostStatusEnum;
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;

use function bin2hex;
use function dirname;
use function file_put_contents;
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

class PostLoaderTest extends UnitTest
{
    private string $jsonFile;
    private Author $author;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jsonFile = sprintf(
            '%s%slight-post-loader-%s%sarticles_cleaned.json',
            sys_get_temp_dir(),
            DIRECTORY_SEPARATOR,
            bin2hex(random_bytes(8)),
            DIRECTORY_SEPARATOR
        );

        mkdir(dirname($this->jsonFile), 0775, true);

        $this->author   = new Author();
        $this->category = new Category();
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
    public function testLoadCreatesPostWithCustomSlugWhenNothingMatchesInDb(): void
    {
        $this->writeArticles([
            $this->article('A post', ['post_slug' => ' custom-slug ']),
        ]);

        $lookups = [];
        $manager = $this->createEntityManager($this->createRepository([], $lookups));

        $persisted = null;
        $manager->expects($this->once())->method('persist')
            ->willReturnCallback(function (Post $post) use (&$persisted): void {
                $persisted = $post;
            });
        $manager->expects($this->once())->method('flush');

        $this->expectOutputString("CREATE: A post\n");
        $this->runLoader($manager);

        $this->assertInstanceOf(Post::class, $persisted);
        $this->assertSame('custom-slug', $persisted->getSlug());
        $this->assertSame(['custom-slug', 'a-post'], $lookups);
    }

    /**
     * @throws Exception
     */
    public function testLoadUpdatesExistingPostFoundByCustomSlug(): void
    {
        $this->writeArticles([
            $this->article('A post', ['post_slug' => 'custom-slug']),
        ]);

        $existing = $this->existingPost('custom-slug', 'A post');

        $lookups = [];
        $manager = $this->createEntityManager($this->createRepository(['custom-slug' => $existing], $lookups));
        $manager->expects($this->never())->method('persist');

        $this->expectOutputString("UNCHANGED: A post\n");
        $this->runLoader($manager);

        $this->assertSame('custom-slug', $existing->getSlug());
        $this->assertSame(['custom-slug'], $lookups);
    }

    /**
     * @throws Exception
     */
    public function testLoadRenamesExistingPostFoundByTitleSlugToCustomSlug(): void
    {
        $this->writeArticles([
            $this->article('A post', ['post_slug' => 'custom-slug']),
        ]);

        // Imported before post_slug was added to the JSON.
        $existing = $this->existingPost('a-post', 'A post');

        $lookups = [];
        $manager = $this->createEntityManager($this->createRepository(['a-post' => $existing], $lookups));
        $manager->expects($this->never())->method('persist');

        $this->expectOutputString("UPDATE: A post\n");
        $this->runLoader($manager);

        $this->assertSame('custom-slug', $existing->getSlug());
        $this->assertSame(['custom-slug', 'a-post'], $lookups);
    }

    /**
     * @throws Exception
     */
    public function testLoadFallsBackToTitleSlugWhenPostSlugIsMissingOrEmpty(): void
    {
        $this->writeArticles([
            $this->article('A post'),
            $this->article('A post', ['post_slug' => '']),
        ]);

        $lookups = [];
        $manager = $this->createEntityManager($this->createRepository([], $lookups));
        $manager->expects($this->exactly(2))->method('persist');

        $this->expectOutputString("CREATE: A post\nCREATE: A post\n");
        $this->runLoader($manager);

        $this->assertSame(['a-post', 'a-post-2'], $lookups);
    }

    /**
     * @throws Exception
     */
    public function testLoadUpdatesEveryChangedFieldOnExistingPost(): void
    {
        $this->writeArticles([
            $this->article('A post', [
                'post_status'   => 'archived',
                'post_date'     => '0000-00-00 00:00:00',
                'excerpt'       => 'New excerpt',
                'tl_dr'         => 'New TL;DR',
                'isObsolete'    => true,
                'isTwig'        => true,
                'opengraph_img' => 'og.png',
            ]),
        ]);

        $existing = $this->existingPost('a-post', 'Old title');
        $existing->setCategory(new Category());
        $existing->setAuthor(new Author());

        $lookups = [];
        $manager = $this->createEntityManager($this->createRepository(['a-post' => $existing], $lookups));
        $manager->expects($this->never())->method('persist');

        $this->expectOutputString("UPDATE: A post\n");
        $this->runLoader($manager);

        $this->assertSame('A post', $existing->getTitle());
        $this->assertSame(PostStatusEnum::Archived, $existing->getStatus());
        $this->assertNotSame('2022-07-18 09:12:59', $existing->getPostDate()->format('Y-m-d H:i:s'));
        $this->assertSame($this->category, $existing->getCategory());
        $this->assertSame($this->author, $existing->getAuthor());
        $this->assertSame('New excerpt', $existing->getExcerpt());
        $this->assertSame('New TL;DR', $existing->getTlDr());
        $this->assertTrue($existing->isObsolete());
        $this->assertTrue($existing->isTwig());
        $this->assertSame('og.png', $existing->getOpenGraphImage());
    }

    /**
     * @throws Exception
     */
    public function testLoadMapsPostStatuses(): void
    {
        $this->writeArticles([
            $this->article('Private post', ['post_status' => 'private']),
            $this->article('Draft post', ['post_status' => 'unknown']),
        ]);

        $lookups = [];
        $manager = $this->createEntityManager($this->createRepository([], $lookups));

        $statuses = [];
        $manager->expects($this->exactly(2))->method('persist')
            ->willReturnCallback(function (Post $post) use (&$statuses): void {
                $statuses[] = $post->getStatus();
            });

        $this->expectOutputString("CREATE: Private post\nCREATE: Draft post\n");
        $this->runLoader($manager);

        $this->assertSame([PostStatusEnum::Private, PostStatusEnum::Draft], $statuses);
    }

    /**
     * @throws Exception
     */
    public function testLoadSkipsArticleWithUnknownAuthor(): void
    {
        $this->writeArticles([
            $this->article('A post', ['author' => ['display_name' => 'nobody']]),
        ]);

        $lookups = [];
        $manager = $this->createEntityManager($this->createRepository([], $lookups));
        $manager->expects($this->never())->method('persist');

        $this->expectOutputString("SKIP (no author): A post\n");
        $this->runLoader($manager);

        $this->assertSame([], $lookups);
    }

    /**
     * @throws Exception
     */
    public function testLoadThrowsWhenJsonFileCannotBeRead(): void
    {
        $manager = $this->createStub(EntityManagerInterface::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to read file');

        @$this->runLoader($manager);
    }

    public function testDependenciesAndOrder(): void
    {
        $loader = new PostLoader($this->jsonFile);

        $this->assertSame([AuthorLoader::class, CategoryLoader::class], $loader->getDependencies());
        $this->assertSame(3, $loader->getOrder());
    }

    /**
     * @param array<string, mixed> $extra
     * @return array<string, mixed>
     */
    private function article(string $title, array $extra = []): array
    {
        return $extra + [
            'post_title'  => $title,
            'post_date'   => '2022-07-18 09:12:59',
            'post_status' => 'published',
            'author'      => ['display_name' => 'arhimede'],
            'excerpt'     => 'Excerpt',
            'tl_dr'       => 'TL;DR',
            'tags'        => [],
        ];
    }

    private function existingPost(string $slug, string $title): Post
    {
        $post = new Post();
        $post->setSlug($slug);
        $post->setTitle($title);
        $post->setPostDate(new DateTimeImmutable('2022-07-18 09:12:59'));
        $post->setStatus(PostStatusEnum::Published);
        $post->setCategory($this->category);
        $post->setAuthor($this->author);
        $post->setExcerpt('Excerpt');
        $post->setTldr('TL;DR');
        $post->setObsolete(false);
        $post->setTwig(false);
        $post->setOpenGraphImage(null);

        return $post;
    }

    /**
     * @param list<array<string, mixed>> $articles
     */
    private function writeArticles(array $articles): void
    {
        file_put_contents($this->jsonFile, json_encode([
            ['slug' => 'category', 'articles' => $articles],
        ]));
    }

    /**
     * @param array<string, Post> $postsBySlug
     * @param list<string> $lookups
     * @return EntityRepository<Post>
     * @throws Exception
     */
    private function createRepository(array $postsBySlug, array &$lookups): EntityRepository
    {
        $repository = $this->createStub(EntityRepository::class);
        $repository->method('findOneBy')->willReturnCallback(
            function (array $criteria) use ($postsBySlug, &$lookups): ?Post {
                $lookups[] = $criteria['slug'];
                return $postsBySlug[$criteria['slug']] ?? null;
            }
        );

        return $repository;
    }

    private function runLoader(EntityManagerInterface $manager): void
    {
        $referenceRepository = new ReferenceRepository($manager);
        $referenceRepository->addReference('category_category', $this->category);
        $referenceRepository->addReference('author_arhimede', $this->author);

        $loader = new PostLoader($this->jsonFile);
        $loader->setReferenceRepository($referenceRepository);
        $loader->load($manager);
    }

    /**
     * @param EntityRepository<Post> $repository
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

        $manager->method('getClassMetadata')->willReturnCallback(function (string $class) {
            $metadata = $this->createStub(ClassMetadata::class);
            $metadata->method('getName')->willReturn($class);
            return $metadata;
        });

        return $manager;
    }
}
