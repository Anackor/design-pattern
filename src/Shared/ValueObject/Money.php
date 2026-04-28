<?php

namespace App\Shared\ValueObject;

final readonly class Money
{
    private function __construct(
        private int $amountInCents,
        private string $currency
    ) {}

    public static function fromFloat(float $amount, string $currency = 'EUR'): self
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Money amount cannot be negative.');
        }

        $normalizedCurrency = self::normalizeCurrency($currency);

        return new self((int) round($amount * 100), $normalizedCurrency);
    }

    public static function fromDecimalString(string $amount, string $currency = 'EUR'): self
    {
        $normalizedAmount = trim($amount);

        if (!preg_match('/^(?:0|[1-9]\d*)(?:\.\d{1,2})?$/', $normalizedAmount)) {
            throw new \InvalidArgumentException('Money amount must be a non-negative decimal with up to 2 decimals.');
        }

        [$units, $decimals] = array_pad(explode('.', $normalizedAmount, 2), 2, '0');

        return new self(
            ((int) $units * 100) + (int) str_pad(substr($decimals, 0, 2), 2, '0'),
            self::normalizeCurrency($currency)
        );
    }

    public static function zero(string $currency = 'EUR'): self
    {
        return self::fromFloat(0.0, $currency);
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function amountInCents(): int
    {
        return $this->amountInCents;
    }

    public function toFloat(): float
    {
        return $this->amountInCents / 100;
    }

    public function toDecimalString(): string
    {
        return sprintf('%d.%02d', intdiv($this->amountInCents, 100), $this->amountInCents % 100);
    }

    public function toDisplayString(): string
    {
        $formatted = $this->toDecimalString();

        return rtrim(rtrim($formatted, '0'), '.');
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amountInCents + $other->amountInCents, $this->currency);
    }

    public function multiply(float $factor): self
    {
        if ($factor < 0) {
            throw new \InvalidArgumentException('Money multiplier cannot be negative.');
        }

        return new self((int) round($this->amountInCents * $factor), $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amountInCents === $other->amountInCents
            && $this->currency === $other->currency;
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException(sprintf(
                'Money currencies must match. Got %s and %s.',
                $this->currency,
                $other->currency
            ));
        }
    }

    private static function normalizeCurrency(string $currency): string
    {
        $normalizedCurrency = strtoupper(trim($currency));

        if ('' === $normalizedCurrency) {
            throw new \InvalidArgumentException('Money currency cannot be empty.');
        }

        return $normalizedCurrency;
    }
}
