<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('summary');
            $table->json('pros');
            $table->json('cons');
            $table->json('best_for_grades');
            $table->json('best_use_cases');
            $table->timestamp('generated_at');
            $table->integer('review_count_at_generation')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_insights');
    }
};
