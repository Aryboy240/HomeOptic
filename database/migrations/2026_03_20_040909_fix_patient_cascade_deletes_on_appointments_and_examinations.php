<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * appointments.patient_id and examinations.patient_id were created with
     * restrictOnDelete. Patient deletion is handled explicitly in PHP
     * (PatientController::destroy) for SQLite compatibility. On MySQL/Postgres
     * this migration also enforces cascade at the database level.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            // SQLite does not support dropping/re-adding FK constraints via ALTER TABLE.
            // Cascade deletion is handled explicitly in PatientController::destroy.
            return;
        }

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->foreign('patient_id')->references('id')->on('patients')->cascadeOnDelete();
        });

        Schema::table('examinations', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->foreign('patient_id')->references('id')->on('patients')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->foreign('patient_id')->references('id')->on('patients')->restrictOnDelete();
        });

        Schema::table('examinations', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->foreign('patient_id')->references('id')->on('patients')->restrictOnDelete();
        });
    }
};
