# HomeOptic

A domiciliary optician management system built for **Psk Locum Cover Ltd** — a mobile optometry service that visits patients at home. Built as a technical assignment using **Claude Code** as the primary agentic AI development tool.

---

## The Brief

Father dearest is a qualified optician who is looking to start his own domiciliary service — visiting patients at home, typically elderly people who can't travel to a practice. To understand the domain properly before building anything, I researched Blink OMS, a real commercial optician management platform used by an existing practice. I used Blink as the reference for this prototype, basing the data model and workflows on a proven real-world system rather than guessing at requirements.

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
- **Alpine.js** for interactive UI components
- **Tailwind CSS** via Vite
- **DomPDF** (`barryvdh/laravel-dompdf`) for PDF generation

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

To process queued jobs (appointment reminders, PDF generation):
```bash
php artisan queue:work
```

To send day-before appointment reminders:
```bash
php artisan reminders:send-day-before
# Test with a specific date:
php artisan reminders:send-day-before --date=2026-03-21
```

---

## Features

### Patient Management
- Full patient database with personal, contact, address, and clinical fields
- Medical Information section: registered blind/partially sighted, hearing impairment, retinitis pigmentosa, physical disabilities, mental health conditions
- Social & Benefits section: full-time education status, benefits received (Income Support, Universal Credit, Pension Credit, etc.), next of kin, emergency contact, carer details
- Patient search with filters: name, ID, DOB, postcode, patient type, glaucoma, diabetic flags
- Default sort by latest, with sort options (Latest, Oldest, Surname A-Z, Surname Z-A)
- Patient documents section — upload and download PDFs attached to a patient's profile
- Audit trail on examinations — last edited by and timestamp

### GOS Eligibility
- Automatic GOS1, GOS3, and GOS6 eligibility calculation based on patient data (age, medical conditions, benefits)
- Admin override with reason — can manually mark eligible/not eligible per form
- Eligibility recalculates immediately on every patient update
- **GOS1** — free NHS sight test eligibility
- **GOS3** — NHS optical voucher eligibility (correctly excludes age >= 60 as a standalone criterion)
- **GOS6** — mobile/domiciliary sight test (requires GOS1 eligibility + domiciliary reason)

### GOS Forms (Printable)
- Full recreation of official NHS GOS1, GOS3, and GOS6 forms as HTML/Blade views
- Patient data pre-fills automatically from the database
- Eligibility checkboxes auto-ticked based on patient data
- Interactive canvas-based signature pads (mouse and touch support)
- Native browser date pickers on all date fields
- Print button triggers browser print-to-PDF
- Save Form button saves filled form data to the eGOS submissions system
- Back button is context-aware — returns to patient profile or eGOS page depending on origin

### eGOS Claims Management
- Dedicated eGOS page (Diary -> Patients -> eGOS in navigation)
- Table showing: patient, form type, voucher value, batch reference, submitted date, status badge
- Filters: date range, form type, status, patient name/ID
- Individual status management per submission
- **Batch Submit** — select multiple submissions, generates a BATCH-YYYYMMDD-XXXX reference and marks all as Awaiting Confirmation
- **Batch Mark Paid** — marks selected submissions as Accepted
- Delete submissions (unsubmitted only)
- Form data saved per submission — reopening a saved form repopulates all fields

### Appointment Diary
- Week and day view time-grid calendar (08:00-20:00)
- Appointment blocks absolutely positioned by time, sized by duration
- Colour-coded by status: Booked (green), Confirmed (blue), Completed (purple), Did Not Attend (red), Cancelled (grey)
- Click-to-book — hover shows green (free) or red (taken) highlight; clicking an empty slot opens the booking form pre-filled with date and time
- Click appointment block to edit
- Live patient search autocomplete on booking form (search by name or ID)
- + New Patient button on booking form opens patient creation in new tab
- Double-booking prevention with overlap detection (half-open interval arithmetic)
- Show/hide cancelled appointments toggle
- Calendar icon date picker for navigating to specific weeks/days
- Conflict error display with form repopulation on validation failure

### Examination Records
- Spectacle examination workflow with four tabs:
  - **History & Symptoms** — GOS eligibility, last exam date, reason for visit, POH, GH, medication notes, medication checkboxes (54 medications), FH, FOH, patient flags. Set Default buttons per text field.
  - **Ophthalmoscopy** — Right and Left eye findings with full dropdown options for all 13 fields (Pupils, Lids & Lashes, Lashes, Conjunctiva, Cornea, Sclera, Anterior Chamber, Media, C/D Ratio, A/V Ratio, Fundus Periphery, Macular, Ret. Grading). Set Default (R) and Set Default (L) buttons. Copy Right to Left button.
  - **Further Investigative Techniques** — IOP (Pre/Post) with method dropdowns and Now buttons, Cover Test dropdowns, OMB, Visual Fields, Motility, Amsler Value, Keratometry, NPC, Stereopsis, Colour Vision, Amplitude of Accommodation
  - **Refraction** — Current/Previous Rx, Retinoscopy, Subjective Refraction with full prescription table (SPH, CYL, Axis, Prism with diagonal direction support, VA, Near Add, Int Add, Acuity dropdowns), PD measurements, Outcome, Recommendations, NHS Voucher dropdowns (A-D single vision, E-H bifocal), Retest, Examination Comment
- Carry-forward from previous signed examination — History, medication, GOS context, IOP method, and colour vision pre-filled on new exams
- Tab memory — saving a tab returns to that tab, not the first tab
- Sign-off button stamps signed_at and triggers PDF generation job
- Audit tracking: last_edited_by and last_edited_at on every save
- Delete unsigned examinations (signed examinations are protected)

### Examination History Table
- Shows: Date + time, Examiner (+ last edited info), Subjective Rx (any filled fields), Notes, Signed badge, Report download link, Open/Delete actions
- Subjective Rx is a hyperlink to the Refraction tab of that examination
- Ordered by most recent first

### Notifications & Queue System

#### Queue Chain (Verified Working)
```
Appointment created
  -> AppointmentObserver::created()
    -> SendAppointmentReminderJob dispatched
      -> NotificationStrategyFactory selects channel
        -> Email/SMS/Letter strategy executes
          -> appointment.notified_at stamped
```

#### Strategy Pattern — Notification Channels
- `EmailNotificationStrategy` — sends real email via Laravel Mail (log driver locally, SMTP in production)
- `SmsNotificationStrategy` — stub, logs intent
- `LetterNotificationStrategy` — fallback stub, logs intent
- Factory selects: Email if patient has email -> SMS if mobile but no email -> Letter as fallback
- Every patient gets a notification attempt — no silent skips

#### Day-Before Reminders
- Scheduled command `reminders:send-day-before` runs daily at 08:00
- Queries appointments for the next day, excludes cancelled and recently notified
- Uses separate `day_before_notified_at` column to avoid conflicts with creation notifications
- `--date` flag for manual testing

#### PDF Report Generation
- `GenerateExaminationReportJob` triggered by `ExaminationObserver` on sign-off
- Generates A4 PDF covering all four exam tabs using DomPDF
- Stored to `storage/app/private/reports/examination-{id}.pdf`
- Download link appears on patient profile examination history

---

## Architecture

### Design Patterns

| Pattern | Where it lives | Why |
|---|---|---|
| Repository | `app/Contracts/` + `app/Repositories/` | Keeps query logic out of controllers; single binding point for future caching |
| Factory | `app/Factories/ExaminationFactory` | Creates parent exam + 4 child tab rows atomically inside DB::transaction() |
| Factory | `app/Factories/NotificationStrategyFactory` | Selects the correct notification channel per patient |
| Observer | `app/Observers/AppointmentObserver` | Appointment created -> reminder job dispatched |
| Observer | `app/Observers/ExaminationObserver` | Exam signed -> PDF generation job dispatched (uses wasChanged('signed_at')) |
| Strategy | `app/Notifications/Strategies/` | Swappable notification channels (Email/SMS/Letter) |
| ~~Singleton~~ | n/a | Laravel's service container already manages singleton bindings — adding PHP Singleton would undermine testability |

### Repository Pattern with Interfaces

Each repository has a contract in `app/Contracts/` and an Eloquent implementation in `app/Repositories/`, bound in `RepositoryServiceProvider`. Adding a `CachedPatientRepository` later is a one-line binding change.

### ExaminationFactory Transaction

Without the transaction, a failure on any child row would leave an orphaned examination record with only some of its tabs — breaking every eager-load that expects all four tabs to exist.

### Key Architectural Decisions

**Flat columns on `exam_refraction` (93 columns)**
Rejected normalised `exam_rx_entries` approach after walking through the actual Eloquent query — 8 `updateOrCreate` calls per save and groupBy gymnastics on every load, with no benefit for a form-render-and-save use case.

**RESTRICT (not SET NULL) on patient FK relationships**
Silently nulling a patient's GP association when a doctor is deleted would be a data integrity risk in a medical system. RESTRICT forces the user to explicitly reassign patients first.

**PHP Backed Enums over inline cast arrays**
14+ backed string enums in `app/Enums/` with a shared `HasOptions` trait. Single source of truth across FormRequest validation, Blade dropdowns, Repository filters, and jobs.

**GOS eligibility logic**
`patient_type` is NOT used as the primary eligibility check. A Private patient can still qualify for GOS1 if diabetic, over 60, etc. Only `PatientType::FamilyHistory` adds criteria (age >= 40). GOS3 correctly excludes age >= 60 as a standalone criterion.

**Strategy pattern improvement**
The original job silently skipped if a patient had no email. The Strategy pattern ensures every patient gets a notification attempt through the appropriate channel. No silent skips in a medical system.

**ExaminationObserver hook**
Uses `updated` with `wasChanged('signed_at')` rather than `created`. The exam record is blank on creation — PDF should only generate on sign-off.

---

## What's Real vs Stubbed

| Feature | Status | Notes |
|---|---|---|
| Appointment reminder emails | Real | Full Mailable, log driver locally. Switch MAIL_MAILER in .env for production |
| Day-before reminders | Real | Scheduled command, dispatches via Strategy pattern |
| Queue infrastructure | Real | Observer -> dispatch -> worker -> process chain fully verified |
| PDF examination reports | Real | DomPDF, stored to disk, downloadable from patient profile |
| GOS form saving | Real | Form data serialised to JSON, repopulates on reopen |
| SMS notifications | Stubbed | Logs intent. Needs SMS provider (e.g. Twilio) |
| Letter notifications | Stubbed | Logs intent. Needs letter generation template |

---

## Project Structure

```
app/
├── Console/Commands/   # SendDayBeforeReminders
├── Contracts/          # Repository interfaces, NotificationStrategy interface
├── Enums/              # 14+ backed string enums + HasOptions trait
├── Factories/          # ExaminationFactory, NotificationStrategyFactory
├── Http/Controllers/   # PatientController, ExaminationController,
│                       # AppointmentController, DiaryController,
│                       # EgosController, PatientGosFormController,
│                       # PatientDocumentController
├── Jobs/               # SendAppointmentReminderJob,
│                       # SendAppointmentDayBeforeReminderJob,
│                       # GenerateExaminationReportJob
├── Mail/               # AppointmentReminderMail
├── Models/             # 13+ Eloquent models
├── Notifications/
│   └── Strategies/     # EmailNotificationStrategy, SmsNotificationStrategy,
│                       # LetterNotificationStrategy
├── Observers/          # AppointmentObserver, ExaminationObserver
├── Providers/          # AppServiceProvider, RepositoryServiceProvider
├── Repositories/       # EloquentPatientRepository,
│                       # EloquentExaminationRepository,
│                       # EloquentAppointmentRepository
└── Services/           # GosEligibilityService
research/
├── BLINK_REFERENCE.md  # Field-level documentation of the reference software
└── FINAL_SCHEMA.md     # Agreed database schema (written before any migrations)
research/transcripts/   # Full Claude Code conversation history (raw, unedited)
resources/views/
├── diary/              # Time-grid calendar
├── egos/               # eGOS claims management
├── examinations/       # 4-tab examination form + PDF report template
├── gos/                # GOS1, GOS3, GOS6 printable forms
└── patients/           # Patient index, show, create, edit
```

---

## AI Transcripts

The full conversation history with Claude Code is in the `research/transcripts/` folder. These are raw and unedited.