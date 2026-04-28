<?php

namespace App\Tests\Unit\DataFixtures;

use App\DataFixtures\UserFixtures;
use App\Domain\Entity\User;
use App\Domain\Enum\UserRole;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

class UserFixturesTest extends TestCase
{
    public function testLoadPersistsTwoExpectedUsers(): void
    {
        $persistedUsers = [];

        $manager = $this->createMock(ObjectManager::class);
        $manager->expects($this->exactly(2))
            ->method('persist')
            ->willReturnCallback(static function (object $entity) use (&$persistedUsers): void {
                $persistedUsers[] = $entity;
            });
        $manager->expects($this->once())->method('flush');

        (new UserFixtures())->load($manager);

        $this->assertCount(2, $persistedUsers);
        $this->assertContainsOnlyInstancesOf(User::class, $persistedUsers);
        $this->assertSame('John Smith', $persistedUsers[0]->getName());
        $this->assertSame(UserRole::ADMIN, $persistedUsers[0]->getRole());
        $this->assertSame('Jane Smith', $persistedUsers[1]->getName());
        $this->assertSame(UserRole::MODERATOR, $persistedUsers[1]->getRole());
    }
}
