# Task Manager (Laravel)

Simple Task Manager application built with Laravel and Bootstrap 4.

Features
- Create / Read / Update / Delete tasks
- Server-side validation
- Bootstrap 4 UI with responsive table and modal confirmation
- Search by title and pagination
- AJAX status toggle for fast Completed/Pending updates
- PHPUnit feature tests

Quick start
1. Install dependencies

```powershell
composer install
cp .env.example .env
php artisan key:generate
```

2. Database (SQLite recommended for quick local use)

```powershell
# create sqlite file
mkdir database\ && New-Item database\database.sqlite -ItemType File -Force
# or configure .env to point to your local DB
php artisan migrate
php artisan db:seed
php artisan serve --host=127.0.0.1 --port=8000
```

3. Run tests

```powershell
php artisan test
```

Assumptions and notes
- Status values are stored as `pending`, `in_progress`, or `completed` (lowercase).
- The application uses Bootstrap 4 from CDN.
- For CI, a GitHub Actions workflow is included at `.github/workflows/ci.yml`.

Bonus features implemented
- Search by title
- Pagination
- AJAX status toggle