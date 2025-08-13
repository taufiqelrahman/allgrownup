AllGrownUp

AllGrownUp is a web application built with Laravel (PHP) and JavaScript, designed to manage products, orders, users, and more. It includes features for e-commerce, user authentication, order processing, and email notifications.

## Features

-   User registration, authentication, and password reset
-   Product and category management
-   Shopping cart and order processing
-   Email notifications for order events
-   Social login integration (Facebook, Google)
-   RESTful API endpoints
-   Admin and user roles
-   File storage and uploads
-   Queue and background job support

## Project Structure

-   `app/` — Main application logic (models, controllers, services, mail, etc.)
-   `bootstrap/` — Application bootstrap files
-   `config/` — Configuration files
-   `database/` — Migrations, factories, and seeders
-   `external_emails/` — Email templates
-   `public/` — Public web root
-   `resources/` — Frontend assets and Blade views
-   `routes/` — Route definitions (web, API, console)
-   `storage/` — Logs, cache, and file storage
-   `tests/` — Unit and feature tests

## Getting Started

### Prerequisites

-   PHP >= 7.4
-   Composer
-   Node.js & npm
-   MySQL or compatible database
-   Docker (optional, for containerized setup)

### Installation

1. Clone the repository:
    ```sh
    git clone https://github.com/taufiqelrahman/allgrownup.git
    cd allgrownup
    ```
2. Install PHP dependencies:
    ```sh
    composer install
    ```
3. Install JavaScript dependencies:
    ```sh
    npm install
    ```
4. Copy the example environment file and configure:
    ```sh
    cp .env.example .env
    # Edit .env with your database and mail settings
    ```
5. Generate application key:
    ```sh
    php artisan key:generate
    ```
6. Run database migrations and seeders:
    ```sh
    php artisan migrate --seed
    ```
7. Build frontend assets:
    ```sh
    npm run dev
    ```
8. Start the development server:
    ```sh
    php artisan serve
    ```

### Docker (Optional)

To use Docker for local development:

```sh
docker-compose up --build
```

## Running Tests

```sh
php artisan test
```

## Security

-   Input validation on both frontend and backend
-   Secure password hashing and authentication
-   CSRF protection
-   No sensitive data in client storage
-   Prepared statements for database access

## License

This project is licensed under the MIT License.

## Author

Taufiq El Rahman
