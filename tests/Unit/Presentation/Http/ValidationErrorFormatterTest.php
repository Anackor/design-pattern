<?php

namespace App\Tests\Unit\Presentation\Http;

use App\Presentation\Http\ValidationErrorFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationErrorFormatterTest extends TestCase
{
    public function testFormatProjectsViolationsIntoStableHttpDetails(): void
    {
        $formatter = new ValidationErrorFormatter();

        $formatted = $formatter->format(new ConstraintViolationList([
            new ConstraintViolation('Phone cannot be empty.', null, [], null, 'phone', null),
            new ConstraintViolation('Invalid payload.', null, [], null, '', null),
        ]));

        $this->assertSame([
            ['field' => 'phone', 'message' => 'Phone cannot be empty.'],
            ['field' => 'payload', 'message' => 'Invalid payload.'],
        ], $formatted);
    }
}
