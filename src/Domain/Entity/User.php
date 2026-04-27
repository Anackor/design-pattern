<?php

namespace App\Domain\Entity;

use App\Domain\Enum\UserRole;
use App\Domain\Flyweight\Country;
use App\Domain\Flyweight\UserType;
use App\Shared\ValueObject\Email;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', unique: false)]
    private string $country;

    #[ORM\Column(type: 'string', unique: false)]
    private string $type;

    #[ORM\OneToOne(mappedBy: 'user_id', cascade: ['persist', 'remove'])]
    private ?UserProfile $userProfile = null;

    #[ORM\Column(enumType: UserRole::class)]
    private UserRole $role;

    /**
     * @var Collection<int, UserOrders>
     */
    #[ORM\OneToMany(targetEntity: UserOrders::class, mappedBy: 'user_id', orphanRemoval: true)]
    private Collection $userOrders;

    /**
     * @var Collection<int, Document>
     */
    #[ORM\OneToMany(targetEntity: Document::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $documents;

    public function __construct()
    {
        $this->userOrders = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->country = 'unknown';
        $this->type = 'standard';
        $this->role = UserRole::USER;
    }

    public static function register(
        string $name,
        string $email,
        UserRole $role = UserRole::USER,
        string $country = 'unknown',
        string $type = 'standard'
    ): self {
        $user = new self();
        $user->setName($name);
        $user->setEmail($email);
        $user->setRole($role);
        $user->setCountry($country);
        $user->setType($type);

        return $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getCountry(): string
    {
        return $this->country;
    }
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Setters with Fluent Interface
     */
    public function setName(string $name): self
    {
        $this->name = $this->normalizeName($name);

        // Using Fluent Interface enables chaining multiple setter calls in a single statement.
        // Follow the usage example on src/DataFixtures/UserFixtures::load()
        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = Email::fromString($email)->value();

        return $this;
    }

    public function setCountry(string $country): self
    {
        $this->country = $this->normalizeCountry($country);

        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $this->normalizeType($type);

        return $this;
    }

    public function getUserProfile(): ?UserProfile
    {
        return $this->userProfile;
    }

    public function setUserProfile(UserProfile $userProfile): static
    {
        // set the owning side of the relation if necessary
        if ($userProfile->getUser() !== $this) {
            $userProfile->setUser($this);
        }

        $this->userProfile = $userProfile;

        return $this;
    }

    /**
     * @return Collection<int, UserOrders>
     */
    public function getUserOrders(): Collection
    {
        return $this->userOrders;
    }

    public function addUserOrder(UserOrders $userOrder): static
    {
        if (!$this->userOrders->contains($userOrder)) {
            $this->userOrders->add($userOrder);
            $userOrder->setUserId($this);
        }

        return $this;
    }

    public function removeUserOrder(UserOrders $userOrder): static
    {
        if ($this->userOrders->removeElement($userOrder)) {
            // set the owning side to null (unless already changed)
            if ($userOrder->getUserId() === $this) {
                $userOrder->setUserId(null);
            }
        }

        return $this;
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function setRole(UserRole $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setUser($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): static
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getUser() === $this) {
                $document->setUser(null);
            }
        }

        return $this;
    }

    private function normalizeName(string $name): string
    {
        $normalized = trim($name);

        if ('' === $normalized) {
            throw new \InvalidArgumentException('User name cannot be empty.');
        }

        return $normalized;
    }

    private function normalizeCountry(string $country): string
    {
        return Country::fromName($country)->getName();
    }

    private function normalizeType(string $type): string
    {
        return UserType::fromString($type)->getType();
    }
}
