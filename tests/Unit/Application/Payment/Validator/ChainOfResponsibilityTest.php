<?php

namespace App\Tests\Application\Payment\Validator;

use PHPUnit\Framework\TestCase;
use App\Application\Payment\Validator\TypeValidator;
use App\Application\Payment\Validator\CardValidator;
use App\Application\Payment\Validator\AmountValidator;
use App\Application\Payment\DTO\PaymentRequest;

class ChainOfResponsibilityTest extends TestCase
{
    public function testValidChainReturnsSuccess()
    {
        $typeValidator = new TypeValidator();
        $cardValidator = new CardValidator();
        $amountValidator = new AmountValidator();

        $typeValidator->setNext($cardValidator)->setNext($amountValidator);

        $request = new PaymentRequest(100, 'credit_card', ['cardNumber' => '4111111111111111']);
        $result = $typeValidator->handle($request);

        $this->assertTrue($result->isSuccess());
    }

    public function testChainStopsOnFirstInvalid()
    {
        $typeValidator = new TypeValidator();
        $cardValidator = new CardValidator();
        $amountValidator = new AmountValidator();

        $typeValidator->setNext($cardValidator)->setNext($amountValidator);

        // Invalid type will stop the chain immediately
        $request = new PaymentRequest(100, 'bitcoin', ['cardNumber' => '4111111111111111']);
        $result = $typeValidator->handle($request);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Invalid payment type.', $result->getMessage());
    }
}
