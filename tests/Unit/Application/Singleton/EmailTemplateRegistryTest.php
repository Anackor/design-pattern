<?php

namespace App\Tests\Application\tion\Singleton;

use App\Application\Singleton\EmailTemplateRegistry;
use PHPUnit\Framework\TestCase;

/**
 * While this test verifies expected behavior for invalid template keys,
 * it's important to note that relying on static methods in Singleton patterns
 * makes testing harder due to global state and lack of dependency injection.
 * For this reason, using Singletons is generally discouraged in favor of more testable designs.
 */
class EmailTemplateRegistryTest extends TestCase
{
    protected function setUp(): void
    {
        EmailTemplateRegistry::getInstance()->reset();
    }

    public function testGetReturnsCorrectTemplatePath(): void
    {
        $registry = EmailTemplateRegistry::getInstance();

        $templatePath = $registry->get('welcome');

        $this->assertEquals('emails/welcome.html.twig', $templatePath);
    }

    public function testGetThrowsExceptionOnInvalidKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Template key 'invalid_key' not found.");

        $registry = EmailTemplateRegistry::getInstance();
        $registry->get('invalid_key');
    }
}
