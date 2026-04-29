<?php

namespace App\Domain\Entity;

class Document
{
    private ?int $id = null;

    private ?string $title = null;

    private ?User $user = null;

    /**
     * @var iterable<int, DocumentVersion>
     */
    private iterable $versions;

    public function __construct(string $title, User $user)
    {
        $this->assertValidTitle($title);

        $this->title = $title;
        $this->user = $user;
        $this->versions = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->assertValidTitle($title);
        $this->title = $title;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return list<DocumentVersion>
     */
    public function getVersions(): array
    {
        return $this->iterableToArray($this->versions);
    }

    public function getLastVersion(): DocumentVersion
    {
        $lastVersion = null;

        foreach ($this->versions as $version) {
            if ($lastVersion === null || $version->getCreatedAt() >= $lastVersion->getCreatedAt()) {
                $lastVersion = $version;
            }
        }

        if (!$lastVersion instanceof DocumentVersion) {
            throw new \LogicException('Document has no versions yet.');
        }

        return $lastVersion;
    }

    public function addVersion(DocumentVersion $version): static
    {
        if (!$this->containsVersion($version)) {
            if ($version->getDocument() !== $this) {
                throw new \LogicException('Document version belongs to a different document.');
            }

            $this->versions = $this->appendVersion($this->versions, $version);
        }

        return $this;
    }

    private function assertValidTitle(string $title): void
    {
        if ('' === trim($title)) {
            throw new \InvalidArgumentException('Document title cannot be empty.');
        }
    }

    private function containsVersion(DocumentVersion $candidate): bool
    {
        foreach ($this->versions as $version) {
            if ($version === $candidate) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param iterable<int, DocumentVersion> $versions
     */
    private function appendVersion(iterable $versions, DocumentVersion $version): iterable
    {
        if (is_object($versions) && method_exists($versions, 'add')) {
            $versions->add($version);

            return $versions;
        }

        $buffer = $this->iterableToArray($versions);
        $buffer[] = $version;

        return $buffer;
    }

    /**
     * @template T of object
     *
     * @param iterable<int, T> $items
     *
     * @return list<T>
     */
    private function iterableToArray(iterable $items): array
    {
        if (is_array($items)) {
            return array_values($items);
        }

        return array_values(iterator_to_array($items, false));
    }
}
