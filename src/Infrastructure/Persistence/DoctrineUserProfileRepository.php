<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\UserProfile;
use App\Domain\Repository\UserProfileRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineUserProfileRepository implements UserProfileRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findById(int $id): ?UserProfile
    {
        return $this->entityManager->find(UserProfile::class, $id);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(UserProfile::class)->findAll();
    }

    public function save(UserProfile $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
