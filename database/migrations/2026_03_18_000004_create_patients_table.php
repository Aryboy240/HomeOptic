<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('title');
            $table->string('first_name');
            $table->string('surname');

            // Address
            $table->string('address_line_1');
            $table->string('post_code');
            $table->string('town_city');
            $table->string('county')->nullable();
            $table->string('country')->nullable();

            // Contact
            $table->string('telephone_mobile')->nullable();
            $table->string('telephone_other')->nullable();
            $table->string('alt_contact_name')->nullable();
            $table->string('alt_tel_number')->nullable();
            $table->string('email')->nullable();

            // Demographics
            $table->string('sex_gender');
            $table->date('date_of_birth');
            $table->string('status')->default('active');

            // Practice & doctor associations
            $table->foreignId('practice_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('doctor_other')->nullable();

            // Clinical flags
            $table->boolean('has_glaucoma')->default(false);
            $table->boolean('is_diabetic')->default(false);
            $table->boolean('is_nhs')->default(false);
            $table->char('patient_type', 1);

            // Administrative
            $table->string('dropped_reason')->nullable();
            $table->string('how_heard')->nullable();
            $table->string('how_heard_other')->nullable();
            $table->foreignId('pct_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('domiciliary_reason')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes driven by the Find Patient search/sort options
            $table->index('surname');
            $table->index('post_code');
            $table->index('date_of_birth');
            $table->index('patient_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
