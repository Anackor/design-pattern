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
   php .vendor/bin/phpunit

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
