<?php

namespace App\Application\BuilderUserProfile;

use App\Application\DTO\UserProfileDTO;
use App\Domain\Builder\UserProfileBuilder;
use App\Domain\Entity\UserProfile;
use App\Domain\Repository\UserRepositoryInterface as UserRepository;
use App\Domain\Repository\UserProfileRepositoryInterface as UserProfileRepository;
use Psr\Log\LoggerInterface;

/**
 * CreateUserProfileHandler coordinates a full application use case: fetch a user,
 * validate the incoming data, build a domain object and persist it.
 *
 * That sequence makes it a valuable place to add teaching-oriented observability.
 * The handler therefore emits logs at the meaningful transitions of the flow:
 * start, not-found outcome, invalid input and successful persistence.
 *
 * The context deliberately keeps only identifiers and missing field names.
 * Addresses, birth dates and phone numbers are intentionally excluded from logs.
 */
class CreateUserProfileHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserProfileRepository $userProfileRepository,
        private LoggerInterface $logger
    ) {}

    public function handle(UserProfileDTO $dto): ?UserProfile
    {
        $context = [
            'use_case' => self::class,
            'user_id' => $dto->userId,
        ];

        // Logging the start lets us distinguish "never called" from "called but failed early".
        $this->logger->info('user_profile.create.started', $context);

        $user = $this->userRepository->registeredUserOfId($dto->userId);
        if (!$user) {
            $this->logger->warning('user_profile.create.user_not_found', $context);

            return null;
        }

        if (null === $dto->phone || null === $dto->address || null === $dto->birthdate) {
            $this->logger->warning('user_profile.create.invalid_payload', $context + [
                'missing_fields' => $this->missingFields($dto),
            ]);

            throw new \InvalidArgumentException('User profile data is incomplete.');
        }

        try {
            $profile = (new UserProfileBuilder())
                ->setUser($user)
                ->setPhone($dto->phone)
                ->setAddress($dto->address)
                ->setBirthdate($dto->birthdate)
                ->build();

            $this->userProfileRepository->addProfile($profile);
        } catch (\Throwable $exception) {
            $this->logger->error('user_profile.create.failed', $context + ['exception' => $exception]);

            throw $exception;
        }

        $this->logger->info('user_profile.create.succeeded', $context + [
            'profile_has_identifier' => null !== $profile->getId(),
        ]);

        return $profile;
    }

    /**
     * @return list<string>
     */
    private function missingFields(UserProfileDTO $dto): array
    {
        $missingFields = [];

        if (null === $dto->phone) {
            $missingFields[] = 'phone';
        }

        if (null === $dto->address) {
            $missingFields[] = 'address';
        }

        if (null === $dto->birthdate) {
            $missingFields[] = 'birthdate';
        }

        return $missingFields;
    }
}
