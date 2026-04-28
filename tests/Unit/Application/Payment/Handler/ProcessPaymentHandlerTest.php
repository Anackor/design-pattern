<?php

namespace App\Tests\Unit\Application\Payment\Handler;

use App\Application\Payment\DTO\PaymentRequest;
use App\Application\Payment\Handler\ProcessPaymentHandler;
use PHPUnit\Framework\TestCase;

class ProcessPaymentHandlerTest extends TestCase
{
    public function testInvokeBuildsAndExecutesFullValidationChain(): void
    {
        $handler = new ProcessPaymentHandler();

        $result = $handler(new PaymentRequest(150, 'credit_card', ['cardNumber' => '4111111111111111']));

        $this->assertTrue($result->isSuccess());
        $this->assertNull($result->getMessage());
    }

    public function testInvokeStopsWhenCardNumberIsInvalid(): void
    {
        $handler = new ProcessPaymentHandler();

        $result = $handler(new PaymentRequest(150, 'credit_card', ['cardNumber' => '123']));

        $this->assertFalse($result->isSuccess());
        $this->assertSame('Invalid or missing credit card number.', $result->getMessage());
    }
}
