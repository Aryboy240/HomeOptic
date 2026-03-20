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
        Schema::table('patients', function (Blueprint $table) {
            // Medical
            $table->boolean('is_blind_partially_sighted')->default(false)->after('is_nhs');
            $table->boolean('has_hearing_impairment')->default(false)->after('is_blind_partially_sighted');
            $table->boolean('has_retinitis_pigmentosa')->default(false)->after('has_hearing_impairment');
            $table->text('physical_disabilities')->nullable()->after('has_retinitis_pigmentosa');
            $table->text('mental_health_conditions')->nullable()->after('physical_disabilities');

            // Social
            $table->boolean('in_full_time_education')->default(false)->after('mental_health_conditions');
            $table->json('benefits')->nullable()->after('in_full_time_education');
            $table->string('next_of_kin_name')->nullable()->after('benefits');
            $table->string('next_of_kin_relationship')->nullable()->after('next_of_kin_name');
            $table->string('next_of_kin_phone')->nullable()->after('next_of_kin_relationship');
            $table->string('emergency_contact_name')->nullable()->after('next_of_kin_phone');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('carer_name')->nullable()->after('emergency_contact_phone');
            $table->string('carer_phone')->nullable()->after('carer_name');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'is_blind_partially_sighted', 'has_hearing_impairment', 'has_retinitis_pigmentosa',
                'physical_disabilities', 'mental_health_conditions',
                'in_full_time_education', 'benefits',
                'next_of_kin_name', 'next_of_kin_relationship', 'next_of_kin_phone',
                'emergency_contact_name', 'emergency_contact_phone',
                'carer_name', 'carer_phone',
            ]);
        });
    }
};
