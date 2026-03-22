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
        Schema::table('pending_bookings', function (Blueprint $table) {
            $table->timestamp('reminder_sent_at')->nullable()->after('admin_decision_at');
        });
    }

    public function down(): void
    {
        Schema::table('pending_bookings', function (Blueprint $table) {
            $table->dropColumn('reminder_sent_at');
        });
    }
};
