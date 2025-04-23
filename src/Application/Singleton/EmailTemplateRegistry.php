<?php

namespace App\Application\Singleton;

/**
 * EmailTemplateRegistry is an implementation of the Singleton pattern.
 *
 * It acts as a centralized registry of email template paths used throughout the application.
 * By ensuring only one instance exists, the registry provides global access to predefined
 * template mappings without requiring dependency injection or redundant instantiations.
 *
 * Benefits of using the Singleton pattern in this context:
 * - Centralized management of email templates, improving maintainability.
 * - Global access from any part of the application without passing it through constructors.
 * - Prevents duplication or inconsistent registration of template keys.
 *
 * Note:
 * While Singleton can be convenient, it introduces global state that can complicate testing
 * and reduce flexibility. Consider alternatives such as dependency injection when testability
 * or scalability is a priority.
 */
class EmailTemplateRegistry
{
    private static ?self $instance = null;
    private array $templates;

    private function __construct()
    {
        $this->templates = [
            'welcome' => 'emails/welcome.html.twig',
            'reset_password' => 'emails/reset_password.html.twig',
        ];
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function get(string $key): string
    {
        return $this->templates[$key] ?? throw new \InvalidArgumentException("Template key '{$key}' not found.");
    }

    public function reset(): void
    {
        self::$instance = null;
    }
}
