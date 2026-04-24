<?php

namespace App\Domain\Sort;

/**
 * The Strategy Pattern allows defining a family of algorithms, encapsulating each one, and making them interchangeable.
 * The client can choose the sorting algorithm at runtime, without modifying the class that performs the sorting.
 * This promotes flexibility and scalability, allowing easy extension of new sorting strategies without altering the existing code.
 */
interface SortStrategyInterface
{
    public function sort(array $items): array;
}
