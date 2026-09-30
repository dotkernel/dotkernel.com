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
use LightTest\Unit\UnitTest;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;

use function array_count_values;
use function dirname;
use function file_get_contents;
use function json_decode;
use function preg_quote;
use function preg_replace;
use function strtolower;
use function trim;

/**
 * Runs PostLoader against the real articles_cleaned.json, using the
 * "Autologin using Cookie / Remember Me in Dotkernel" article that sets post_slug.
 */
class PostLoaderTest extends UnitTest
{
    private const string TITLE       = 'Autologin using Cookie / Remember Me in Dotkernel';
    private const string CUSTOM_SLUG = 'autologin-cookie-remember-me-feature';
    private const string TITLE_SLUG  = 'autologin-using-cookie-remember-me-in-dotkernel';

    /**
     * @throws Exception
     */
    public function testLoadCreatesPostWithCustomSlugWhenNothingMatchesInDb(): void
    {
        $lookups = [];
        $manager = $this->createEntityManager($this->createRepository([], $lookups));

        $persistedSlugs = [];
        $manager->expects($this->atLeastOnce())->method('persist')
            ->willReturnCallback(function (Post $post) use (&$persistedSlugs): void {
                $persistedSlugs[$post->getTitle()] = $post->getSlug();
            });

        $this->expectOutputRegex('/CREATE: ' . preg_quote(self::TITLE, '/') . '\n/');
        $this->runLoader($manager);

        $this->assertSame(self::CUSTOM_SLUG, $persistedSlugs[self::TITLE]);
        $this->assertNotContains(self::TITLE_SLUG, $persistedSlugs);

        // Not found by post_slug, so the title-based slug is tried before creating.
        $counts = array_count_values($lookups);
        $this->assertSame(1, $counts[self::CUSTOM_SLUG]);
        $this->assertSame(1, $counts[self::TITLE_SLUG]);
    }

    /**
     * @throws Exception
     */
    public function testLoadUpdatesPostFoundByCustomSlug(): void
    {
        $existing = $this->existingPost(self::CUSTOM_SLUG, 'Old title');

        $lookups = [];
        $manager = $this->createEntityManager($this->createRepository([self::CUSTOM_SLUG => $existing], $lookups));
        $manager->expects($this->atLeastOnce())->method('flush');

        $this->expectOutputRegex('/UPDATE: ' . preg_quote(self::TITLE, '/') . '\n/');
        $this->runLoader($manager);

        $this->assertSame(self::CUSTOM_SLUG, $existing->getSlug());
        $this->assertSame(self::TITLE, $existing->getTitle());
        $this->assertNotContains(self::TITLE_SLUG, $lookups);
    }

    /**
     * @throws Exception
     */
    public function testLoadRenamesPostImportedUnderTitleSlugToCustomSlug(): void
    {
        // Imported before post_slug was added to the JSON.
        $existing = $this->existingPost(self::TITLE_SLUG, self::TITLE);

        $lookups = [];
        $manager = $this->createEntityManager($this->createRepository([self::TITLE_SLUG => $existing], $lookups));
        $manager->expects($this->atLeastOnce())->method('flush');

        $this->expectOutputRegex('/UPDATE: ' . preg_quote(self::TITLE, '/') . '\n/');
        $this->runLoader($manager);

        $this->assertSame(self::CUSTOM_SLUG, $existing->getSlug());
    }

    public function testDependenciesAndOrder(): void
    {
        $loader = new PostLoader();

        $this->assertSame([AuthorLoader::class, CategoryLoader::class], $loader->getDependencies());
        $this->assertSame(3, $loader->getOrder());
    }

    private function existingPost(string $slug, string $title): Post
    {
        $post = new Post();
        $post->setSlug($slug);
        $post->setTitle($title);
        $post->setPostDate(new DateTimeImmutable());
        $post->setCategory(new Category());
        $post->setAuthor(new Author());
        $post->setExcerpt('');

        return $post;
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

    /**
     * Registers the category and author references the real articles_cleaned.json needs.
     */
    private function runLoader(EntityManagerInterface $manager): void
    {
        $contents = file_get_contents(dirname(__DIR__, 4) . '/src/App/src/Fixture/articles_cleaned.json');
        $this->assertIsString($contents);

        /** @var list<array{slug: string, articles: list<array{author?: array{display_name?: string}}>}> $categories */
        $categories = json_decode($contents, true);

        $referenceRepository = new ReferenceRepository($manager);
        foreach ($categories as $category) {
            $referenceRepository->setReference('category_' . $category['slug'], new Category());

            foreach ($category['articles'] as $article) {
                $authorName = $article['author']['display_name'] ?? null;
                if ($authorName !== null) {
                    $referenceRepository->setReference('author_' . $this->slugify($authorName), new Author());
                }
            }
        }

        $loader = new PostLoader();
        $loader->setReferenceRepository($referenceRepository);
        $loader->load($manager);
    }

    private function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
        return trim($text, '-');
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
