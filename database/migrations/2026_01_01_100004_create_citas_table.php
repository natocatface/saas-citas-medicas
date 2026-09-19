<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('medico_id')->constrained('medicos')->cascadeOnDelete();
            $table->date('fecha');
            $table->time('hora');
            $table->enum('estado', ['pendiente', 'confirmada', 'atendida', 'cancelada'])->default('pendiente');
            $table->string('motivo')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index(['fecha', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
