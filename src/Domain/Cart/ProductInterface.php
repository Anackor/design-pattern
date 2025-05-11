<?php

namespace App\Domain\Cart;

/**
 * ProductInterface represents the common contract in the Composite pattern.
 * Both single products and bundles implement this interface so that they can be treated uniformly.
 */
interface ProductInterface
{
    public function getName(): string;
    public function getPrice(): float;
}
