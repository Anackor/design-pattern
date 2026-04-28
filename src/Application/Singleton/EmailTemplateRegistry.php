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
    private const DEFAULT_TEMPLATES = [
        'welcome' => 'emails/welcome.html.twig',
        'reset_password' => 'emails/reset_password.html.twig',
    ];

    private static ?self $instance = null;

    /**
     * @var array<string, string>
     */
    private array $templates;

    private function __construct()
    {
        $this->templates = self::DEFAULT_TEMPLATES;
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function get(string $key): string
    {
        $normalizedKey = $this->normalize($key, 'Template key');

        return $this->templates[$normalizedKey] ?? throw new \InvalidArgumentException("Template key '{$normalizedKey}' not found.");
    }

    public function has(string $key): bool
    {
        return isset($this->templates[$this->normalize($key, 'Template key')]);
    }

    public function register(string $key, string $templatePath): void
    {
        $this->templates[$this->normalize($key, 'Template key')] = $this->normalize($templatePath, 'Template path');
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->templates;
    }

    public function reset(): void
    {
        self::$instance = null;
    }

    private function __clone() {}

    public function __wakeup(): void
    {
        throw new \LogicException('EmailTemplateRegistry cannot be unserialized.');
    }

    private function normalize(string $value, string $label): string
    {
        $normalized = trim($value);

        if ('' === $normalized) {
            throw new \InvalidArgumentException(sprintf('%s cannot be empty.', $label));
        }

        return $normalized;
    }
}
