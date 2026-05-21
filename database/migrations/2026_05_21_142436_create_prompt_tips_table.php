<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function transition(): void
    {
        // deprecated: use up() instead.
    }

    public function up(): void
    {
        Schema::create('prompt_tips', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('title');
            $blueprint->string('target_role'); // 'docentes' or 'estudiantes'
            $blueprint->string('category');
            $blueprint->string('complexity'); // 'Baja', 'Media', 'Alta'
            $blueprint->text('description');
            $blueprint->text('prompt_text');
            $blueprint->integer('sort_order')->default(0);
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompt_tips');
    }
};
