<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompt_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prompt_tip_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // 'upvote' or 'downvote'
            $table->timestamps();

            $table->unique(['user_id', 'prompt_tip_id']);
        });

        Schema::create('prompt_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prompt_tip_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompt_comments');
        Schema::dropIfExists('prompt_votes');
    }
};
