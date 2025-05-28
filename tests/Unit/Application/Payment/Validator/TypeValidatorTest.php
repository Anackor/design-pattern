<?php

namespace App\Tests\Application\Payment\Validator;

use PHPUnit\Framework\TestCase;
use App\Application\Payment\Validator\TypeValidator;
use App\Application\Payment\DTO\PaymentRequest;

class TypeValidatorTest extends TestCase
{
    public function testValidTypeReturnsSuccess()
    {
        $validator = new TypeValidator();
        $request = new PaymentRequest(100, 'credit_card', ['cardNumber' => '4111111111111111']);

        $result = $validator->handle($request);

        $this->assertTrue($result->isSuccess());
    }

    public function testInvalidTypeReturnsFailure()
    {
        $validator = new TypeValidator();
        $request = new PaymentRequest(100, 'cash', []);

        $result = $validator->handle($request);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Invalid payment type.', $result->getMessage());
    }
}
