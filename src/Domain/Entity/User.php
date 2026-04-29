<?php

namespace App\Domain\Entity;

use App\Domain\Enum\UserRole;
use App\Domain\Flyweight\Country;
use App\Domain\Flyweight\UserType;
use App\Shared\ValueObject\Email;

class User
{
    private ?int $id = null;

    private string $name;

    private string $email;

    private string $country;

    private string $type;

    private ?UserProfile $userProfile = null;

    private UserRole $role;

    /**
     * @var iterable<int, UserOrders>
     */
    private iterable $userOrders;

    /**
     * @var iterable<int, Document>
     */
    private iterable $documents;

    public function __construct()
    {
        $this->userOrders = [];
        $this->documents = [];
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
     * @return list<UserOrders>
     */
    public function getUserOrders(): array
    {
        return $this->iterableToArray($this->userOrders);
    }

    public function addUserOrder(UserOrders $userOrder): static
    {
        if (!$this->containsUserOrder($userOrder)) {
            $this->userOrders = $this->appendUserOrder($this->userOrders, $userOrder);
            $userOrder->setUserId($this);
        }

        return $this;
    }

    public function removeUserOrder(UserOrders $userOrder): static
    {
        if ($this->containsUserOrder($userOrder)) {
            $this->userOrders = $this->withoutUserOrder($this->userOrders, $userOrder);

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
     * @return list<Document>
     */
    public function getDocuments(): array
    {
        return $this->iterableToArray($this->documents);
    }

    public function addDocument(Document $document): static
    {
        if (!$this->containsDocument($document)) {
            $this->documents = $this->appendDocument($this->documents, $document);
            $document->setUser($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): static
    {
        if ($this->containsDocument($document)) {
            $this->documents = $this->withoutDocument($this->documents, $document);

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

    private function containsUserOrder(UserOrders $candidate): bool
    {
        foreach ($this->userOrders as $userOrder) {
            if ($userOrder === $candidate) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param iterable<int, UserOrders> $userOrders
     */
    private function appendUserOrder(iterable $userOrders, UserOrders $userOrder): iterable
    {
        if (is_object($userOrders) && method_exists($userOrders, 'add')) {
            $userOrders->add($userOrder);

            return $userOrders;
        }

        $buffer = $this->iterableToArray($userOrders);
        $buffer[] = $userOrder;

        return $buffer;
    }

    /**
     * @param iterable<int, UserOrders> $userOrders
     */
    private function withoutUserOrder(iterable $userOrders, UserOrders $candidate): iterable
    {
        if (is_object($userOrders) && method_exists($userOrders, 'removeElement')) {
            $userOrders->removeElement($candidate);

            return $userOrders;
        }

        return array_values(array_filter(
            $this->iterableToArray($userOrders),
            static fn(UserOrders $userOrder): bool => $userOrder !== $candidate
        ));
    }

    private function containsDocument(Document $candidate): bool
    {
        foreach ($this->documents as $document) {
            if ($document === $candidate) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param iterable<int, Document> $documents
     */
    private function appendDocument(iterable $documents, Document $document): iterable
    {
        if (is_object($documents) && method_exists($documents, 'add')) {
            $documents->add($document);

            return $documents;
        }

        $buffer = $this->iterableToArray($documents);
        $buffer[] = $document;

        return $buffer;
    }

    /**
     * @param iterable<int, Document> $documents
     */
    private function withoutDocument(iterable $documents, Document $candidate): iterable
    {
        if (is_object($documents) && method_exists($documents, 'removeElement')) {
            $documents->removeElement($candidate);

            return $documents;
        }

        return array_values(array_filter(
            $this->iterableToArray($documents),
            static fn(Document $document): bool => $document !== $candidate
        ));
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
