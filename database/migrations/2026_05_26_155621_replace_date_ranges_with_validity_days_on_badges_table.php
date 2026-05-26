<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Replace global start_date / end_date with per-badge validity_days.
     * Each teacher's individual expiry is calculated when admin approves:
     * expires_at = approved_at + validity_days
     */
    public function up(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            // New field: how many days the certification is valid (e.g. 1095 = 3 years)
            // null = permanent / never expires
            $table->integer('validity_days')->nullable()->after('evidence_instructions');

            // Drop the old global date range columns
            if (Schema::hasColumn('badges', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('badges', 'end_date')) {
                $table->dropColumn('end_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('evidence_instructions');
            $table->date('end_date')->nullable()->after('start_date');
            if (Schema::hasColumn('badges', 'validity_days')) {
                $table->dropColumn('validity_days');
            }
        });
    }
};
