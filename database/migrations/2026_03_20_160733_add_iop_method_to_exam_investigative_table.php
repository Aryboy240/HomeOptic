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
            $table->string('pre_iop_method')->nullable()->after('pre_iop_time');
            $table->string('post_iop_method')->nullable()->after('post_iop_time');
        });
    }

    public function down(): void
    {
        Schema::table('exam_investigative', function (Blueprint $table) {
            $table->dropColumn(['pre_iop_method', 'post_iop_method']);
        });
    }
};
