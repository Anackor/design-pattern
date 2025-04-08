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
   
### Design Patterns Implemented
The project follows a **modular DDD** structure with some design patterns. Below are the patterns implemented and their locations:

- **Builder Pattern** (`src/Application/BuilderUserProfile/`)
    - Construct complex objects step by step

- **Repository Pattern** (`src/Infrastructure/Persistence`)
    - Separates database logic from business logic

### Tips & Best Practices

This sections provides useful tips and best practices followed in the project to ensure clean, mantainable and scalable code.

- **Fluent Interface in Setters**
Instead of traditional setters that return `void` we use a **Fluent Interface** by returning `$this`. This allows method chaining and improves readability.

Example of **Fluent Interface** (`src/Domain/Entity/User`)

- **DTOs**
Encapsulate request data before passing it to handlers to ensure type safety and reduce Controller logic.

