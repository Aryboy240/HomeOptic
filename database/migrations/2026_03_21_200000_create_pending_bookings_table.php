<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('appointment_type');
            $table->text('reason')->nullable();
            $table->text('examiner_notes')->nullable();
            $table->string('status')->default('pending'); // pending, approved, declined
            $table->timestamp('admin_decision_at')->nullable();
            $table->foreignId('admin_decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_email');
            $table->json('patient_form_data');
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_bookings');
    }
};
