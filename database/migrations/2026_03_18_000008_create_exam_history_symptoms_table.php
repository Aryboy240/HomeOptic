<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_history_symptoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examination_id')->unique()->constrained()->cascadeOnDelete();

            // GOS section
            $table->string('gos_eligibility');
            $table->string('gos_establishment_name')->nullable();
            $table->string('gos_establishment_town')->nullable();

            // Last examination date section
            $table->boolean('last_exam_first')->default(false);
            $table->boolean('last_exam_not_known')->default(false);
            $table->date('last_exam_date')->nullable();

            // History free-text fields (all have a "select default" button in the UI)
            $table->text('reason_for_visit')->nullable();
            $table->text('poh')->nullable();   // Past Ocular History
            $table->text('gh')->nullable();    // General Health
            $table->text('medication_notes')->nullable();
            $table->text('fh')->nullable();    // Family History
            $table->text('foh')->nullable();   // Family Ocular History

            // Medication checkboxes stored as JSON array of medication name strings.
            // PROTOTYPE ONLY — normalise to a pivot table before production.
            $table->json('medications')->nullable();

            $table->text('other_notes')->nullable();

            // Patient information snapshot at time of exam
            $table->boolean('has_glaucoma')->default(false);
            $table->boolean('has_fhg')->default(false);    // Family History of Glaucoma
            $table->boolean('is_diabetic')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_history_symptoms');
    }
};
