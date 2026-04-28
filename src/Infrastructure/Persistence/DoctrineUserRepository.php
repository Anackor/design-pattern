<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineUserRepository implements UserRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function registeredUserOfId(int $userId): ?User
    {
        return $this->entityManager->find(User::class, $userId);
    }

    public function allRegisteredUsers(): array
    {
        return $this->entityManager->getRepository(User::class)->findAll();
    }

    public function addRegisteredUser(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
