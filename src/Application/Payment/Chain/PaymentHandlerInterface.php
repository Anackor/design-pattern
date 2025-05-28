<?php

namespace App\Application\Payment\Chain;

use App\Application\Payment\DTO\PaymentRequest;
use App\Application\Payment\DTO\PaymentResult;

/**
 * Represents a handler in a chain of responsibility.
 * Each concrete handler must implement logic to decide whether to process the request
 * or delegate it to the next handler in the chain.
 */
interface PaymentHandlerInterface
{
    public function setNext(PaymentHandlerInterface $handler): PaymentHandlerInterface;
    public function handle(PaymentRequest $request): PaymentResult;
}
