<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')
                  ->constrained('categories')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->after('category_id')
                  ->constrained('users')->nullOnDelete();
            $table->boolean('featured')->default(false)->after('approval_status');
            $table->unsignedInteger('click_count')->default(0)->after('featured');
            $table->decimal('avg_rating', 2, 1)->default(0)->after('click_count');
        });
    }

    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['category_id', 'created_by', 'featured', 'click_count', 'avg_rating']);
        });
    }
};
