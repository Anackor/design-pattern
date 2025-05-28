<?php

namespace App\Tests\Application\Payment\Validator;

use PHPUnit\Framework\TestCase;
use App\Application\Payment\Validator\CardValidator;
use App\Application\Payment\DTO\PaymentRequest;

class CardValidatorTest extends TestCase
{
    public function testValidCardReturnsSuccess()
    {
        $validator = new CardValidator();
        $request = new PaymentRequest(100, 'credit_card', ['cardNumber' => '4111111111111111']);

        $result = $validator->handle($request);

        $this->assertTrue($result->isSuccess());
    }

    public function testInvalidCardReturnsFailure()
    {
        $validator = new CardValidator();
        $request = new PaymentRequest(100, 'credit_card', ['cardNumber' => '123']);

        $result = $validator->handle($request);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Invalid or missing credit card number.', $result->getMessage());
    }
}
