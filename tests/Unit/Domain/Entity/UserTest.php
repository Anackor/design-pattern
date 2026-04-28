<?php

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Document;
use App\Domain\Entity\User;
use App\Domain\Entity\UserOrders;
use App\Domain\Entity\UserProfile;
use App\Domain\Enum\UserRole;
use App\Shared\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testRegisterCreatesAValidUserWithDefaults(): void
    {
        $user = User::register('Jane Doe', 'jane@example.com');

        $this->assertSame('Jane Doe', $user->getName());
        $this->assertSame('jane@example.com', $user->getEmail());
        $this->assertSame('unknown', $user->getCountry());
        $this->assertSame('standard', $user->getType());
        $this->assertSame(UserRole::USER, $user->getRole());
    }

    public function testRegisterRejectsEmptyName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User name cannot be empty.');

        User::register('   ', 'jane@example.com');
    }

    public function testRegisterRejectsInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address: invalid-email');

        User::register('Jane Doe', 'invalid-email');
    }

    public function testSetCountryRejectsEmptyValue(): void
    {
        $user = User::register('Jane Doe', 'jane@example.com');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Country name cannot be empty.');

        $user->setCountry('   ');
    }

    public function testSetTypeRejectsEmptyValue(): void
    {
        $user = User::register('Jane Doe', 'jane@example.com');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User type cannot be empty.');

        $user->setType('   ');
    }

    public function testUserManagesRelationsAndNormalizedValues(): void
    {
        $user = User::register('  Jane Doe  ', 'jane@example.com');
        $profile = new UserProfile($user, '+34 600 000 000', '  Main Street  ', new \DateTimeImmutable('2000-01-01'));
        $order = UserOrders::placeFor($user, Money::fromDecimalString('19.99'));
        $document = new Document('Invoice', $user);

        $user->setRole(UserRole::ADMIN);
        $user->setCountry('  Spain  ');
        $user->setType('  manager  ');
        $user->setUserProfile($profile);
        $user->addUserOrder($order);
        $user->addDocument($document);

        $this->assertSame('Jane Doe', $user->getName());
        $this->assertSame(UserRole::ADMIN, $user->getRole());
        $this->assertSame('Spain', $user->getCountry());
        $this->assertSame('manager', $user->getType());
        $this->assertSame($profile, $user->getUserProfile());
        $this->assertCount(1, $user->getUserOrders());
        $this->assertCount(1, $user->getDocuments());

        $user->removeUserOrder($order);
        $user->removeDocument($document);

        $this->assertCount(0, $user->getUserOrders());
        $this->assertCount(0, $user->getDocuments());
        $this->assertNull($order->getUser());
        $this->assertNull($document->getUser());
    }
}
