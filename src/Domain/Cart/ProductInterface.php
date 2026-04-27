<?php

namespace App\Domain\Cart;

use App\Shared\ValueObject\Money;

/**
 * ProductInterface represents the common contract in the Composite pattern.
 * Both single products and bundles implement this interface so that they can be treated uniformly.
 */
interface ProductInterface
{
    public function getName(): string;
    public function getPrice(): Money;
}
