<?php

namespace App\Tests\Unit\Application\Payment\Validator;

use PHPUnit\Framework\TestCase;
use App\Application\Payment\Validator\AmountValidator;
use App\Application\Payment\DTO\PaymentRequest;

class AmountValidatorTest extends TestCase
{
    public function testValidAmountReturnsSuccess()
    {
        $validator = new AmountValidator();
        $request = new PaymentRequest(100, 'credit_card', ['cardNumber' => '4111111111111111']);

        $result = $validator->handle($request);

        $this->assertTrue($result->isSuccess());
    }

    public function testZeroAmountReturnsFailure()
    {
        $validator = new AmountValidator();
        $request = new PaymentRequest(0, 'credit_card', ['cardNumber' => '4111111111111111']);

        $result = $validator->handle($request);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Invalid payment amount.', $result->getMessage());
    }
}
