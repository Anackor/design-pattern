<?php

namespace App\Tests\Unit\Application\Approval;

use App\Application\Approval\VacationRequestApproval;
use PHPUnit\Framework\TestCase;

class VacationRequestApprovalTest extends TestCase
{
    public function testVacationRequestApproval()
    {
        $vacationApproval = new VacationRequestApproval();

        $result = $vacationApproval->processRequest();

        $this->assertEquals("Vacation request approved.\n", $result);
    }
}
