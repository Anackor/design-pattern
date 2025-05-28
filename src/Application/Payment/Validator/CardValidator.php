<?php

namespace App\Application\Payment\Validator;

use App\Application\Payment\Chain\AbstractPaymentHandler;
use App\Application\Payment\DTO\PaymentRequest;
use App\Application\Payment\DTO\PaymentResult;

/**
 * This handler validates card-specific fields when the payment type requires them.
 * It shows selective validation logic based on context,
 * a common practice when different handlers in the chain deal with different aspects.
 */
class CardValidator extends AbstractPaymentHandler
{
    public function handle(PaymentRequest $request): PaymentResult
    {
        if ($request->getPaymentMethod() === 'credit_card') {
            $cardNumber = $request->getPaymentData()['cardNumber'];

            if (empty($cardNumber) || strlen($cardNumber) !== 16 || !ctype_digit($cardNumber)) {
                return PaymentResult::failure('Invalid or missing credit card number.');
            }
        }

        return $this->next($request);
    }
}
