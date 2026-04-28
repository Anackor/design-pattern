<?php

namespace App\Tests\Unit\Infrastructure\Security;

use App\Infrastructure\Security\AccessChecker;
use App\Shared\Security\UserContext;
use PHPUnit\Framework\TestCase;

class AccessCheckerTest extends TestCase
{
    public function testCanViewFinancialReportsWhenUserHasExpectedRole(): void
    {
        $checker = new AccessChecker(new UserContext(['ROLE_USER', 'ROLE_ADMIN']));

        $this->assertTrue($checker->canViewFinancialReports());
    }

    public function testCannotViewFinancialReportsWithoutExpectedRole(): void
    {
        $checker = new AccessChecker(new UserContext(['ROLE_ADMIN']));

        $this->assertFalse($checker->canViewFinancialReports());
    }
}
