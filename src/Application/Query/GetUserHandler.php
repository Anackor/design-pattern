<?php

namespace App\Application\Query;

use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Entity\User;
use Symfony\Component\Uid\UuidV4;

class GetUserHandler
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function handle(GetUserQuery $query): ?User
    {
        return $this->userRepository->findById($query->userId);
    }
}
