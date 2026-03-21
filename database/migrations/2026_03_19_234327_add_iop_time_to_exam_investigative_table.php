<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_investigative', function (Blueprint $table) {
            $table->string('pre_iop_time')->nullable()->after('pre_iop_l');
            $table->string('post_iop_time')->nullable()->after('post_iop_l');
        });
    }

    public function down(): void
    {
        Schema::table('exam_investigative', function (Blueprint $table) {
            $table->dropColumn(['pre_iop_time', 'post_iop_time']);
        });
    }
};
