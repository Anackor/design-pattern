<?php

namespace App\DataFixtures;

use App\Domain\Entity\User;
use App\Domain\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user1 = User::register('John Smith', 'john@example.com', UserRole::ADMIN);
        $user2 = User::register('Jane Smith', 'jane@example.com', UserRole::MODERATOR);

        $manager->persist($user1);
        $manager->persist($user2);
        $manager->flush();
    }
}
