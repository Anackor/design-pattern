# Executable Code Tour

This guide only points to commands and tests that are already verified in the current repository state. The goal is not to describe every pattern at once, but to give a short route that can be executed, inspected and understood step by step.

## Two tracks

- Core path: start here if you want to review the repository as a small application with HTTP entrypoints, use cases, observability and persistence boundaries.
- Pattern gallery: use these extra stops afterwards if you want to study more isolated pattern examples without losing time in the first pass.

## Core path

### Stop 1: Observability in action

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

### Stop 2: Builder flow from HTTP to domain

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

### Stop 3: Functional HTTP boundary

Run:

```sh
make test-functional
```

What this shows:

- the Symfony kernel handles real HTTP requests in test mode;
- controllers are reached through routing, not direct method calls;
- DTO validation now runs with the framework validator, not with mocked results;
- malformed JSON and missing parameters return stable JSON error payloads;
- success and error responses now share one explicit envelope.

Read next:

- `tests/Functional/Support/JsonHttpKernelTestCase.php`
- `tests/Functional/Presentation/NotificationControllerFunctionalTest.php`
- `tests/Functional/Presentation/DocumentControllerFunctionalTest.php`
- `tests/Functional/Presentation/FileStorageControllerFunctionalTest.php`
- `tests/Functional/Presentation/FormControllerFunctionalTest.php`
- `src/Presentation/Http/ApiResponseFactory.php`
- `src/Presentation/Http/ValidationErrorFormatter.php`
- `src/Presentation/Http/JsonRequestDecoder.php`
- `src/Presentation/EventSubscriber/JsonApiExceptionSubscriber.php`
- `src/Presentation/NotificationController.php`

### Stop 4: Factory Method plus outbound observability

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

### Stop 5: Observer as a logging sidecar

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

## Pattern gallery

### Stop 6: Prototype in catalog cloning

Run:

```sh
make test-unit PHPUNIT_ARGS="tests/Unit/Application/Prototype/ProductClonerTest.php"
```

What this shows:

- A cloned product keeps the original untouched.
- Overrides stay explicit instead of leaking into a long constructor call.
- The pattern appears inside an application-oriented catalog use case, not as a disconnected toy example.

Read next:

- `src/Application/Prototype/ProductCloner.php`
- `src/Application/Prototype/ProductCloneOverrides.php`
- `src/Domain/Entity/Product.php`
- `tests/Unit/Application/Handler/CloneProductHandlerTest.php`

### Stop 7: Adapter at the file storage boundary

Run:

```sh
make test-functional PHPUNIT_ARGS="tests/Functional/Presentation/FileStorageControllerFunctionalTest.php"
```

What this shows:

- One HTTP contract can drive local, FTP or S3 storage behind the same application boundary.
- The controller only knows DTO validation and response shape, not storage implementation details.
- Adapter becomes easier to understand when seen through the functional tests that protect the boundary.

Read next:

- `src/Presentation/FileStorageController.php`
- `src/Application/File/UploadFileHandler.php`
- `src/Application/File/DownloadFileHandler.php`
- `src/Application/File/DeleteFileHandler.php`
- `src/Infrastructure/FileStorage/FileStorageResolver.php`
- `src/Infrastructure/FileStorage/LocalFileStorageAdapter.php`
- `src/Infrastructure/FileStorage/FtpStorageAdapter.php`
- `src/Infrastructure/FileStorage/AwsS3StorageAdapter.php`

### Stop 8: Flyweight as normalized shared state

Run:

```sh
make test-unit PHPUNIT_ARGS="tests/Unit/Domain/Flyweight/FlyweightFactoryTest.php"
```

What this shows:

- Repeated country and user type values are normalized before reuse.
- The factories make the memory-sharing idea visible without hiding the normalization rule.
- This is a good pattern stop to read after the core path because it is intentionally more didactic than infrastructural.

Read next:

- `src/Domain/Flyweight/Country.php`
- `src/Domain/Flyweight/CountryFlyweightFactory.php`
- `src/Domain/Flyweight/UserType.php`
- `src/Domain/Flyweight/UserTypeFlyweightFactory.php`

## Suggested order

1. Run the observability demo first to see something tangible.
2. Open the Builder flow to follow a full request-to-domain path.
3. Run the functional HTTP suite to see the same boundary exercised through the kernel.
4. Move to notifications to see Factory Method plus outbound logging.
5. Finish with Observer to understand how observability can be attached as a side effect.
6. Continue with Prototype, Adapter and Flyweight only after the core path is clear.

## Why this tour exists

The repository contains many patterns, but not all of them carry the same teaching value at the same time. The core path intentionally prioritizes examples that satisfy three conditions:

- they execute with a real command or focused test;
- they show more than one architectural layer at once;
- they help explain the current technical goals: confidence, observability and a more trustworthy HTTP boundary.

The optional gallery then broadens the tour with smaller, more pattern-centered examples once the main application route is already familiar.
