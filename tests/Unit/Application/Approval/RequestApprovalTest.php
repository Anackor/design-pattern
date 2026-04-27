<?php

namespace App\Tests\Unit\Application\Approval;

use App\Application\Approval\VacationRequestApproval;
use App\Application\Approval\BudgetRequestApproval;
use PHPUnit\Framework\TestCase;

class RequestApprovalTest extends TestCase
{
    public function testTemplateMethod()
    {
        $vacationApproval = new VacationRequestApproval();
        $result = $vacationApproval->processRequest();
        $this->assertEquals("Vacation request approved.\n", $result);

        $budgetApproval = new BudgetRequestApproval();
        $result = $budgetApproval->processRequest();
        $this->assertEquals("Budget request rejected.\n", $result);
    }
}
