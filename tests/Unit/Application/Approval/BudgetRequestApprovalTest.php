<?php

namespace App\Tests\Unit\Application\Approval;

use App\Application\Approval\BudgetRequestApproval;
use PHPUnit\Framework\TestCase;

class BudgetRequestApprovalTest extends TestCase
{
    public function testBudgetRequestApproval()
    {
        $budgetApproval = new BudgetRequestApproval('budget123');
        
        $result = $budgetApproval->processRequest();
        
        $this->assertEquals("Budget request rejected.\n", $result);
    }
}
