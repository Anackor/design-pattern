<?php

namespace App\Application\Approval;

/**
 * Class BudgetRequestApproval
 *
 * This concrete class implements the specific steps for processing a budget request.
 * Like the VacationRequestApproval class, it overrides the abstract methods from 
 * RequestApproval to define how to review and approve budget requests.
 *
 * The reviewRequest method here checks if the budget request aligns with the company’s 
 * financial policies or whether it exceeds the available budget. The approveOrReject 
 * method then makes the final decision.
 *
 * The Template Method design pattern provides a solid structure for the approval process, 
 * while ensuring that specific details for each request type (vacation, budget, etc.) can 
 * be easily customized.
 */
class BudgetRequestApproval extends RequestApproval
{
    
    /**
     * Implementation of reviewing a budget request.
     * The specific logic for reviewing budget requests is defined here.
     */
    protected function reviewRequest(): bool
    {
        return true;
    }
    
    /**
    * Implementation of approving or rejecting a budget request.
    * The decision is made based on specific criteria, such as available funds.
    *
    * @return string The result of the budget approval process.
    */
    protected function approveOrReject(): string
    {
        return "Budget request rejected.\n";
    }
}
