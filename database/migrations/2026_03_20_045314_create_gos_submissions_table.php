<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gos_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->string('form_type');                         // GOS1, GOS3, GOS6
            $table->string('status')->default('unsubmitted');    // unsubmitted | awaiting_confirmation | accepted | rejected
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->decimal('voucher_value', 8, 2)->nullable();
            $table->string('batch_reference')->nullable();
            $table->json('form_data')->nullable();               // serialised field values from the filled form
            $table->timestamps();

            $table->index('patient_id');
            $table->index(['status', 'form_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gos_submissions');
    }
};
