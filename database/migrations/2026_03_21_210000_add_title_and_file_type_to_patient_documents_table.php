<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_documents', function (Blueprint $table) {
            $table->string('title')->after('patient_id');
            $table->string('file_type')->default('pdf')->after('filename'); // 'pdf' or 'image'
        });
    }

    public function down(): void
    {
        Schema::table('patient_documents', function (Blueprint $table) {
            $table->dropColumn(['title', 'file_type']);
        });
    }
};
