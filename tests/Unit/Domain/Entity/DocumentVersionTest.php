<?php

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Document;
use App\Domain\Entity\DocumentVersion;
use App\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

class DocumentVersionTest extends TestCase
{
    public function testVersionExposesMetadata(): void
    {
        $document = new Document('Contract', User::register('Doc Owner', 'doc-owner@example.com'));
        $version = new DocumentVersion($document, 'Initial content');

        $this->assertNull($version->getId());
        $this->assertSame($document, $version->getDocument());
        $this->assertSame('Initial content', $version->getContent());
        $this->assertSame(6, strlen($version->getVersionCode()));
        $this->assertInstanceOf(\DateTimeImmutable::class, $version->getCreatedAt());
    }

    public function testVersionRejectsEmptyContent(): void
    {
        $document = new Document('Contract', User::register('Doc Owner', 'doc-owner@example.com'));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Document content cannot be empty.');

        new DocumentVersion($document, '   ');
    }
}
