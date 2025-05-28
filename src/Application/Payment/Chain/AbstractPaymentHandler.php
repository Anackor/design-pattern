<?php

namespace App\Application\Payment\Chain;

use App\Application\Payment\DTO\PaymentRequest;
use App\Application\Payment\DTO\PaymentResult;

/**
 * Provides a base implementation of the chain link.
 * Stores a reference to the next handler and handles delegation logic.
 * Subclasses should override the `handle` method to implement custom behavior
 * while optionally calling the next handler in the chain.
 */
abstract class AbstractPaymentHandler implements PaymentHandlerInterface
{
    private ?PaymentHandlerInterface $next = null;

    public function setNext(PaymentHandlerInterface $handler): PaymentHandlerInterface
    {
        $this->next = $handler;
        return $handler;
    }

    public function handle(PaymentRequest $request): PaymentResult
    {
        if ($this->next) {
            return $this->next->handle($request);
        }

        return PaymentResult::success();
    }

    protected function next(PaymentRequest $request): PaymentResult
    {
        if ($this->next) {
            return $this->next->handle($request);
        }

        return PaymentResult::success();
    }
}
