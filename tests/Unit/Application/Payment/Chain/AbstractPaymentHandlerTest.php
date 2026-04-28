<?php

namespace App\Tests\Unit\Application\Payment\Chain;

use App\Application\Payment\Chain\AbstractPaymentHandler;
use App\Application\Payment\Chain\PaymentHandlerInterface;
use App\Application\Payment\DTO\PaymentRequest;
use App\Application\Payment\DTO\PaymentResult;
use PHPUnit\Framework\TestCase;

class AbstractPaymentHandlerTest extends TestCase
{
    public function testHandleReturnsSuccessWhenThereIsNoNextHandler(): void
    {
        $handler = new class extends AbstractPaymentHandler {
            public function passToNext(PaymentRequest $request): PaymentResult
            {
                return $this->next($request);
            }
        };

        $result = $handler->handle(new PaymentRequest(100, 'paypal', []));

        $this->assertTrue($result->isSuccess());
        $this->assertNull($result->getMessage());
    }

    public function testProtectedNextDelegatesToConfiguredHandler(): void
    {
        $request = new PaymentRequest(100, 'paypal', []);
        $expected = PaymentResult::failure('Stopped by next handler.');

        $next = $this->createMock(PaymentHandlerInterface::class);
        $next->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($expected);

        $handler = new class extends AbstractPaymentHandler {
            public function passToNext(PaymentRequest $request): PaymentResult
            {
                return $this->next($request);
            }
        };

        $returnedHandler = $handler->setNext($next);

        $this->assertSame($next, $returnedHandler);
        $this->assertSame($expected, $handler->passToNext($request));
    }
}
