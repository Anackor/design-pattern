# Executable Code Tour

This guide only points to commands and tests that are already verified in the current repository state. The goal is not to describe every pattern at once, but to give a short route that can be executed, inspected and understood step by step.

## Stop 1: Observability in action

Run:

```sh
make observability-demo
```

What this shows:

- A real command generates structured JSON logs.
- The Observer flow records user activity.
- The notification use case emits start and success events.
- The output is printed in the same execution, so you do not need extra tooling to inspect it.

Read next:

- `src/Command/ObservabilityDemoCommand.php`
- `src/Infrastructure/Observability/StructuredFileLogger.php`
- `src/Application/UserActivity/Observer/ActivityLogger.php`
- `src/Application/Notification/SendNotificationHandler.php`

## Stop 2: Builder flow from HTTP to domain

Run:

```sh
make test-unit PHPUNIT_ARGS="tests/Unit/Presentation/BuilderUserProfileControllerTest.php"
```

What this shows:

- A controller receives input and maps it into a DTO.
- Validation happens before the use case runs.
- The use case delegates object construction to a Builder.
- The domain model still enforces its own invariants.

Read next:

- `src/Presentation/BuilderUserProfileController.php`
- `src/Application/BuilderUserProfile/CreateUserProfileHandler.php`
- `src/Application/DTO/UserProfileDTO.php`
- `src/Domain/Builder/UserProfileBuilder.php`
- `src/Domain/Entity/UserProfile.php`

## Stop 3: Factory Method plus outbound observability

Run:

```sh
make test-unit PHPUNIT_ARGS="tests/Unit/Application/Handler/SendNotificationHandlerTest.php"
```

What this shows:

- The use case resolves a notification implementation through a factory.
- The selected outbound channel is logged as structured context.
- Sensitive payload is minimized before leaving an audit trail.
- Failure and non-exception warning paths are both observable.

Read next:

- `src/Application/Notification/SendNotificationHandler.php`
- `src/Application/Factory/NotificationFactory.php`
- `src/Application/DTO/NotificationRequestDTO.php`
- `src/Domain/Notification/NotificationInterface.php`

## Stop 4: Observer as a logging sidecar

Run:

```sh
make test-unit PHPUNIT_ARGS="tests/Unit/Application/UserActivity/ObserverTest.php"
```

What this shows:

- The subject emits an action without knowing who will react to it.
- One observer tracks metrics.
- Another observer transforms the event into a structured log.
- Observability stays decoupled from the main flow.

Read next:

- `src/Application/UserActivity/UserActionSubject.php`
- `src/Application/UserActivity/UserActionDispatcher.php`
- `src/Application/UserActivity/Observer/ActivityLogger.php`
- `src/Application/UserActivity/Observer/UserMetricsTracker.php`

## Suggested order

1. Run the observability demo first to see something tangible.
2. Open the Builder flow to follow a full request-to-domain path.
3. Move to notifications to see Factory Method plus outbound logging.
4. Finish with Observer to understand how observability can be attached as a side effect.

## Why this tour exists

The repository contains many patterns, but not all of them carry the same teaching value at the same time. This tour intentionally prioritizes examples that satisfy three conditions:

- they execute with a real command or focused test;
- they show more than one architectural layer at once;
- they help explain current phase-3 goals: confidence, coverage and observability.
