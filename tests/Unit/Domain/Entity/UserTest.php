<?php

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\User;
use App\Domain\Enum\UserRole;
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
}
