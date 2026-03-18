# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**HomeOptic** is Psk Locum Cover Ltd's domiciliary (mobile) optometry management platform. Core features: patient record management, appointment scheduling/diary, eye examination records, and automated reminders.

Stack: **Laravel 13 / PHP 8.3 / SQLite (local) / Tailwind CSS / Alpine.js / Blade / Laravel Breeze (auth)**.

## Common Commands

```bash
php artisan serve               # Run dev server
php artisan migrate             # Run pending migrations
php artisan migrate:fresh --seed # Reset DB + seed (admin@homeoptic.test / password)
php artisan db:seed             # Seed without migrating
php artisan queue:work          # Process queued jobs (reminders, PDF generation)
npm run dev                     # Vite dev server (hot reload)
npm run build                   # Production asset build
php artisan route:list          # List all routes
php artisan tinker              # REPL
```

No test suite yet — `php artisan test` will run PHPUnit when tests are added.

## Architecture

Three primary domains, mirrored in routes, controllers, repositories, and views:

1. **Patients** — Demographics, medical flags, practice/GP associations. `PatientController` uses `PatientRepositoryInterface`. Search filters via `EloquentPatientRepository::search()` (9 filters, paginated 25).

2. **Examinations** — Spectacle exams (only supported type currently). Created via `ExaminationFactory` (DB transaction, creates parent + 4 child tab rows atomically). `ExaminationController` has one method per tab (`updateHistory`, `updateOphthalmoscopy`, `updateInvestigative`, `updateRefraction`) plus `sign`. Sign-off triggers `ExaminationObserver` → `GenerateExaminationReportJob` (stub; PDF not yet implemented).

3. **Diary / Appointments** — Week/day calendar. `DiaryController::index()` handles view mode + date range. `AppointmentController` uses `AppointmentRepositoryInterface`. Creating an appointment dispatches `SendAppointmentReminderJob` (real Mailable, logged via `MAIL_MAILER=log`).

### Design Patterns

- **Repository Pattern**: `app/Contracts/` (interfaces) → `app/Repositories/` (Eloquent implementations). Bound in `RepositoryServiceProvider`.
- **Factory Pattern**: `app/Factories/ExaminationFactory` — creates all 5 exam rows (examination + 4 tabs) in a single DB transaction.
- **Observer Pattern**: `AppointmentObserver` (`created`) and `ExaminationObserver` (`updated`, guards on `wasChanged('signed_at')`). Registered in `AppServiceProvider::boot()`.

### Key Structural Notes

- **PHP Backed Enums** in `app/Enums/` all use the `HasOptions` trait (`app/Enums/Concerns/HasOptions.php`) providing `options(): array` for Blade `<select>` inputs and `label(): string`.
- **Flat columns** on `exam_refraction` (93 columns) — deliberate choice over normalised `exam_rx_entries`. Primary read path is form render; no cross-exam Rx queries needed.
- **`$table` overrides** on `ExamOphthalmoscopy`, `ExamInvestigative`, `ExamRefraction` models — Laravel's inflector produces wrong names otherwise.
- **Medications** stored as JSON array on `exam_history_symptoms.medications` — prototype convenience, normalise to pivot before production.
- **Queue driver**: `QUEUE_CONNECTION=database`. Jobs table already migrated from Breeze scaffold.
- **Auth**: Laravel Breeze (Blade stack). Default admin: `admin@homeoptic.test` / `password`.

## Key Reference

`research/BLINK_REFERENCE.md` is the authoritative specification document covering all form fields, dropdowns, validation rules, and UI behaviour for every page.

`research/FINAL_SCHEMA.md` is the agreed database schema reference with all tables, columns, enum values, and design rationale.
