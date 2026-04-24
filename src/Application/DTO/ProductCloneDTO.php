<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Application\Validator\Constraints\CategoryExists;

class ProductCloneDTO
{
    #[Assert\NotNull]
    #[Assert\Type('integer')]
    public int $originalId;

    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public ?string $name = null;

    #[Assert\Type('float')]
    #[Assert\GreaterThanOrEqual(0)]
    public ?float $price = null;

    #[Assert\Type('string')]
    public ?string $description = null;

    // Validate that the given category ID exists in the database
    // We centralize validation logic inside the DTO to keep controllers and handlers clean and focused on orchestration
    #[Assert\Type('integer')]
    #[Assert\Positive]
    #[CategoryExists]
    public ?int $categoryId = null;

    public function __construct(
        int $originalId,
        ?string $name = null,
        ?float $price = null,
        ?string $description = null,
        ?int $categoryId = null
    ) {
        $this->originalId = $originalId;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->categoryId = $categoryId;
    }
}
