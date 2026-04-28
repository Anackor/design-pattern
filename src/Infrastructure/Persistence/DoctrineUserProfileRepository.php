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

    public function profileOfId(int $profileId): ?UserProfile
    {
        return $this->entityManager->find(UserProfile::class, $profileId);
    }

    public function allProfiles(): array
    {
        return $this->entityManager->getRepository(UserProfile::class)->findAll();
    }

    public function addProfile(UserProfile $profile): void
    {
        $this->entityManager->persist($profile);
        $this->entityManager->flush();
    }
}
