<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('type', ['link', 'book', 'video', 'article', 'tool', 'course']);
            $table->enum('area', [
                'stem', 'innovation', 'ai', 'robotics',
                'design', 'programming', 'math', 'science', 'general',
            ]);
            $table->json('target_roles')->default('["all"]'); // ['student','teacher','all']
            $table->string('thumbnail_url')->nullable();
            $table->string('author')->nullable();
            $table->string('source')->nullable();   // e.g. "MIT OpenCourseWare", "Khan Academy"
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
