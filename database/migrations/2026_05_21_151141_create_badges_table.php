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
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon')->nullable(); // emoji or URL string
            $table->string('color', 7)->default('#156050'); // hex color code
            $table->string('category')->default('tool_mastery'); // tool_mastery, ai_safety, pedagogy, platform
            $table->string('difficulty')->default('bronze'); // bronze, silver, gold
            $table->string('criteria_type')->default('quiz'); // quiz, usage, manual
            $table->json('criteria_config')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
