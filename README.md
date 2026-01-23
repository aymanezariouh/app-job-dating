# Job Dating App

Minimal PHP (Twig) job dating platform with student and admin flows.

## Requirements
- PHP >= 8.0
- Composer
- MySQL

## Setup
1. Install dependencies:
   - `composer install`
2. Create the database and seed data:
   - Import `data.sql` into MySQL.
3. Configure environment:
   - Create `.env` with at least:
     - `DB_PASS=your_password`
     - `SESSION_LIFETIME=3600` (optional)
4. Start the app (dev server):
   - `php -S localhost:8000 -t public`

## Default Accounts
- Admin:
  - Email: `admin@youcode.com`
  - Password hash in `data.sql` (use your own if needed)
- Student:
  - Email: `student@youcode.com`
  - Password hash in `data.sql`

## Key Pages
- Student jobs dashboard: `/jobs`
- Apply to job: `/jobs/apply?id=ID`
- Admin dashboard: `/admin/dashboard`
- Admin applications review: `/admin/applications`

## Application Flow
- Students apply once per job.
- Status values: `pending`, `accepted`, `rejected`.
- Admin can approve/deny applications from `/admin/applications`.
- Student dashboard shows:
  - Green "Hired" badge when status is `accepted`
  - Red "Rejected" badge when status is `rejected`

## Code Map (Main Pieces)
- Routes: `config/routes.php`
- Front controllers: `app/controllers/front`
- Back controllers: `app/controllers/back`
- Models: `app/models`
- Views:
  - Front: `app/views/frontend` and `app/views/front`
  - Back: `app/views/back`
- Assets:
  - Student UI JS: `public/assets/js/index.js`
  - Admin sidebar JS: `public/assets/js/script.js`

## Notes
- DB config is in `config/config.php` (reads `DB_PASS` from `.env`).
- If you change the `applications.status` enum, update it in:
  - `data.sql`
  - `app/controllers/back/ApplicationController.php`
