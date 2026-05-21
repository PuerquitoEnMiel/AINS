<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prompt_tips', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_community')->default(false);
            $table->boolean('is_approved')->default(true); // Default true for legacy prompts, false for community-submitted ones
            $table->integer('usage_count')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('prompt_tips', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'is_community', 'is_approved', 'usage_count']);
        });
    }
};
