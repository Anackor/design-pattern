<?php

namespace App\Tests\Unit\Domain\Builder;

use App\Domain\Builder\UserProfileBuilder;
use App\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

class UserProfileBuilderTest extends TestCase
{
    public function testBuildCreatesProfileFromValidData(): void
    {
        $profile = (new UserProfileBuilder())
            ->setUser(User::register('Jane Doe', 'jane@example.com'))
            ->setPhone('123456789')
            ->setAddress('Main Street 123')
            ->setBirthdate('2000-01-01')
            ->build();

        $this->assertSame('123456789', $profile->getPhone());
        $this->assertSame('Main Street 123', $profile->getAddress());
        $this->assertSame('2000-01-01', $profile->getDateOfBirth()->format('Y-m-d'));
    }

    public function testBuildAcceptsDateTimeInterfaceBirthdate(): void
    {
        $profile = (new UserProfileBuilder())
            ->setUser(User::register('Jane Doe', 'jane@example.com'))
            ->setPhone('123456789')
            ->setAddress('Main Street 123')
            ->setBirthdate(new \DateTimeImmutable('2000-01-01'))
            ->build();

        $this->assertSame('2000-01-01', $profile->getDateOfBirth()->format('Y-m-d'));
    }

    public function testBuildFailsWhenUserIsMissing(): void
    {
        $builder = (new UserProfileBuilder())
            ->setPhone('123456789')
            ->setAddress('Main Street 123')
            ->setBirthdate('2000-01-01');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('User profile builder requires a user.');

        $builder->build();
    }

    public function testSetBirthdateRejectsEmptyString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User profile birth date cannot be empty.');

        (new UserProfileBuilder())->setBirthdate('   ');
    }

    public function testSetBirthdateRejectsUnexpectedFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User profile birth date must use Y-m-d format.');

        (new UserProfileBuilder())->setBirthdate('01/01/2000');
    }
}
