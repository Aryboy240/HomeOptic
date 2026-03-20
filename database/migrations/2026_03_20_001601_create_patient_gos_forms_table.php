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
        Schema::create('patient_gos_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('form_type');           // GOS1, GOS3, GOS6
            $table->boolean('is_eligible');         // auto-calculated
            $table->boolean('admin_override')->nullable(); // null = use auto
            $table->string('override_note')->nullable();
            $table->timestamps();

            $table->unique(['patient_id', 'form_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_gos_forms');
    }
};
