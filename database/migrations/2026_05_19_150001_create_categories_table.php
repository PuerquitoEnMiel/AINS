<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // "AI Writing", "Productivity", etc.
            $table->string('slug')->unique();  // "ai-writing"
            $table->string('icon')->nullable(); // emoji or icon class
            $table->string('color', 7)->default('#007934'); // hex color
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
