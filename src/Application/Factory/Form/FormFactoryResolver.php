<?php

namespace App\Application\Factory\Form;

use App\Domain\Form\FormFactoryInterface;
use InvalidArgumentException;

/**
 * The FormFactoryResolver is responsible for resolving and returning the appropriate form factory 
 * based on the requested type. This is a key part of the Abstract Factory pattern, where the goal 
 * is to provide a way to create families of related objects without specifying their concrete 
 * classes. In this case, it resolves which specific form factory (Html, Slack, Console) should 
 * be used based on the type provided.
 *
 * The Abstract Factory pattern is useful when there are multiple families of products (in this 
 * case, form components such as TextField, Button, and Checkbox) that need to be created for 
 * different environments (HTML, Slack, Console). By using this pattern, the system is flexible 
 * and easily extensible to support new types of form factories in the future without modifying 
 * the core logic of the application.
 *
 * The FormFactoryResolver acts as the central point of access to the form factories, making it 
 * easy to resolve the correct factory to generate platform-specific forms and components.
 */
class FormFactoryResolver
{
    public function __construct(
        private HtmlFormFactory $htmlFactory,
        private SlackFormFactory $slackFactory,
        private ConsoleFormFactory $consoleFactory
    ) {}

    public function get(string $type): FormFactoryInterface
    {
        return match ($type) {
            'html' => $this->htmlFactory,
            'slack' => $this->slackFactory,
            'console' => $this->consoleFactory,
            default => throw new InvalidArgumentException("Unknown factory type: $type"),
        };
    }
}
