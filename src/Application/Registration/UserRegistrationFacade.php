<?php

namespace App\Application\Registration;

use App\Application\DTO\NotificationRequestDTO;
use App\Application\DTO\UserDataDTO;
use App\Application\Notification\SendNotificationHandler;
use App\Domain\Registration\UserRegistrationFacadeInterface;
use App\Application\Registration\Service\UserCreator;
use App\Application\Registration\Service\PasswordEncryptor;
use App\Application\Registration\Service\WelcomeEmailSender;
use App\Application\Registration\Service\UserLogger;
use App\Application\Service\UserService;
use App\Domain\Entity\User;
use App\Domain\Enum\NotificationChannel;

/**
 * Class UserRegistrationFacade
 *
 * This class implements the Facade design pattern to provide a unified and simplified interface
 * for user registration within the application.
 *
 * The **Facade Pattern** is a structural design pattern that provides a higher-level interface
 * over a set of subsystems, making them easier to use. Instead of exposing the complexities of
 * multiple components to the client, the facade encapsulates and coordinates them internally.
 *
 * ## Purpose of this Facade
 * Registering a user typically involves multiple steps:
 *  - Encrypting the user's password
 *  - Creating a user entity and persisting it
 *  - Sending a welcome email
 *  - Logging the registration event
 *
 * This class hides those steps behind a single `register` method, so the client code
 * (e.g., a controller or service) doesn't need to know the details of how each step is implemented.
 *
 * ## Benefits of using the Facade Pattern here
 *  - Reduces coupling between the client code and subsystem components
 *  - Improves maintainability and readability
 *  - Encourages separation of concerns by delegating logic to dedicated services
 *  - Allows internal implementation to change without affecting the external interface
 *
 * ## Example usage
 * ```
 * $facade = new UserRegistrationFacade(...);
 * $user = $facade->register([
 *     'email' => 'test@example.com',
 *     'password' => 'secure123',
 * ]);
 * ```
 *
 * @see \App\Domain\Registration\UserRegistrationFacadeInterface
 */
class UserRegistrationFacade implements UserRegistrationFacadeInterface
{
    public function __construct(
        private UserService $userService,
        private SendNotificationHandler $sendNotificationHandler
    ) {}

    public function register(UserDataDTO $userData): User
    {
        $user = $this->userService->createUser($userData->getName(), $userData->getEmail());

        $welcomeNotification = $this->buildWelcomeNotification($user);
        $this->sendNotificationHandler->handle($welcomeNotification);

        /**
         * This is where we would add calls to different handlers to complete the user registration process,
         * such as creating logs and exporting user data to a CSV file, then storing it on AWS using our FileStorageService.
         */

        return $user;
    }

    private function buildWelcomeNotification(User $user): NotificationRequestDTO
    {
        $subject = 'Welcome to our platform!';
        $message = sprintf(
            "Hello %s,\n\nThank you for registering. We're excited to have you with us!",
            $user->getEmail()
        );

        return new NotificationRequestDTO($subject, $message, $user->getEmail(), 'email');
    }
}
