<?php

namespace App\Application\Payment\Validator;

use App\Application\Payment\Chain\AbstractPaymentHandler;
use App\Application\Payment\DTO\PaymentRequest;
use App\Application\Payment\DTO\PaymentResult;

/**
 * First element in the responsibility chain.
 * This validator ensures the payment type is allowed and properly defined.
 * It demonstrates how each chain element performs its check before
 * delegating to the next one in the sequence.
 */
class TypeValidator extends AbstractPaymentHandler
{
    public function handle(PaymentRequest $request): PaymentResult
    {
        if (!in_array($request->getPaymentMethod(), ['credit_card', 'paypal', 'bank_transfer'], true)) {
            return PaymentResult::failure('Invalid payment type.');
        }

        return $this->next($request);
    }
}
