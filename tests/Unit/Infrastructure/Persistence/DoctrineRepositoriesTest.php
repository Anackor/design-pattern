<?php

namespace App\Tests\Unit\Infrastructure\Persistence;

use App\Domain\Entity\Category;
use App\Domain\Entity\Document;
use App\Domain\Entity\Product;
use App\Domain\Entity\User;
use App\Domain\Entity\UserProfile;
use App\Infrastructure\Persistence\DoctrineCategoryRepository;
use App\Infrastructure\Persistence\DoctrineDocumentRepository;
use App\Infrastructure\Persistence\DoctrineProductRepository;
use App\Infrastructure\Persistence\DoctrineUserProfileRepository;
use App\Infrastructure\Persistence\DoctrineUserRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class DoctrineRepositoriesTest extends TestCase
{
    public function testCategoryRepositoryDelegatesToEntityManager(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(EntityRepository::class);
        $category = Category::named('Office');

        $entityManager->expects($this->once())->method('find')->with(Category::class, 5)->willReturn($category);
        $entityManager->expects($this->once())->method('getRepository')->with(Category::class)->willReturn($repository);
        $repository->expects($this->once())->method('findAll')->willReturn([$category]);
        $entityManager->expects($this->once())->method('persist')->with($category);
        $entityManager->expects($this->once())->method('flush');

        $doctrineRepository = new DoctrineCategoryRepository($entityManager);

        $this->assertSame($category, $doctrineRepository->catalogCategoryOfId(5));
        $this->assertSame([$category], $doctrineRepository->allCatalogCategories());
        $doctrineRepository->addToCatalog($category);
    }

    public function testDocumentRepositoryDelegatesToEntityManager(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(EntityRepository::class);
        $document = new Document('Contract', User::register('Doc Owner', 'doc@example.com'));

        $entityManager->expects($this->once())->method('find')->with(Document::class, 7)->willReturn($document);
        $entityManager->expects($this->once())->method('getRepository')->with(Document::class)->willReturn($repository);
        $repository->expects($this->once())->method('findAll')->willReturn([$document]);
        $entityManager->expects($this->once())->method('persist')->with($document);
        $entityManager->expects($this->once())->method('flush');

        $doctrineRepository = new DoctrineDocumentRepository($entityManager);

        $this->assertSame($document, $doctrineRepository->documentOfId(7));
        $this->assertSame([$document], $doctrineRepository->allDocuments());
        $doctrineRepository->store($document);
    }

    public function testProductRepositoryDelegatesToEntityManager(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(EntityRepository::class);
        $product = new Product('Notebook', 9.99, 'A5 notebook', Category::named('Office'));

        $entityManager->expects($this->once())->method('find')->with(Product::class, 8)->willReturn($product);
        $entityManager->expects($this->once())->method('getRepository')->with(Product::class)->willReturn($repository);
        $repository->expects($this->once())->method('findAll')->willReturn([$product]);
        $entityManager->expects($this->once())->method('persist')->with($product);
        $entityManager->expects($this->once())->method('flush');

        $doctrineRepository = new DoctrineProductRepository($entityManager);

        $this->assertSame($product, $doctrineRepository->catalogProductOfId(8));
        $this->assertSame([$product], $doctrineRepository->allCatalogProducts());
        $doctrineRepository->addToCatalog($product);
    }

    public function testUserProfileRepositoryDelegatesToEntityManager(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(EntityRepository::class);
        $profile = new UserProfile(
            User::register('Jane Doe', 'jane@example.com'),
            '+34600000000',
            'Main Street 1',
            new \DateTimeImmutable('2000-01-01')
        );

        $entityManager->expects($this->once())->method('find')->with(UserProfile::class, 11)->willReturn($profile);
        $entityManager->expects($this->once())->method('getRepository')->with(UserProfile::class)->willReturn($repository);
        $repository->expects($this->once())->method('findAll')->willReturn([$profile]);
        $entityManager->expects($this->once())->method('persist')->with($profile);
        $entityManager->expects($this->once())->method('flush');

        $doctrineRepository = new DoctrineUserProfileRepository($entityManager);

        $this->assertSame($profile, $doctrineRepository->profileOfId(11));
        $this->assertSame([$profile], $doctrineRepository->allProfiles());
        $doctrineRepository->addProfile($profile);
    }

    public function testUserRepositoryDelegatesToEntityManager(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(EntityRepository::class);
        $user = User::register('Jane Doe', 'jane@example.com');

        $entityManager->expects($this->once())->method('find')->with(User::class, 9)->willReturn($user);
        $entityManager->expects($this->once())->method('getRepository')->with(User::class)->willReturn($repository);
        $repository->expects($this->once())->method('findAll')->willReturn([$user]);
        $entityManager->expects($this->once())->method('persist')->with($user);
        $entityManager->expects($this->once())->method('flush');

        $doctrineRepository = new DoctrineUserRepository($entityManager);

        $this->assertSame($user, $doctrineRepository->registeredUserOfId(9));
        $this->assertSame([$user], $doctrineRepository->allRegisteredUsers());
        $doctrineRepository->addRegisteredUser($user);
    }
}
