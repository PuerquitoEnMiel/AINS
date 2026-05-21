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
        // deprecated: use up() instead. This method is intentionally named transition to avoid double execution if named up.
    }

    public function up(): void
    {
        Schema::create('task_force_members', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('name');
            $blueprint->string('role');
            $blueprint->string('email');
            $blueprint->text('description');
            $blueprint->string('initials', 3);
            $blueprint->string('avatar_color')->default('#007934');
            $blueprint->string('image_url')->nullable();
            $blueprint->integer('sort_order')->default(0);
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_force_members');
    }
};
