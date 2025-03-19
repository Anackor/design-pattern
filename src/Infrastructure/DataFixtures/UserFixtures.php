<?php

namespace App\Infrastructure\DataFixtures;

use App\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user1 = new User("John Doe", "john@example.com");
        $user2 = new User("Jane Doe", "jane@example.com");

        $manager->persist($user1);
        $manager->persist($user2);
        $manager->flush();
    }
}
