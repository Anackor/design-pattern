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
        $user1 = (new User())
            ->setName("John Smith")
            ->setEmail("john@example.com")
            ->setRole(UserRole::ADMIN);
        $user2 = (new User())
            ->setName("Jane Smith")
            ->setEmail("jane@example.com")
            ->setRole(UserRole::MODERATOR);

        $manager->persist($user1);
        $manager->persist($user2);
        $manager->flush();
    }
}
