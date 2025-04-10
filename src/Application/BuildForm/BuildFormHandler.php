<?php

namespace App\Application\BuildForm;

use App\Application\DTO\FormRequestDTO;
use App\Application\Factory\Form\FormFactoryResolver;

class BuildFormHandler
{
    public function __construct(
        private FormFactoryResolver $formFactoryResolver
    ) {}

    public function handle(FormRequestDTO $dto): string
    {
        $factory = $this->formFactoryResolver->get($dto->type);
        $textField = $factory->createTextField($dto->textFieldLabel);
        $checkbox = $factory->createCheckbox($dto->checkboxLabel);
        $button = $factory->createButton($dto->buttonLabel);

        return $textField->render() . PHP_EOL . $checkbox->render() . PHP_EOL . $button->render();
    }
}
