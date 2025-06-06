# Project Name

## Introduction
This project is a Symfony-based API designed to showcase different design patterns for enterprise use. The goal is to demonstrate best practices and modular architecture while maintaining a clean and scalable codebase.

## Getting Started

### Prerequisites
Ensure you have the following installed on your system:
- Docker & Docker Compose
- PHP 8.2+
- Composer
- Symfony CLI (optional but recommended)
- Makefile (if using Linux/macOS)

### Installation

1. **Clone the repository**
   ```sh
   git clone https://github.com/your-repo/project-name.git
   cd project-name

2. **Start Docker Containers**
   ```sh
   make start

3. **Install PHP Dependencies (Inside Docker)**
   ```sh
   composer install

4. **Run Database Migrations**
   ```sh
   php bin/console doctrine:migrations:migrate

5. **Load Fixtures (Optional, for test data)**
   ```sh
   php bin/console doctrine:fixtures:load

6. **Start PHP Development Server**
   ```sh
   php -S 127.0.0.1:8000 -t public

7. **Check Tests**
   ```sh
   php ./vendor/bin/phpunit

## Project Structure
   ```sh
   project-name/
   ├── src/
   │   ├── Application/          # Application layer (DTOs, Handlers, Builders, etc.)
   │   ├── Domain/               # Business logic (Entities, Services, Value Objects)
   │   ├── Infrastructure/       # Persistence & External Services (Repositories)
   │   ├── Presentation/         # API Controllers & Adapters
   │   └── DataFixtures/         # Load Data for Entities
   ├── config/                   # Symfony configuration files
   ├── migrations/               # Database migration files
   ├── public/                   # Public assets and entry point
   ├── docker-compose.yml        # Docker configuration
   ├── Makefile                  # CLI automation commands (if applicable)
   ├── Tests/                    # Unit & Integration tests
   └── README.md
   ```

In some examples, you'll notice the absence of DTOs, Handlers, or Controllers. These components have already been demonstrated in earlier examples, and their patterns, benefits, and usage are considered established. To focus on core design patterns and avoid unnecessary duplication, the following examples will omit them unless their inclusion adds specific value.

## Design Patterns Implemented
The project follows a **modular DDD** structure with some design patterns. Below are the patterns implemented and their locations:

- **Repository Pattern** (`src/Infrastructure/Persistence`)
   - Separates database logic from business logic.

- **Builder Pattern** (`src/Application/BuilderUserProfile/CreateUserProfileHandler`)
   - Construct complex objects step by step.

- **Factory Method** (`src/Application/Notification/SendNotificationHandler`)
   - Provides a way to instantiate objects without exposing the creation logic.

- **Abstract Factory** (`src/Application/BuildForm/BuildFormHandler`)
   - A pattern used to create families of related objects without specifying their concrete classes.

- **Prototype** (`src/Application/Product/CloneProductHandler`)
   - Clone objects rather than creating new instances. Useful when creating new objects is expensive or complex.

- **Immutable Objects** (`src/Application/Document/UpdateDocumentContentHandler`)
   - The **Immutable Objects** pattern is applied in this system by ensuring that every time the content of a document is updated, a new `DocumentVersion` is created. The `DocumentVersion` is an immutable object, meaning its state cannot be modified after creation. Each update results in the creation of a new `DocumentVersion` instance, preserving the original versions for historical tracking while reflecting the latest changes.

- **Singleton** (`src/Application/Template/RenderEmailTemplateHandler`)
   - Singleton is a design pattern that ensures a class has only one instance and provides a global access point to it. It's useful for shared resources like configuration managers or loggers.

- **Factory Function** (`src/Application/Auth/GetOAuthConfigHandler`)
   - Factory Function pattern creates simple objects in a simple and centralized way, ideal for managing limited known variants without complex inheritance.

- **Adapter** (`src/Infrastructure/FileStorage/*`)
   -  The Adapter pattern allows incompatible interfaces to work together by converting the interface of a class into one that clients expect. It acts as a bridge between two unrelated interfaces, promoting code reusability and decoupling.
   It is commonly used when integrating third-party systems or legacy code into new architectures.

- **Bridge** (`src/Application/Export/AbstractExporter`)
   - The Bridge pattern is a structural design pattern that separates an abstraction from its implementation, allowing both to vary independently. It is useful when you want to avoid a permanent link between classes and allow flexibility in how features are extended or combined.

- **Composite** (`src/Application/Cart/Cart`)
   - The Composite Pattern allows treating individual objects and compositions of objects uniformly. It is particularly useful when dealing with tree structures such as file systems, UI hierarchies, or organizational charts. By defining a common interface for all components, the pattern enables clients to interact with both simple and complex elements in the same way.

- **Decorator** (`src/Application/Logger/AbstractLoggerDecorator`)
   - The Decorator pattern allows behavior to be added to individual objects dynamically without modifying their class. It wraps the original object in a new object (decorator) that enhances or overrides behavior, promoting flexibility and adherence to the Open/Closed Principle.

- **Facade** (`src/Application/Registration/UserRegistrationFacade`)
   - The Facade pattern provides a simplified interface to a complex subsystem. It hides the complexities of multiple underlying classes by exposing a single unified interface, making it easier for clients to interact with the system.

- **Flyweight** (`src/Application/Service/UserImportService`)
   - The Flyweight Pattern is a structural design pattern used to minimize memory usage by sharing as much data as possible with similar objects. It separates intrinsic (shared, immutable) state from extrinsic (context-specific) state, allowing objects with identical intrinsic state to be reused rather than duplicated. This is especially useful when dealing with large numbers of objects that share common data, improving performance and reducing resource consumption.

- **Proxy** (`src\Application\Report\GenerateFinancialReportHandler`)
   - The Proxy Pattern provides a surrogate or placeholder for another object to control access to it. It is commonly used to implement lazy loading, access control, logging, or remote proxies, without changing the original object's interface.

- **Chain of Responsability** (`src\Application\Payment\Handler\ProcessPaymentHandler`)
   - The Chain of Responsibility pattern decouples request senders from receivers by allowing multiple objects to handle the request in a sequence. Each handler decides whether to process the request or pass it along the chain, promoting flexibility and scalability in flow control.

- **Command** (`src\Command\CustomerInteractionCommand`)
   - The Command Pattern encapsulates a request as an object, allowing parameterization of clients with different requests, queuing of operations, and support for undoable actions. It decouples the object that issues a request from the one that handles it, enabling more flexible and extensible software architectures.

- **Iterator** (`src\Application\CsvImport\CsvFileIterator`)
   - The Iterator pattern provides a standard way to traverse elements in a collection without exposing its internal structure. It enhances flexibility and encapsulation when dealing with different types of iterable data sources.

- **Mediator** (`src\Application\Chat\ChatRoom`)
   - The Mediator Pattern centralizes communication between objects by introducing a mediator that handles interactions. This reduces direct dependencies between components, simplifies collaboration, and improves code maintainability. It is useful when multiple components need to coordinate behavior without being tightly coupled.

- **Memento** (`src\Application\FormWizard\FormWizard`)
   - The Memento pattern captures and restores an object's internal state without violating encapsulation. It is particularly useful for preserving snapshots of state, enabling undo operations, and improving the separation of concerns between objects managing state and those requesting its preservation.

- **Observer** (`src\Application\UserActivity\UserActionSubject`)
   - The Observer pattern establishes a one-to-many dependency between objects so that when one object changes its state, all its dependents are automatically notified. It promotes loose coupling and is useful for implementing reactive systems or event-driven architectures.

- **State** (`src\Domain\Order\OrderStateInterface`)
   - The State pattern allows an object to alter its behavior when its internal state changes. It helps encapsulate state-specific logic into separate classes, making the code easier to extend and maintain. This pattern is especially useful for workflows where objects transition through well-defined states.

- **Strategy** (`src\Domain\Sort\SortStrategyInterface`)
   - The Strategy Pattern defines a family of algorithms, encapsulates each one, and makes them interchangeable. It allows the algorithm to be selected at runtime, without altering the context. It promotes flexibility and extensibility in your code, especially when multiple algorithms perform the same task, and you need to switch them based on certain conditions.

- **Template Method** (`src\Application\Approval\RequestApproval`)
   - Defines the skeleton of an algorithm in the method, allowing subclasses to implement specific steps of the algorithm without changing its structure. This pattern promotes code reuse and allows customization of specific steps while maintaining the overall algorithm.

- **Visitor** (`src\Domain\Product\ShoppingCart`)
   - The Visitor pattern is used to apply dynamic and flexible discount logic to various types of products or services in a system. It allows us to define a set of discount strategies and apply them without modifying the underlying product or service classes. By using the Visitor pattern, we can add new types of discounts or promotions with minimal changes to existing code, making it easy to extend the system with new discount rules in the future.

## Tips & Best Practices

This sections provides useful tips and best practices followed in the project to ensure clean, mantainable and scalable code.

- **Fluent Interface in Setters**
   - Instead of traditional setters that return `void` we use a **Fluent Interface** by returning `$this`. This allows method chaining and improves readability.
   - Example of **Fluent Interface** (`src/Domain/Entity/User`)

- **DTOs**
   - Encapsulate request data before passing it to handlers to ensure type safety and reduce Controller logic.

- **DTO Validation**
   - We centralize our validation logic within **Data Transfer Objects** to ensure that the data passed between layers is correct and consistent.
   - Example of **DTO Validation** in all DTOs (`src/Application/DTO`)
   - We also do custom validations more complex with annotations as Category in the following example (`src/Application/DTO/ProductCloneDTO`) (`src/Application/Validator/Constraints/CategoryExistsValidator.php`)

- **Dependency Injection**
   - The project relies heavily on **Dependency Injection** to inject dependencies into classes, making tests easier as we can mock dependencies in unit tests and improves the flexibility of the code by reducing hard dependencies between classes.

- **Separation of Concerns**
   - Following the **Separation of Concerns** principle, the project separates different layers, such as application logic, domain logic, and infrastructure. This leads to a more modular and maintainable codebase, where changes in one layer do not affect others unnecessarily.

- **Single Responsibility Principle**
   - Each class should have one reason to change. In this project, we ensure that each class has a single responsibility, such as handlers focusing on business logic and factories on object creation. This separation of concerns leads to cleaner and more testable code.

- **Mappers**
   - A mapper converts raw DTOs into valid domain entities, encapsulating transformation logic and promoting cleaner architecture.
   - Example of **Mapper** on (`src/Application/Mapper`)

- **Design Choice: When to Use Interfaces**
   - We create interfaces (`src/Domain/Client/AwsS3ClientInterface`) only when dependency inversion is required — typically to mock external SDKs or complex clients in tests. For simpler wrappers like FtpClient (`src/Infrastructure/Client/FtpClient`), an interface isn't necessary unless multiple implementations are expected.

- **Handlers Instead Concrete Implementations** (`src/Application/Registration/UserRegistrationFacade`)
   - To maintain a decoupled and testable architecture, we always interact with handlers rather than directly invoking concrete implementations of services or factories. Handlers serve as clear entry points to specific actions or workflows, encapsulating their dependencies and logic. This abstraction promotes single responsibility, facilitates dependency injection, and makes it easier to modify, test, or extend behavior without impacting the rest of the system.

- **PHPDoc type hinting** (`src\Command\CustomerInteractionCommand.php`)
   - We use `/** @var ClassName $variable */` annotations to help static analysis tools and IDEs understand the expected type of a variable, especially when the type cannot be inferred automatically (e.g. when using helper methods or dependency injection containers). This improves code readability, auto-completion, and error detection during development.