<?php

use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\EgosController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientDocumentController;
use App\Http\Controllers\PatientGosFormController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// ── Public (no auth) ─────────────────────────────────────────────────────────
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/book', [PublicController::class, 'book'])->name('book');
Route::get('/api/available-slots', [PublicController::class, 'availableSlots']);
Route::get('/api/check-postcode', [PublicController::class, 'checkPostcode']);
Route::post('/book', [PublicController::class, 'submitBooking'])->name('book.submit');
Route::get('/booking/{token}/confirmed', [PublicController::class, 'bookingConfirmed'])->name('booking.confirmed');

Route::get('/dashboard', function () {
    return redirect()->route('diary.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // ── Profile (Breeze) ─────────────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Diary ────────────────────────────────────────────────────────────────
    Route::get('/diary', [DiaryController::class, 'index'])->name('diary.index');

    // ── Appointments ─────────────────────────────────────────────────────────
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');

    // ── Patients ─────────────────────────────────────────────────────────────
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
    Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
    Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
    Route::post('/patients/{patient}/documents', [PatientDocumentController::class, 'store'])->name('patients.documents.store');
    Route::get('/documents/{document}/download', [PatientDocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/view', [PatientDocumentController::class, 'view'])->name('documents.view');
    Route::delete('/documents/{document}', [PatientDocumentController::class, 'destroy'])->name('documents.destroy');

    // ── eGOS Claims ──────────────────────────────────────────────────────────
    Route::get('/egos', [EgosController::class, 'index'])->name('egos.index');
    Route::post('/egos', [EgosController::class, 'store'])->name('egos.store');
    Route::post('/egos/batch-submit', [EgosController::class, 'batchSubmit'])->name('egos.batch-submit');
    Route::post('/egos/batch-paid', [EgosController::class, 'batchMarkPaid'])->name('egos.batch-paid');
    Route::patch('/egos/{submission}/status', [EgosController::class, 'updateStatus'])->name('egos.status');
    Route::delete('/egos/{submission}', [EgosController::class, 'destroy'])->name('egos.destroy');

    // ── GOS Forms ────────────────────────────────────────────────────────────
    Route::get('/patients/{patient}/gos/{formType}/form', [PatientGosFormController::class, 'showForm'])->name('patients.gos.form');
    Route::post('/patients/{patient}/gos/{formType}', [PatientGosFormController::class, 'update'])->name('patients.gos.update');
    Route::delete('/patients/{patient}/gos/{formType}/override', [PatientGosFormController::class, 'clearOverride'])->name('patients.gos.clear');

    // ── Examinations ─────────────────────────────────────────────────────────
    Route::post('/patients/{patient}/examinations', [ExaminationController::class, 'store'])->name('examinations.store');
    Route::get('/examinations/{examination}', [ExaminationController::class, 'show'])->name('examinations.show');
    Route::put('/examinations/{examination}/history', [ExaminationController::class, 'updateHistory'])->name('examinations.history.update');
    Route::put('/examinations/{examination}/ophthalmoscopy', [ExaminationController::class, 'updateOphthalmoscopy'])->name('examinations.ophthalmoscopy.update');
    Route::put('/examinations/{examination}/investigative', [ExaminationController::class, 'updateInvestigative'])->name('examinations.investigative.update');
    Route::put('/examinations/{examination}/refraction', [ExaminationController::class, 'updateRefraction'])->name('examinations.refraction.update');
    Route::patch('/examinations/{examination}/sign', [ExaminationController::class, 'sign'])->name('examinations.sign');
    Route::delete('/examinations/{examination}', [ExaminationController::class, 'destroy'])->name('examinations.destroy');
    Route::get('/examinations/{examination}/report', [ExaminationController::class, 'report'])->name('examinations.report');

    // ── Admin Notifications & Booking Decisions ───────────────────────────────
    Route::get('/admin/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/admin/notifications/{notification}/read', [AdminNotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/admin/pending-bookings/{pendingBooking}/approve', [AdminNotificationController::class, 'approve'])->name('bookings.approve');
    Route::post('/admin/pending-bookings/{pendingBooking}/decline', [AdminNotificationController::class, 'decline'])->name('bookings.decline');
});

require __DIR__.'/auth.php';
