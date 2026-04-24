<?php

namespace App\Application\Payment\Handler;

use App\Application\Payment\DTO\PaymentRequest;
use App\Application\Payment\DTO\PaymentResult;
use App\Application\Payment\Validator\AmountValidator;
use App\Application\Payment\Validator\CardValidator;
use App\Application\Payment\Validator\TypeValidator;

/**
 * The Chain of Responsibility pattern is a behavioral design pattern that allows you to pass a request along a chain of handlers.
 * Each handler in the chain can process the request or pass it on to the next handler if it cannot process it.
 *
 * This pattern is particularly useful when you have a series of steps that need to be executed in a particular order and some steps can be optional or conditional.
 * It promotes flexibility by allowing handlers to be added, removed, or re-ordered without changing the core logic.
 *
 * In this implementation, each validator (e.g., `CardNumberValidator`, `PaymentMethodValidator`, `BalanceValidator`) represents a handler in the chain.
 * The request starts with the first handler, which either processes the request or passes it to the next handler in the chain.
 * If any handler fails its validation, the request is rejected early and no further handlers are called.
 *
 * The benefits of using the Chain of Responsibility pattern here include:
 * 1. **Decoupling of concerns**: Each validator is independent and focused on a single responsibility, making the code easier to maintain and extend.
 * 2. **Extensibility**: New validators can be easily added to the chain without modifying existing ones, enabling easy updates to the validation process.
 * 3. **Flexible ordering**: Handlers can be re-ordered to change the validation flow as needed, providing more control over the validation process.
 */
class ProcessPaymentHandler
{
    public function __invoke(PaymentRequest $request): PaymentResult
    {
        $typeValidator = new TypeValidator();
        $cardValidator = new CardValidator();
        $amountValidator = new AmountValidator();

        $typeValidator->setNext($cardValidator);
        $cardValidator->setNext($amountValidator);

        return $typeValidator->handle($request);
    }
}
