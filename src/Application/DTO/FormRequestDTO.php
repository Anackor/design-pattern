<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FormRequestDTO
{
    #[Assert\Choice(['html', 'slack', 'console'])]
    public string $type;

    #[Assert\Type("string")]
    public ?string $textFieldLabel = null;

    #[Assert\Type("string")]
    public ?string $checkboxLabel = null;

    #[Assert\Type("string")]
    public ?string $buttonLabel = null;

    public function __construct(string $type, ?string $textFieldLabel = null, ?string $checkboxLabel = null, ?string $buttonLabel = null)
    {
        $this->type = $type;
        $this->textFieldLabel = $textFieldLabel;
        $this->checkboxLabel = $checkboxLabel;
        $this->buttonLabel = $buttonLabel;
    }
}
