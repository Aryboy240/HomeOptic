<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->string('exam_type');
            $table->date('examined_at');

            // The optometrist who conducted the exam
            $table->foreignId('staff_id')->constrained('users')->restrictOnDelete();

            // Signature fields — set when optometrist clicks "Click To Sign"
            $table->foreignId('signed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('signed_at')->nullable();

            // Shown in the patient examination history table
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('patient_id');
            $table->index('examined_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examinations');
    }
};
