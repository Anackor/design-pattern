<?php

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Document;
use App\Domain\Entity\DocumentVersion;
use App\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function testDocumentRejectsEmptyTitle(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Document title cannot be empty.');

        new Document('   ', new User());
    }

    public function testGetLastVersionThrowsWhenDocumentHasNoVersions(): void
    {
        $document = new Document('Contract', new User());

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Document has no versions yet.');

        $document->getLastVersion();
    }

    public function testWithNewContentCreatesANewImmutableVersion(): void
    {
        $document = new Document('Contract', new User());
        $initialVersion = new DocumentVersion($document, 'Initial content');

        $updatedVersion = $initialVersion->withNewContent('Updated content');

        $this->assertNotSame($initialVersion, $updatedVersion);
        $this->assertSame('Initial content', $initialVersion->getContent());
        $this->assertSame('Updated content', $updatedVersion->getContent());
        $this->assertSame($updatedVersion, $document->getLastVersion());
    }
}
