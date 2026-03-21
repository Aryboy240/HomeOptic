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
        Schema::table('exam_refraction', function (Blueprint $table) {
            $table->string('nhs_voucher_bifocal')->nullable()->after('nhs_voucher_near');
        });
    }

    public function down(): void
    {
        Schema::table('exam_refraction', function (Blueprint $table) {
            $table->dropColumn('nhs_voucher_bifocal');
        });
    }
};
