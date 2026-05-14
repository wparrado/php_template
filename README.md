# PHP Laravel 11 — Hexagonal Architecture Template

Minimal steps to run the project locally:

1. Clone the repository:

   git clone https://github.com/wparrado/php_template.git
   cd php_template

2. Install PHP dependencies:

   composer install

3. Create environment file and application key:

   cp .env.example .env
   php artisan key:generate

4. (Optional) If using the Eloquent backend, set DB_BACKEND=eloquent in .env and run migrations:

   DB_BACKEND=eloquent php artisan migrate

5. Generate OpenAPI documentation (produces storage/api-docs/api-docs.json):

   php artisan openapi:generate

6. Serve the application locally:

   php artisan serve --host=127.0.0.1 --port=8000

7. Open the API docs in your browser:

   http://127.0.0.1:8000/api/documentation

Tests:

- Run unit tests: composer test:unit

Notes:
- After removing vendored dependencies from the repo, run composer install before first use.
- The OpenAPI JSON is generated, not committed; run php artisan openapi:generate when you change annotations.
