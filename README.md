# HomeOptic

A domiciliary optician management system built for **Psk Locum Cover Ltd** — a mobile optometry service that visits patients at home. Built as a technical assignment using **Claude Code** as the primary agentic AI development tool.

---

## The Brief

My dad is a qualified optician who is looking to start his own domiciliary service — visiting patients at home, typically elderly people who can't travel to a practice. To understand the domain properly before building anything, I researched Blink OMS, a real commercial optician management platform used by an existing practice. I used Blink as the reference for this prototype, basing the data model and workflows on a proven real-world system rather than guessing at requirements.

The three core pillars of the prototype:
- **Patient database** — storing personal, contact, and clinical information
- **Spectacle examination records** — a multi-tab form covering history, ophthalmoscopy, investigative techniques, and refraction
- **Appointment diary** — a week/day calendar view with queued job infrastructure for reminders

---

## AI Tool: Claude Code

**Tool used:** Claude Code (claude.ai/code) with Claude Sonnet via Claude Pro  
**Installation:**
```bash
npm install -g @anthropic/claude-code
claude  # authenticates via browser with your Anthropic account
```

Claude Code is an agentic CLI tool that reads and writes files directly in your project. Unlike pasting code from a chat interface, it works conversationally inside your codebase — proposing changes, running commands, checking output, and iterating.

My workflow was deliberately structured: I spent the first sessions planning and agreeing architecture *before* any code was written. Claude Code's transcript shows this clearly — schema design, OOP pattern decisions, and open questions were all resolved in discussion first.

---

## Tech Stack

- **Laravel 13.1** / PHP 8.3
- **SQLite** (local development — zero config, ships with Laravel 13)
- **Laravel Breeze** (Blade stack) for authentication
- **Laravel Queues** (database driver) for background jobs
- **Alpine.js** for the tabbed examination form
- **Tailwind CSS** via Vite

---

## Getting Started

```bash
git clone <repo-url>
cd homeOptic

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate

php artisan migrate:fresh --seed
php artisan serve
```

Log in with: `admin@homeoptic.test` / `password`

To process queued jobs (appointment reminders):
```bash
php artisan queue:work
```

---

## Architecture

### Design Patterns

| Pattern | Where it lives | Why |
|---|---|---|
| Repository | `app/Contracts/` + `app/Repositories/` | Keeps query logic out of controllers; single binding point for future caching |
| Factory | `app/Factories/ExaminationFactory` | Creates parent exam + 4 child tab rows atomically inside `DB::transaction()` |
| Observer | `app/Observers/` | Decouples side effects — appointment created → reminder queued; exam signed → PDF job queued |
| Strategy | `NotificationStrategy` (planned) | Different notification channels per patient |
| Value Object | `RxMeasurement` (planned) | Transpose Rx optical calculation — domain logic that doesn't belong on the model |
| ~~Singleton~~ | n/a | Laravel's service container already manages singleton bindings — adding PHP Singleton pattern would undermine testability |

### Repository Pattern with Interfaces

Each repository has a contract in `app/Contracts/` and an Eloquent implementation in `app/Repositories/`, bound in `RepositoryServiceProvider`:

```php
$this->app->bind(PatientRepositoryInterface::class, EloquentPatientRepository::class);
```

The interface is there for a specific reason: a `CachedPatientRepository` decorator is a planned next step for the patient search screen. With the interface in place, adding caching is a one-line binding change — not a refactor.

### Queue Chain (Verified Working)

```
Appointment::create()
  → AppointmentObserver::created()
    → SendAppointmentReminderJob dispatched to database queue
      → php artisan queue:work picks it up
        → AppointmentReminderMail rendered and sent
          → Written to storage/logs/laravel.log (log mail driver)
            → appointment.notified_at stamped
```

The mail implementation is real — not a stub. `MAIL_MAILER=log` is Laravel's built-in local development mail driver. Switching to production is one `.env` line change (`MAIL_MAILER=smtp`).

### ExaminationObserver Hook

The observer fires on `updated`, not `created`:

```php
public function updated(Examination $examination): void
{
    if ($examination->wasChanged('signed_at') && $examination->signed_at !== null) {
        GenerateExaminationReportJob::dispatch($examination);
    }
}
```

The exam record is created blank when a patient's "New Examination" button is clicked — tabs are empty at that point. The PDF should only generate when the optometrist signs off. `wasChanged('signed_at')` ensures every other tab save passes through silently.

### ExaminationFactory Transaction

```php
return DB::transaction(function () use ($patient, $type, $staff) {
    $examination = Examination::create([...]);
    ExamHistorySymptom::create(['examination_id' => $examination->id]);
    ExamOphthalmoscopy::create(['examination_id' => $examination->id]);
    ExamInvestigative::create(['examination_id' => $examination->id]);
    ExamRefraction::create(['examination_id' => $examination->id]);
    return $examination->load([...]);
});
```

Without the transaction, a failure on the third child row would leave an orphaned `examinations` record with only some of its tab rows — breaking every eager-load that expects all four tabs to exist.

---

## Key Architectural Decisions

### Flat columns on `exam_refraction` (93 columns)

I initially accepted a normalised `exam_rx_entries` approach (one row per Rx section per eye). After asking Claude Code to walk through the actual Eloquent query, I reversed this decision.

The normalised approach requires:
- A `groupBy` + 8 `?->first()` calls on every form load
- 8 `updateOrCreate` calls on every save
- HTML field names that still have to encode `rx_type` and `eye` anyway

For a system where the primary read path is "render a form, save a form" — with no cross-exam Rx queries in scope — flat columns are simpler and equally correct. The 93-column table is an honest reflection of the domain.

### RESTRICT (not SET NULL) on patient FK relationships

The initial migration used `nullOnDelete()` for `practice_id`, `doctor_id`, and `pct_id` on the patients table. I flagged this as wrong for a medical system: silently nulling a patient's GP association when a doctor record is deleted leaves no audit trail and no error. The correct behaviour is `restrictOnDelete()` — force the user to explicitly reassign patients before deleting a doctor record.

### PHP Backed Enums over inline cast arrays

12 backed string enums in `app/Enums/` with a shared `HasOptions` trait. The alternative (inline arrays on models) would require repeating the same strings across FormRequest validation, Blade dropdowns, Repository filters, and every job that branches on the value. Enums are the single source of truth. The `label()` method on each enum drives every dropdown in the UI.

### JSON medications (prototype only)

Medications are stored as a JSON column on `exam_history_symptoms` rather than a normalised pivot table. This was a deliberate prototype scope decision. The README documents it explicitly: this is the first thing to normalise before production use — a proper `medications` pivot table with seeded drug names would support queries like "all patients on Warfarin".

---

## What's Real vs Stubbed

| Feature | Status | Notes |
|---|---|---|
| Appointment reminder emails | ✅ Real | Full Mailable, log driver locally. Switch `MAIL_MAILER` in `.env` for production |
| Queue infrastructure | ✅ Real | Observer → dispatch → worker → process chain fully verified |
| PDF examination reports | 🔲 Stubbed | `GenerateExaminationReportJob` logs intent. Needs: DomPDF package, report Blade template, storage strategy |
| Patient search | ✅ Real | 9 filters, 10 sort options, paginated |
| Examination form (4 tabs) | ✅ Real | All fields, Alpine.js tabs, per-tab save routes |
| Sign-off / signature | ✅ Real | Stamps `signed_at`, triggers observer |

---

## Project Structure

```
app/
├── Contracts/          # Repository interfaces
├── Enums/              # 12 backed string enums + HasOptions trait
├── Factories/          # ExaminationFactory (DB::transaction)
├── Http/Controllers/   # PatientController, ExaminationController,
│                       # AppointmentController, DiaryController
├── Jobs/               # SendAppointmentReminderJob, GenerateExaminationReportJob
├── Mail/               # AppointmentReminderMail
├── Models/             # 11 Eloquent models + User
├── Observers/          # AppointmentObserver, ExaminationObserver
├── Providers/          # AppServiceProvider, RepositoryServiceProvider
└── Repositories/       # EloquentPatientRepository, EloquentExaminationRepository,
                        #   EloquentAppointmentRepository
research/
├── BLINK_REFERENCE.md  # Field-level documentation of the reference software
└── FINAL_SCHEMA.md     # Agreed database schema (written before any migrations)
transcripts/            # Full Claude Code conversation history
```

---

## AI Transcripts

The full conversation history with Claude Code is in the `transcripts/` folder. These are raw and unedited.

A few moments worth reading in the transcripts:

- **Schema planning session** — all architectural decisions made before a line of code was written, including the decision to reverse the `exam_rx_entries` normalisation after walking through the actual Eloquent query complexity
- **FK constraint correction** — I caught that `nullOnDelete()` was wrong for a medical system and pushed back; Claude Code agreed and corrected all three patient FK relationships to `restrictOnDelete()`
- **Singleton discussion** — I asked about using the Singleton pattern; Claude Code correctly pushed back explaining Laravel's service container already handles this, and adding PHP Singleton would undermine testability
- **Observer trigger decision** — `wasChanged('signed_at')` on `updated` rather than `created`, because the exam record is blank on creation and the PDF should only generate on sign-off
