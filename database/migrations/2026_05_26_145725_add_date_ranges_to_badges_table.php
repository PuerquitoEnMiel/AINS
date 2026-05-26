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
        Schema::table('badges', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('evidence_instructions');
            $table->date('end_date')->nullable()->after('start_date');
            if (Schema::hasColumn('badges', 'expires_in_days')) {
                $table->dropColumn('expires_in_days');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->integer('expires_in_days')->nullable()->after('evidence_instructions');
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
};
