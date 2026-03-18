<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_investigative', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examination_id')->unique()->constrained()->cascadeOnDelete();

            // Drops section
            $table->boolean('drops_used')->default(false); // Tropicamide 1%
            $table->string('drops_detail_batch')->nullable();
            $table->date('drops_expiry')->nullable();
            $table->text('drops_more_info')->nullable();

            // Intraocular Pressure
            $table->string('pre_iop_r')->nullable();
            $table->string('pre_iop_l')->nullable();
            $table->string('post_iop_r')->nullable();
            $table->string('post_iop_l')->nullable();

            // Cover Test
            $table->string('ct_with_rx')->nullable();
            $table->string('ct_with_rx_near')->nullable();
            $table->text('ct_with_rx_near_notes')->nullable();
            $table->string('ct_without_rx')->nullable();
            $table->string('ct_without_rx_near')->nullable();
            $table->text('ct_without_rx_near_notes')->nullable();

            // Ocular Motility Balance — near
            $table->string('omb_near_h')->nullable();
            $table->string('omb_near_v')->nullable();

            // Visual Fields
            $table->string('visual_fields_r')->nullable();
            $table->string('visual_fields_l')->nullable();

            // Motility
            $table->string('motility')->nullable();

            // Amsler Grid
            $table->string('amsler_r')->nullable();
            $table->text('amsler_r_notes')->nullable();
            $table->string('amsler_l')->nullable();
            $table->text('amsler_l_notes')->nullable();

            // Ocular Motility Balance — distance
            $table->string('omb_h')->nullable();
            $table->string('omb_v')->nullable();

            // Keratometry
            $table->string('keratometry_r')->nullable();
            $table->string('keratometry_l')->nullable();

            // Additional measurements
            $table->string('npc')->nullable();                         // Near Point of Convergence
            $table->string('stereopsis')->nullable();
            $table->string('colour_vision')->nullable();
            $table->string('amplitude_of_accommodation')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_investigative');
    }
};
