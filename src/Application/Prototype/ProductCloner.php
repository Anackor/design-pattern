<?php

namespace App\Application\Prototype;

use App\Domain\Entity\Product;

/**
 * The Prototype Pattern allows us to clone an existing object and create a new instance with modifications, rather than building it from scratch.
 * 
 * In this case, ProductCloner is responsible for cloning Product Entities, enabling the creation of new products based on an existing one.
 * 
 * Benefits of using the Prototype Pattern:
 * - Efficiency: Instead of creating new products from scratch, we clone and apply changes. This is especially beneficial when creating complex 
 * objects that share many attributes with an existing object.
 * - Consistency: The cloned object starts with the same attributes as the original, ensuring consistency in object creation and reducing the chances
 * of errors or discrepancies.
 * - Flexibility: Cloning allows you to quickly generate variations of an object while maintaining control over the parts that differ.
 */
class ProductCloner
{
    public function cloneWithOverrides(Product $original, array $overrides): Product
    {
        $clone = $original->clone();

        if (isset($overrides['name'])) {
            $clone->setName($overrides['name']);
        }

        if (isset($overrides['price'])) {
            $clone->setPrice($overrides['price']);
        }

        if (isset($overrides['description'])) {
            $clone->setDescription($overrides['description']);
        }

        if (isset($overrides['category'])) {
            $clone->setCategory($overrides['category']);
        }

        return $clone;
    }
}
