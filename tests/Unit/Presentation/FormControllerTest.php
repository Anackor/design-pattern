<?php

namespace App\Tests\Unit\Presentation;

use App\Application\BuildForm\BuildFormHandler;
use App\Presentation\FormController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FormControllerTest extends TestCase
{
    public function testBuildReturnsRenderedForm(): void
    {
        $handler = $this->createMock(BuildFormHandler::class);
        $handler->expects($this->once())->method('handle')->willReturn('<form>Rendered</form>');

        $controller = new FormController($handler, $this->createValidator());
        $response = $controller->build($this->jsonRequest([
            'type' => 'html',
            'textFieldLabel' => 'Email',
            'checkboxLabel' => 'Terms',
            'buttonLabel' => 'Submit',
        ]));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['form' => '<form>Rendered</form>'], json_decode((string) $response->getContent(), true));
    }

    public function testBuildReturnsValidationErrors(): void
    {
        $controller = new FormController(
            $this->createMock(BuildFormHandler::class),
            $this->createValidator($this->violationList('Invalid form payload.'))
        );

        $response = $controller->build($this->jsonRequest([]));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertStringContainsString('Invalid form payload.', (string) $response->getContent());
    }

    public function testBuildReturnsBadRequestWhenHandlerFails(): void
    {
        $handler = $this->createMock(BuildFormHandler::class);
        $handler->method('handle')->willThrowException(new \InvalidArgumentException('Unknown factory type: pdf'));

        $controller = new FormController($handler, $this->createValidator());
        $response = $controller->build($this->jsonRequest([
            'type' => 'pdf',
            'textFieldLabel' => 'Email',
            'checkboxLabel' => 'Terms',
            'buttonLabel' => 'Submit',
        ]));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame(['error' => 'Unknown factory type: pdf'], json_decode((string) $response->getContent(), true));
    }

    private function createValidator(?ConstraintViolationList $violations = null): ValidatorInterface
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn($violations ?? new ConstraintViolationList());

        return $validator;
    }

    private function jsonRequest(array $data): Request
    {
        return new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
    }

    private function violationList(string $message): ConstraintViolationList
    {
        return new ConstraintViolationList([
            new ConstraintViolation($message, null, [], null, 'payload', null),
        ]);
    }
}
