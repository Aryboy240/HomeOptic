<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('diary_id')->constrained()->restrictOnDelete();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();

            $table->string('appointment_type');
            $table->string('appointment_status');

            $table->date('date');
            $table->time('start_time');
            $table->smallInteger('length_minutes');

            $table->text('display_text')->nullable();

            // Null unless the appointment has been cancelled
            $table->timestamp('cancelled_at')->nullable();
            // Set when "Update & Notify" is triggered
            $table->timestamp('notified_at')->nullable();

            $table->timestamps();

            // Primary calendar query: all appointments for a diary in a date range
            $table->index(['diary_id', 'date']);
            $table->index('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
