<?php

namespace App\Application\Payment\DTO;

/**
 * Encapsulates input data passed through the chain.
 * Acts as a unified representation of the request context,
 * allowing each handler to inspect or act upon it.
 */
class PaymentRequest
{
    public function __construct(
        private readonly float $amount,
        private readonly string $paymentMethod,
        private readonly array $paymentData, // e.g. ['cardNumber' => '4111...']
    ) {}

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function getPaymentData(): array
    {
        return $this->paymentData;
    }
}
