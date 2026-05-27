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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique();
            $table->string('subject');
            $table->text('template');
            $table->timestamps();
        });

        // Seed defaults
        DB::table('notification_templates')->insert([
            [
                'type' => 'evidence',
                'subject' => 'Alerta AINS: Evidencia de Insignia subida',
                'template' => 'El docente {user} ha subido evidencia para la insignia "{badge}".',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'suggestion',
                'subject' => 'Alerta AINS: Nueva Sugerencia de Insignia',
                'template' => 'El docente {user} ha sugerido una nueva insignia: "{suggestion}".',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'security',
                'subject' => 'Alerta AINS: Intento de Prompt Injection detectado',
                'template' => 'El usuario {user} intentó bypass o hackear el chatbot.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
