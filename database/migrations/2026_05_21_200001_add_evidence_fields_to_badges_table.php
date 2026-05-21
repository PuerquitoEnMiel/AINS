<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            // Evidence-based validation fields
            $table->boolean('requires_evidence')->default(false)->after('criteria_type');
            $table->string('certification_url')->nullable()->after('requires_evidence'); // Link to official cert page
            $table->string('evidence_instructions')->nullable()->after('certification_url'); // Instructions for uploading proof
            // Expiry configuration
            $table->integer('expires_in_days')->nullable()->after('evidence_instructions'); // null = permanent
        });
    }

    public function down(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->dropColumn(['requires_evidence', 'certification_url', 'evidence_instructions', 'expires_in_days']);
        });
    }
};
