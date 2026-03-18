<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_ophthalmoscopy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examination_id')->unique()->constrained()->cascadeOnDelete();

            $table->text('ophthalmoscopy_notes')->nullable();

            // Right eye findings — all dropdown-selected values
            $table->string('right_pupils')->nullable();
            $table->string('right_lids_lashes')->nullable();
            $table->string('right_lashes')->nullable();
            $table->string('right_conjunc')->nullable();
            $table->string('right_cornea')->nullable();
            $table->string('right_sclera')->nullable();
            $table->string('right_ant_ch')->nullable();       // Anterior Chamber
            $table->string('right_media')->nullable();
            $table->string('right_cd')->nullable();           // Cup-to-Disc ratio
            $table->string('right_av')->nullable();           // Arteriovenous ratio
            $table->string('right_fundus_periphery')->nullable();
            $table->string('right_macular')->nullable();
            $table->string('right_ret_grading')->nullable();  // Retinal Grading

            // Left eye findings — mirror of right eye
            $table->string('left_pupils')->nullable();
            $table->string('left_lids_lashes')->nullable();
            $table->string('left_lashes')->nullable();
            $table->string('left_conjunc')->nullable();
            $table->string('left_cornea')->nullable();
            $table->string('left_sclera')->nullable();
            $table->string('left_ant_ch')->nullable();
            $table->string('left_media')->nullable();
            $table->string('left_cd')->nullable();
            $table->string('left_av')->nullable();
            $table->string('left_fundus_periphery')->nullable();
            $table->string('left_macular')->nullable();
            $table->string('left_ret_grading')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_ophthalmoscopy');
    }
};
