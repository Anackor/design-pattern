<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'document')]
class Document
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, DocumentVersion>
     */
    #[ORM\OneToMany(mappedBy: 'document', targetEntity: DocumentVersion::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $versions;

    public function __construct(string $title, User $user)
    {
        $this->assertValidTitle($title);

        $this->title = $title;
        $this->user = $user;
        $this->versions = new ArrayCollection();
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
     * @return Collection<int, DocumentVersion>
     */
    public function getVersions(): Collection
    {
        return $this->versions;
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
        if (!$this->versions->contains($version)) {
            if ($version->getDocument() !== $this) {
                throw new \LogicException('Document version belongs to a different document.');
            }

            $this->versions->add($version);
        }

        return $this;
    }

    private function assertValidTitle(string $title): void
    {
        if ('' === trim($title)) {
            throw new \InvalidArgumentException('Document title cannot be empty.');
        }
    }
}
