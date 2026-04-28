<?php

namespace App\Tests\Unit\Domain\Flyweight;

use App\Domain\Flyweight\UserType;
use PHPUnit\Framework\TestCase;

class UserTypeTest extends TestCase
{
    public function testFromStringNormalizesType(): void
    {
        $userType = UserType::fromString(' premium ');

        $this->assertSame('premium', $userType->getType());
    }

    public function testFromStringRejectsEmptyType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User type cannot be empty.');

        UserType::fromString('   ');
    }

    public function testEqualsComparesType(): void
    {
        $left = UserType::fromString('standard');
        $right = UserType::fromString('standard');

        $this->assertTrue($left->equals($right));
    }
}
