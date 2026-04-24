<?php

namespace App\Application\Payment\Validator;

use App\Application\Payment\Chain\AbstractPaymentHandler;
use App\Application\Payment\DTO\PaymentRequest;
use App\Application\Payment\DTO\PaymentResult;

/**
 * The final step in the chain, this validator ensures the payment amount is valid.
 * Represents a terminal node in the chain that may conclude the validation process.
 */
class AmountValidator extends AbstractPaymentHandler
{
    public function handle(PaymentRequest $request): PaymentResult
    {
        if ($request->getAmount() <= 0) {
            return PaymentResult::failure('Invalid payment amount.');
        }

        return PaymentResult::success();
    }
}
