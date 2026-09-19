<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lista_espera', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('especialidad_id')->nullable()->constrained('especialidades')->nullOnDelete();
            $table->foreignId('medico_id')->nullable()->constrained('medicos')->nullOnDelete();
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->enum('estado', ['esperando', 'llamado', 'atendido', 'cancelado'])->default('esperando');
            $table->string('motivo')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->index(['estado', 'prioridad']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lista_espera');
    }
};
