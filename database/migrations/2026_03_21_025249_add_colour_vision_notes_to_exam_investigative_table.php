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
        Schema::table('exam_investigative', function (Blueprint $table) {
            $table->text('colour_vision_notes')->nullable()->after('colour_vision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_investigative', function (Blueprint $table) {
            $table->dropColumn('colour_vision_notes');
        });
    }
};
