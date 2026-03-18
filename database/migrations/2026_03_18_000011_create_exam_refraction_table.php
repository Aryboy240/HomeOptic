<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_refraction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examination_id')->unique()->constrained()->cascadeOnDelete();

            // -------------------------------------------------------------------------
            // Current / Previous Rx 1 — Right Eye
            // -------------------------------------------------------------------------
            $table->decimal('current_r_sph', 5, 2)->nullable();
            $table->decimal('current_r_cyl', 5, 2)->nullable();
            $table->smallInteger('current_r_axis')->nullable();
            $table->decimal('current_r_prism', 4, 2)->nullable();
            $table->string('current_r_prism_dir')->nullable();
            $table->decimal('current_r_add', 4, 2)->nullable();
            $table->string('current_r_va')->nullable();

            // Current / Previous Rx 1 — Left Eye
            $table->decimal('current_l_sph', 5, 2)->nullable();
            $table->decimal('current_l_cyl', 5, 2)->nullable();
            $table->smallInteger('current_l_axis')->nullable();
            $table->decimal('current_l_prism', 4, 2)->nullable();
            $table->string('current_l_prism_dir')->nullable();
            $table->decimal('current_l_add', 4, 2)->nullable();
            $table->string('current_l_va')->nullable();

            // Current Rx — Additional Fields
            $table->decimal('current_pd_r', 4, 1)->nullable();
            $table->decimal('current_pd_l', 4, 1)->nullable();
            $table->decimal('current_bvd', 4, 1)->nullable();
            $table->string('current_bin_bcva')->nullable();
            $table->text('current_comments')->nullable(); // date/location of previous test

            // -------------------------------------------------------------------------
            // Previous Rx Other (incl. Autorefractor) — Right Eye
            // -------------------------------------------------------------------------
            $table->decimal('prev_other_r_sph', 5, 2)->nullable();
            $table->decimal('prev_other_r_cyl', 5, 2)->nullable();
            $table->smallInteger('prev_other_r_axis')->nullable();
            $table->decimal('prev_other_r_prism', 4, 2)->nullable();
            $table->string('prev_other_r_prism_dir')->nullable();
            $table->decimal('prev_other_r_add', 4, 2)->nullable();
            $table->string('prev_other_r_va')->nullable();

            // Previous Rx Other — Left Eye
            $table->decimal('prev_other_l_sph', 5, 2)->nullable();
            $table->decimal('prev_other_l_cyl', 5, 2)->nullable();
            $table->smallInteger('prev_other_l_axis')->nullable();
            $table->decimal('prev_other_l_prism', 4, 2)->nullable();
            $table->string('prev_other_l_prism_dir')->nullable();
            $table->decimal('prev_other_l_add', 4, 2)->nullable();
            $table->string('prev_other_l_va')->nullable();

            // -------------------------------------------------------------------------
            // Retinoscopy
            // -------------------------------------------------------------------------
            $table->text('retino_r_value')->nullable(); // double-click sets default in UI
            $table->text('retino_l_value')->nullable();

            // -------------------------------------------------------------------------
            // Rx Subjective — Right Eye
            // -------------------------------------------------------------------------

            // Distance
            $table->string('subj_r_uav')->nullable();   // Unaided Visual Acuity
            $table->decimal('subj_r_sph', 5, 2)->nullable();
            $table->decimal('subj_r_cyl', 5, 2)->nullable();
            $table->smallInteger('subj_r_axis')->nullable();
            $table->decimal('subj_r_prism', 4, 2)->nullable();
            $table->string('subj_r_prism_dir')->nullable();
            $table->string('subj_r_va')->nullable();

            // Near Add
            $table->decimal('subj_r_near_add', 4, 2)->nullable();
            $table->decimal('subj_r_near_prism', 4, 2)->nullable();
            $table->string('subj_r_near_prism_dir')->nullable();
            $table->string('subj_r_near_acuity')->nullable();

            // Intermediate Add
            $table->decimal('subj_r_int_add', 4, 2)->nullable();
            $table->decimal('subj_r_int_prism', 4, 2)->nullable();
            $table->string('subj_r_int_prism_dir')->nullable();
            $table->string('subj_r_int_acuity')->nullable();

            // -------------------------------------------------------------------------
            // Rx Subjective — Left Eye
            // -------------------------------------------------------------------------

            // Distance
            $table->string('subj_l_uav')->nullable();
            $table->decimal('subj_l_sph', 5, 2)->nullable();
            $table->decimal('subj_l_cyl', 5, 2)->nullable();
            $table->smallInteger('subj_l_axis')->nullable();
            $table->decimal('subj_l_prism', 4, 2)->nullable();
            $table->string('subj_l_prism_dir')->nullable();
            $table->string('subj_l_va')->nullable();

            // Near Add
            $table->decimal('subj_l_near_add', 4, 2)->nullable();
            $table->decimal('subj_l_near_prism', 4, 2)->nullable();
            $table->string('subj_l_near_prism_dir')->nullable();
            $table->string('subj_l_near_acuity')->nullable();

            // Intermediate Add
            $table->decimal('subj_l_int_add', 4, 2)->nullable();
            $table->decimal('subj_l_int_prism', 4, 2)->nullable();
            $table->string('subj_l_int_prism_dir')->nullable();
            $table->string('subj_l_int_acuity')->nullable();

            // -------------------------------------------------------------------------
            // Subjective PD & Additional Measurements
            // -------------------------------------------------------------------------
            $table->decimal('subj_pd_r', 4, 1)->nullable();
            $table->decimal('subj_pd_l', 4, 1)->nullable();
            $table->decimal('subj_pd_combined', 4, 1)->nullable();
            $table->decimal('subj_bvd', 4, 1)->nullable();
            $table->string('subj_bin_bcva')->nullable();
            $table->text('subj_notes')->nullable();

            // -------------------------------------------------------------------------
            // Outcome (radio buttons — single selection)
            // -------------------------------------------------------------------------
            $table->string('outcome')->nullable();

            // -------------------------------------------------------------------------
            // Recommendations (checkboxes)
            // -------------------------------------------------------------------------
            $table->boolean('rec_distance')->default(false);
            $table->boolean('rec_near')->default(false);
            $table->boolean('rec_intermediate')->default(false);
            $table->boolean('rec_high_index')->default(false);
            $table->boolean('rec_bifocals')->default(false);
            $table->boolean('rec_varifocals')->default(false);
            $table->boolean('rec_occupational')->default(false);
            $table->boolean('rec_min_sub')->default(false);
            $table->boolean('rec_photochromic')->default(false);
            $table->boolean('rec_hardcoat')->default(false);
            $table->boolean('rec_tint')->default(false);
            $table->boolean('rec_mar')->default(false);

            // -------------------------------------------------------------------------
            // NHS Voucher
            // -------------------------------------------------------------------------
            $table->string('nhs_voucher_dist')->nullable();
            $table->string('nhs_voucher_near')->nullable();

            // -------------------------------------------------------------------------
            // Examination Comment & Retest
            // -------------------------------------------------------------------------
            $table->text('examination_comment')->nullable();
            $table->string('retest_after')->nullable();         // e.g. "1 Year"
            $table->char('retest_patient_type', 1)->nullable(); // PatientType enum value

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_refraction');
    }
};
