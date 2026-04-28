<?php

namespace App\Tests\Unit\Shared\Security;

use App\Shared\Security\UserContext;
use PHPUnit\Framework\TestCase;

class UserContextTest extends TestCase
{
    public function testGetRolesReturnsRolesPassedAtConstruction(): void
    {
        $context = new UserContext(['ROLE_USER', 'ROLE_ADMIN']);

        $this->assertSame(['ROLE_USER', 'ROLE_ADMIN'], $context->getRoles());
    }
}
