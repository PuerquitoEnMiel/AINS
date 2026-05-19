<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Expand role to support student
            // Current values: 'admin', 'teacher'. Adding 'student'.
            // Role column already exists as string, so just add new fields.
            $table->string('department')->nullable()->after('role');  // "Math", "Science", "IT"
            $table->text('bio')->nullable()->after('department');     // Short profile description
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['department', 'bio']);
        });
    }
};
