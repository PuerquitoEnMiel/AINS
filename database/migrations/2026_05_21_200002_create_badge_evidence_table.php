<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badge_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            // Evidence file (stored in storage/app/public/badge-evidence/)
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable(); // pdf, jpg, png, etc.
            // Optional link to online certificate (e.g. Google credentials URL)
            $table->string('certificate_url')->nullable();
            // Notes from the teacher explaining or contextualizing the evidence
            $table->text('notes')->nullable();
            // Admin review
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable(); // Admin feedback
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            // Badge expiry (calculated from approval date + badge expires_in_days)
            $table->timestamp('expires_at')->nullable(); // null = permanent
            $table->timestamps();

            // One pending submission per user per badge at a time
            $table->unique(['user_id', 'badge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_evidence');
    }
};
