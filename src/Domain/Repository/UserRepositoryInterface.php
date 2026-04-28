<?php

namespace App\Domain\Repository;

use App\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function registeredUserOfId(int $userId): ?User;

    public function addRegisteredUser(User $user): void;
}
