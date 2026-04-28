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

        new Document('   ', User::register('Doc Owner', 'doc-owner@example.com'));
    }

    public function testGetLastVersionThrowsWhenDocumentHasNoVersions(): void
    {
        $document = new Document('Contract', User::register('Doc Owner', 'doc-owner@example.com'));

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Document has no versions yet.');

        $document->getLastVersion();
    }

    public function testWithNewContentCreatesANewImmutableVersion(): void
    {
        $document = new Document('Contract', User::register('Doc Owner', 'doc-owner@example.com'));
        $initialVersion = new DocumentVersion($document, 'Initial content');

        $updatedVersion = $initialVersion->withNewContent('Updated content');

        $this->assertNotSame($initialVersion, $updatedVersion);
        $this->assertSame('Initial content', $initialVersion->getContent());
        $this->assertSame('Updated content', $updatedVersion->getContent());
        $this->assertSame($updatedVersion, $document->getLastVersion());
    }

    public function testSetTitleAndGettersWorkForValidDocument(): void
    {
        $user = User::register('Doc Owner', 'doc-owner@example.com');
        $document = new Document('Contract', $user);

        $this->assertNull($document->getId());
        $this->assertSame('Contract', $document->getTitle());
        $this->assertSame($user, $document->getUser());

        $document->setTitle('Updated Contract');

        $this->assertSame('Updated Contract', $document->getTitle());
        $this->assertCount(0, $document->getVersions());
    }

    public function testAddVersionRejectsVersionFromDifferentDocument(): void
    {
        $owner = User::register('Doc Owner', 'doc-owner@example.com');
        $document = new Document('Contract', $owner);
        $anotherDocument = new Document('Invoice', $owner);
        $foreignVersion = new DocumentVersion($anotherDocument, 'Different content');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Document version belongs to a different document.');

        $document->addVersion($foreignVersion);
    }
}
