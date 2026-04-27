<?php

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\User;
use App\Domain\Entity\UserProfile;
use PHPUnit\Framework\TestCase;

class UserProfileTest extends TestCase
{
    public function testConstructorCreatesValidProfile(): void
    {
        $profile = new UserProfile(
            User::register('Jane Doe', 'jane@example.com'),
            '123456789',
            'Main Street 123',
            new \DateTimeImmutable('2000-01-01')
        );

        $this->assertSame('123456789', $profile->getPhone());
        $this->assertSame('Main Street 123', $profile->getAddress());
    }

    public function testConstructorRejectsEmptyAddress(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User profile address cannot be empty.');

        new UserProfile(
            User::register('Jane Doe', 'jane@example.com'),
            '123456789',
            '   ',
            new \DateTimeImmutable('2000-01-01')
        );
    }

    public function testConstructorRejectsInvalidPhone(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User profile phone must contain 9 to 15 digits.');

        new UserProfile(
            User::register('Jane Doe', 'jane@example.com'),
            '12',
            'Main Street 123',
            new \DateTimeImmutable('2000-01-01')
        );
    }

    public function testConstructorRejectsFutureBirthDate(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User profile birth date cannot be in the future.');

        new UserProfile(
            User::register('Jane Doe', 'jane@example.com'),
            '123456789',
            'Main Street 123',
            new \DateTimeImmutable('+1 day')
        );
    }
}
