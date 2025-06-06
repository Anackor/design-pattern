<?php

namespace App\Application\Approval;

/**
 * Class VacationRequestApproval
 *
 * This concrete class implements the specific steps for processing a vacation request.
 * It overrides the abstract methods defined in the RequestApproval template class 
 * to provide custom logic for reviewing and approving vacation requests.
 *
 * In the reviewRequest method, we might check if the employee has enough vacation days left 
 * and if the timing of the request aligns with company policies. In the approveOrReject 
 * method, we determine whether the vacation is approved based on these criteria.
 *
 * The Template Method design pattern allows for the reusability of the common steps (like 
 * receiving the request), while enabling the customization of the review and decision 
 * steps for specific request types like vacation requests.
 */
class VacationRequestApproval extends RequestApproval
{
    /**
     * Implementation of reviewing a vacation request.
     * The specific logic for vacation request review is defined here.
     */
    protected function reviewRequest(): bool
    {
        return true;
    }

    /**
     * Implementation of approving or rejecting a vacation request.
     * The decision is made based on specific criteria, such as available vacation days.
     *
     * @return string The result of the vacation approval process.
     */
    protected function approveOrReject(): string
    {
        return "Vacation request approved.\n";
    }
}
