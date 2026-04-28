<?php

namespace App\Tests\Unit\Shared\ValueObject;

use App\Shared\ValueObject\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testFromStringAcceptsValidEmail(): void
    {
        $email = Email::fromString('user@example.com');

        $this->assertSame('user@example.com', $email->value());
    }

    public function testFromStringTrimsWhitespace(): void
    {
        $email = Email::fromString('  user@example.com  ');

        $this->assertSame('user@example.com', $email->value());
    }

    public function testFromStringRejectsInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address: not-an-email');

        Email::fromString('not-an-email');
    }

    public function testEqualsComparesValue(): void
    {
        $left = Email::fromString('user@example.com');
        $right = Email::fromString('user@example.com');

        $this->assertTrue($left->equals($right));
    }
}
