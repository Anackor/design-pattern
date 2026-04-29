<?php

namespace App\Domain\Entity;

class DocumentVersion
{
    private ?int $id = null;

    private Document $document;

    private string $content;

    private string $versionCode;

    private \DateTimeImmutable $createdAt;

    public function __construct(Document $document, string $content)
    {
        $this->assertValidContent($content);

        $this->document = $document;
        $this->content = $content;
        $this->createdAt = new \DateTimeImmutable();
        $this->versionCode = substr(md5($content . microtime()), 0, 6);

        $document->addVersion($this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocument(): Document
    {
        return $this->document;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getVersionCode(): string
    {
        return $this->versionCode;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function withNewContent(string $content): self
    {
        return new self($this->document, $content);
    }

    private function assertValidContent(string $content): void
    {
        if ('' === trim($content)) {
            throw new \InvalidArgumentException('Document content cannot be empty.');
        }
    }
}
