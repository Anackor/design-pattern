<?php

namespace App\Application\Approval;

/**
 * Class RequestApproval
 *
 * The abstract class that defines the template method for processing a request.
 *
 * This class is a part of the Template Method design pattern. It defines the structure
 * of the algorithm for processing a request, ensuring that the overall flow is consistent
 * across different types of requests (e.g., vacation requests, budget requests).
 *
 * The template method, `processRequest()`, controls the flow of the process. It invokes
 * common steps like receiving the request, followed by the more specific steps of reviewing
 * and making a decision about the request. The detailed implementations of these steps
 * are left to the subclasses through abstract methods.
 *
 * The primary benefit of using the Template Method is that it allows you to define the
 * skeleton of an algorithm in a method, allowing subclasses to implement specific steps
 * of the algorithm without changing the overall structure. This helps in promoting
 * code reuse, consistency, and flexibility. Each subclass can focus on its specific logic
 * while inheriting a common flow.
 */
abstract class RequestApproval
{
    /**
     * The template method that defines the flow of the request approval process.
     * This method outlines the high-level steps and leaves specific implementation details
     * to the subclasses.
     *
     * @return string The result of the approval process (e.g., approved or rejected).
     */
    public function processRequest(): string
    {
        $this->receiveRequest();
        $this->reviewRequest();
        return $this->approveOrReject();
    }

    /**
     * A common step for receiving the request. This method will be executed the same
     * way for all types of requests, regardless of their specific implementation.
     */
    protected function receiveRequest(): void {}

    /**
     * Abstract method for reviewing the request. Subclasses must implement this to
     * define how the request should be reviewed.
     */
    abstract protected function reviewRequest(): bool;

    /**
     * Abstract method for approving or rejecting the request. Subclasses must implement
     * this to define how the decision is made.
     *
     * @return string The result of the decision-making (approved or rejected).
     */
    abstract protected function approveOrReject(): string;
}
