# Intern Task Management System

A web application designed to help organizations manage tasks for interns efficiently. The system provides distinct interfaces for Admins and Interns, with features for task assignment, progress tracking, and communication.

## About The Project

This project is a Laravel-based application that facilitates the management of interns and their assigned tasks. Administrators can create, assign, and monitor tasks, while interns can view their tasks, submit their work, and communicate with admins through comments and a real-time chat feature.

### Key Features

-   **Role-Based Access Control:** Separate dashboards and permissions for Admins and Interns using `spatie/laravel-permission`.
-   **Task Management:** Admins can create tasks, assign them to one or more interns, and track their status.
-   **Comments:** Users can leave comments on tasks for clarification or feedback.
-   **Real-time Chat:** A messaging system for direct communication between users, powered by Laravel Reverb.
-   **User Management:** Admins can manage intern and other admin accounts.

### Built With

*   [Laravel](https://laravel.com/) (v12)
*   [Tailwind CSS](https://tailwindcss.com/)
*   [Vite](https://vitejs.dev/)
*   [Pusher / Laravel Reverb](https://laravel.com/docs/broadcasting) for WebSockets
*   [jQuery](https://jquery.com/) & [Select2](https://select2.org/)

## Getting Started

To get a local copy up and running, follow these simple steps.

### Prerequisites

-   PHP >= 8.2
-   Composer
-   Node.js & NPM
-   A local database (MySQL, PostgreSQL, or SQLite)

### Installation

1.  **Clone the repository:**
    ```sh
    git clone https://github.com/your_username/your_repository.git
    cd Intern-Task-Management
    ```

2.  **Install PHP dependencies:**
    ```sh
    composer install
    ```

3.  **Install NPM dependencies:**
    ```sh
    npm install
    ```

4.  **Set up your environment file:**
    -   Copy the example environment file:
        ```sh
        cp .env.example .env
        ```
    -   Generate an application key:
        ```sh
        php artisan key:generate
        ```

5.  **Configure your `.env` file:**
    -   Set up your database connection details (`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
    -   Ensure `BROADCAST_DRIVER` is set to `reverb` and configure `REVERB_APP_ID`, `REVERB_APP_KEY`, and `REVERB_SECRET`.

6.  **Run database migrations and seeders:**
    -   The seeders will create default roles (Admin, Intern) and a default Admin user.
    ```sh
    php artisan migrate --seed
    ```

## Usage

This project includes a `concurrently` script to run all necessary development servers with a single command.

```sh
composer run dev
```

This command will start:
- The PHP development server (`php artisan serve`)
- The Vite asset bundler
- The queue worker
- The Pail log viewer

Once running, you can access the application at `http://127.0.0.1:8000`.

**Default Admin Credentials:**
-   **Email:** admin@example.com
-   **Password:** password

## Testing

To run the feature and unit tests, use the following command:

```sh
php artisan test
```
